<script setup>
import { ref, onMounted } from "vue";
import CountrySelector from "../components/CountrySelector.vue";
import WeatherChart from "../components/WeatherChart.vue";
import { getCountries, getDateRange, fetchWeather } from "../services/weatherApi";

const countries = ref([]);
const selectedCountries = ref([]);
const minDate = ref("");
const maxDate = ref("");

const start = ref("");
const end = ref("");

const labels = ref([]);

const charts = ref([
  { key: "avg_temp", label: "Average Temp", datasets: [] },
  { key: "min_temp", label: "Min Temp", datasets: [] },
  { key: "max_temp", label: "Max Temp", datasets: [] },
  { key: "avg_precip", label: "Avg Precip", datasets: [] },
  { key: "avg_snowfall", label: "Avg Snowfall", datasets: [] },
  { key: "avg_snowdepth", label: "Avg Snow Depth", datasets: [] },
  { key: "avg_wind", label: "Avg Wind", datasets: [] },
  { key: "max_wind", label: "Max Wind", datasets: [] }
]);

onMounted(async () => {
  const result = await getCountries();
  countries.value = result;

  const range = await getDateRange();
  minDate.value = range.min;
  maxDate.value = range.max;

  // default 30d
  const dmax = new Date(range.max);
  const dmin = new Date(dmax.getTime() - 30 * 86400 * 1000);

  start.value = dmin.toISOString().slice(0, 10);
  end.value = range.max;
});

async function loadData() {
  const selected = selectedCountries.value;

  if (selected.length === 0) return;

  const data = await fetchWeather(start.value, end.value, selected);

  labels.value = [...new Set(data.map(d => d.date))];

  charts.value.forEach((chart) => {
    chart.datasets = selected.map((code, idx) => {
      const rows = data.filter(r => r.country_code === code);

      return {
        label: code,
        borderColor: `hsl(${(idx * 50) % 360}, 70%, 50%)`,
        data: rows.map(r => r[chart.key] ?? 0),
        tension: 0.2
      };
    });
  });
}
</script>

<template>
  <div style="padding:20px">
    <h1>NOAA Drag & Drop Dashboard</h1>

    <div>
      Start:
      <input type="date" v-model="start" :min="minDate" :max="maxDate">

      End:
      <input type="date" v-model="end" :min="minDate" :max="maxDate">

      Countries:
      <CountrySelector
        :countries="countries"
        @update:selected="selectedCountries = $event"
      />

      <button @click="loadData">Reload</button>
    </div>

    <div class="grid">
      <div v-for="chart in charts" :key="chart.key" class="chart-box">
        <h3>{{ chart.label }}</h3>
        <WeatherChart :labels="labels" :datasets="chart.datasets"/>
      </div>
    </div>
  </div>
</template>

<style>
.grid {
  display: grid;
  grid-template-columns: repeat(2, 50%);
}
.chart-box {
  padding: 10px;
  border: 1px solid #ccc;
  margin: 6px;
}
</style>