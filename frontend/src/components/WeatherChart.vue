<template>
  <div>
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import {
  Chart,
  LineController,
  LineElement,
  PointElement,
  LinearScale,
  Title,
  CategoryScale,
  Legend
} from "chart.js";

Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend);

const props = defineProps({
  labels: Array,
  datasets: Array,
  title: String
});

const canvas = ref();
let chart;

function render() {
  if (chart) chart.destroy();
  chart = new Chart(canvas.value, {
    type: "line",
    data: { labels: props.labels, datasets: props.datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { title: { display: true, text: props.title } }
    }
  });
}

watch(() => [props.labels, props.datasets], render, { deep: true });
onMounted(render);
</script>

<style scoped>
div {
  position: relative;
  height: 300px;
}
</style>
