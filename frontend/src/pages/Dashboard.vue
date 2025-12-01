<script setup>
import { ref, onMounted } from "vue";
import CountrySelector from "../components/CountrySelector.vue";
import DateRangePicker from "../components/DateRangePicker.vue";
import WeatherChart from "../components/WeatherChart.vue";
import { getCountries, getDateRange, fetchWeather } from "../services/weatherApi";

const dark = ref(false);

function toggleDark() {
  dark.value = !dark.value;
  if (dark.value) document.body.classList.add("dark");
  else document.body.classList.remove("dark");
  window.dispatchEvent(new Event("theme-changed"));
}

const countries = ref([]);
const selectedCountries = ref([]);

const start = ref("");
const end = ref("");
const minDate = ref("");
const maxDate = ref("");

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

  // Default 30 days from max date
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

  const nameMap = {};
  countries.value.forEach(c => (nameMap[c.country_code] = c.country_name));

  charts.value.forEach(chart => {
    chart.datasets = selected.map((code, idx) => {
      const rows = data.filter(r => r.country_code === code);
      return {
        label: nameMap[code] || code,
        borderColor: `hsl(${(idx * 50) % 360}, 70%, 50%)`,
        data: rows.map(r => r[chart.key] ?? 0),
        tension: 0.25
      };
    });
  });
}
</script>

<template>
  <div :class="['page', dark ? 'dark' : '']">

    <div class="header">
      <h1>NOAA Climate Dashboard</h1>

      <button class="darkbtn" @click="toggleDark">
        {{ dark ? "☀ Light Mode" : "🌙 Dark Mode" }}
      </button>
    </div>

    <div class="controls">

      <!-- DATE RANGE FIXED -->
      <DateRangePicker
        :dark="dark"
        :min="minDate"
        :max="maxDate"
        :start="start"
        :end="end"
        @update:range="(r) => { start = r.start; end = r.end }"
      />

      <!-- COUNTRY SELECTOR -->
      <CountrySelector
        :countries="countries"
        :dark="dark"
        @update:selected="selectedCountries = $event"
      />

      <button class="submitbtn" @click="loadData">Submit</button>
    </div>

    <!-- CHARTS -->
    <div class="grid">
      <div v-for="chart in charts" :key="chart.key" class="chart-box">
        <h3>{{ chart.label }}</h3>
        <WeatherChart :labels="labels" :datasets="chart.datasets" />
      </div>
    </div>

  </div>
</template>

<style>
.page {
  padding: 20px;
  background: #fafafa;
  color: #222;
  min-height: 100vh;
  transition: 0.25s;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.controls {
  margin-bottom: 20px;
}

.darkbtn,
.submitbtn {
  padding: 6px 12px;
  background: white;
  border: 1px solid #bbb;
  cursor: pointer;
  margin-left: 10px;
}

.grid {
  display: grid;
  grid-template-columns: repeat(2, 50%);
}

.chart-box {
  padding: 10px;
  border: 1px solid #ccc;
  background: white;
  margin: 6px;
  transition: 0.25s;
}

.page.dark {
  background: #1e1e1e;
  color: #ddd;
}

.page.dark .chart-box {
  background: #2a2a2a;
  border-color: #444;
  color: white;
}

.page.dark .darkbtn,
.page.dark .submitbtn {
  background: #333;
  color: white;
  border-color: #666;
}
</style>