<template>
  <div :class="['drp', dark ? 'dark' : '']">
    <label>Start:</label>
    <input
      type="date"
      v-model="localStart"
      :min="min"
      :max="max"
      @change="onStartChange"
    />

    <label>End:</label>
    <input
      type="date"
      v-model="localEnd"
      :min="localStart"
      :max="max"
      @change="onEndChange"
    />
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  start: String,
  end: String,
  min: String,
  max: String,
  dark: Boolean
});

const emit = defineEmits(["update:range"]);

const localStart = ref(props.start);
const localEnd = ref(props.end);

// -----------------------------
// Handle START change
// -----------------------------
function onStartChange() {
  // Jika start > end → end dipaksa sama start
  if (localEnd.value < localStart.value) {
    localEnd.value = localStart.value;
  }

  emit("update:range", {
    start: localStart.value,
    end: localEnd.value
  });
}

// -----------------------------
// Handle END change
// -----------------------------
function onEndChange() {
  // Jika end < start → end dipaksa sama start
  if (localEnd.value < localStart.value) {
    localEnd.value = localStart.value;
  }

  emit("update:range", {
    start: localStart.value,
    end: localEnd.value
  });
}

// Sinkronisasi jika Dashboard update default 30 days
watch(() => props.start, v => (localStart.value = v));
watch(() => props.end, v => (localEnd.value = v));
</script>

<style>
.drp {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px;
}

/* ---------- LIGHT MODE ---------- */
.drp input[type="date"] {
  background: white !important;
  color: black !important;
  border: 1px solid #ccc !important;
  padding: 4px 6px;
}

/* Force black text (Chrome Windows override) */
.drp input[type="date"]::-webkit-datetime-edit,
.drp input[type="date"]::-webkit-datetime-edit-text,
.drp input[type="date"]::-webkit-datetime-edit-month-field,
.drp input[type="date"]::-webkit-datetime-edit-day-field,
.drp input[type="date"]::-webkit-datetime-edit-year-field {
  color: black !important;
}


/* ---------- DARK MODE ---------- */
.drp.dark label {
  color: #eee;
}

.drp.dark input[type="date"] {
  background: #333 !important;
  color: #eee !important;
  border: 1px solid #555 !important;
}

/* Force white text */
.drp.dark input[type="date"]::-webkit-datetime-edit,
.drp.dark input[type="date"]::-webkit-datetime-edit-text,
.drp.dark input[type="date"]::-webkit-datetime-edit-month-field,
.drp.dark input[type="date"]::-webkit-datetime-edit-day-field,
.drp.dark input[type="date"]::-webkit-datetime-edit-year-field {
  color: #eee !important;
}

/* Kalender icon */
.drp.dark input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(1);
}
</style>
