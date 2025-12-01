<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from "vue";
import {
  Chart,
  LineController,
  LineElement,
  PointElement,
  LinearScale,
  Title,
  CategoryScale,
  Legend,
  Tooltip
} from "chart.js";

Chart.register(LineController, LineElement, PointElement, LinearScale, Title, CategoryScale, Legend, Tooltip);

const props = defineProps({
  labels: Array,
  datasets: Array,
  title: String
});

const canvas = ref();
let chart;

/* --------------------------------------------
   🔥 Helper: check dark mode from <body> class
---------------------------------------------*/
function isDark() {
  return document.body.classList.contains("dark");
}

function render() {
  if (chart) chart.destroy();

  const dark = isDark();

  const textColor = dark ? "#e6e6e6" : "#222";
  const gridColor = dark ? "rgba(255,255,255,0.12)" : "rgba(0,0,0,0.1)";
  const tooltipBg = dark ? "#333" : "#fff";
  const tooltipColor = dark ? "#fff" : "#000";

  chart = new Chart(canvas.value, {
    type: "line",
    data: {
      labels: props.labels,
      datasets: props.datasets
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,

      // 🔥 ROUNDED LINES (TIDAK DIUBAH — tetap)
      elements: {
        line: {
          tension: 1
        },
        point: {
          radius: 3,
          hitRadius: 8
        }
      },

      plugins: {
        title: { display: false },

        tooltip: {
          enabled: true,
          backgroundColor: tooltipBg,
          titleColor: tooltipColor,
          bodyColor: tooltipColor,
          borderWidth: 0,
          mode: "nearest",
          intersect: false,
          callbacks: {
            label: ({ dataset, raw }) => `${dataset.label}: ${raw}`
          }
        },

        legend: {
          position: "top",
          labels: {
            color: textColor,
            padding: 15,
            boxWidth: 18
          }
        }
      },

      hover: {
        mode: "nearest",
        intersect: false
      },

      scales: {
        y: {
          beginAtZero: false,
          ticks: { padding: 6, color: textColor },
          grid: { color: gridColor }
        },
        x: {
          ticks: { maxRotation: 45, minRotation: 45, color: textColor },
          grid: { color: gridColor }
        }
      }
    }
  });
}

/* --------------------------------------------
   🔥 Re-render when data or theme changes
---------------------------------------------*/
watch(() => [props.labels, props.datasets], render, { deep: true });

// Saat darkmode toggle → re-render chart
function themeListener() {
  render();
}

onMounted(() => {
  render();
  window.addEventListener("theme-changed", themeListener);
});

onBeforeUnmount(() => {
  window.removeEventListener("theme-changed", themeListener);
});
</script>

<template>
  <div style="position:relative;height:300px;">
    <canvas ref="canvas"></canvas>
  </div>
</template>
