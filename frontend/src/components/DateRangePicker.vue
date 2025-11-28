<template>
  <div>
    <label>Start:</label>
    <input type="date" v-model="start" @change="emitRange" />

    <label>End:</label>
    <input type="date" v-model="end" @change="emitRange" />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { getDateRange } from "../services/weatherApi";

const start = ref("");
const end = ref("");

const emit = defineEmits(["update:range"]);

const emitRange = () => {
  emit("update:range", { start: start.value, end: end.value });
};

onMounted(async () => {
  const meta = await getDateRange();
  start.value = meta.default_start;
  end.value = meta.default_end;
  emitRange();
});
</script>
