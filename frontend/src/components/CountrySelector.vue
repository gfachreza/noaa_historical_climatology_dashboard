<template>
  <div class="country-selector">
    <div class="dropdown" @click="toggleDropdown">
      {{ selectedLabels }}
      <span class="arrow">▼</span>
    </div>

    <div class="dropdown-menu" v-if="open">
      <!-- Select All -->
      <label class="item all">
        <input type="checkbox" v-model="selectAll" @change="toggleAll">
        <strong>Select All Countries</strong>
      </label>
      <hr>

      <!-- Countries -->
      <label
        class="item"
        v-for="c in countries"
        :key="c.country_code"
      >
        <input
          type="checkbox"
          :value="c.country_code"
          v-model="selectedLocal"
          @change="updateSelection"
        >
        {{ c.country_name }}
      </label>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
  countries: { type: Array, required: true }
});

const emit = defineEmits(["update:selected"]);

const open = ref(false);
const selectedLocal = ref([]);     // <-- TIDAK ADA SELECT ALL DI SINI
const selectAll = ref(false);

// buka tutup dropdown
function toggleDropdown() {
  open.value = !open.value;
}

// Label dropdown
const selectedLabels = computed(() => {
  if (selectedLocal.value.length === 0) return "0 selected";
  return `${selectedLocal.value.length} selected`;
});

// Klik checkbox individual
function updateSelection() {
  // Kalau uncheck 1 → selectAll = false
  if (selectedLocal.value.length !== props.countries.length) {
    selectAll.value = false;
  }
  // Kalau semua sudah ke-check 1 per 1 → selectAll = true
  if (selectedLocal.value.length === props.countries.length) {
    selectAll.value = true;
  }

  emit("update:selected", selectedLocal.value);
}

// Klik "Select All"
function toggleAll() {
  if (selectAll.value) {
    selectedLocal.value = props.countries.map(c => c.country_code);
  } else {
    selectedLocal.value = [];
  }
  emit("update:selected", selectedLocal.value);
}
</script>

<style>
.country-selector {
  position: relative;
  width: 250px;
  font-size: 14px;
}

.dropdown {
  border: 1px solid #ccc;
  padding: 6px;
  cursor: pointer;
  background: white;
}

.arrow {
  float: right;
}

.dropdown-menu {
  position: absolute;
  width: 100%;
  max-height: 250px;
  overflow-y: auto;
  border: 1px solid #aaa;
  background: white;
  z-index: 9999;
  padding: 5px;
}

.item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 3px 2px;
}

.all {
  background: #f1f1f1;
  padding: 4px;
}
</style>