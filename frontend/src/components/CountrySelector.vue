<template>
  <div class="country-selector" :class="{ dark: isDark }">
    <div class="dropdown" @click="toggleDropdown">
      {{ selectedLabels }}
      <span class="arrow">▼</span>
    </div>

    <div class="dropdown-menu" v-if="open">

      <!-- Search -->
      <input
        v-model="search"
        type="text"
        placeholder="Search country..."
        class="search-box"
        @click.stop
      />

      <!-- Select All -->
      <label class="item all">
        <input type="checkbox" v-model="selectAll" @change="toggleAll">
        <strong>Select All Countries</strong>
      </label>

      <hr />

      <!-- Filtered Countries -->
      <label
        class="item"
        v-for="c in filteredCountries"
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
import { ref, computed } from "vue";

const props = defineProps({
  countries: { type: Array, required: true },
  dark: { type: Boolean, default: false }
});

const emit = defineEmits(["update:selected"]);

const open = ref(false);
const search = ref("");

const selectedLocal = ref([]);
const selectAll = ref(false);

const isDark = computed(() => props.dark);

// buka tutup dropdown
function toggleDropdown() {
  open.value = !open.value;
}

// Label dropdown
const selectedLabels = computed(() => {
  if (selectedLocal.value.length === 0) return "0 selected";
  return `${selectedLocal.value.length} selected`;
});

// Filtering
const filteredCountries = computed(() => {
  if (!search.value) return props.countries;
  return props.countries.filter(c =>
    c.country_name.toLowerCase().includes(search.value.toLowerCase())
  );
});

// update select
function updateSelection() {
  selectAll.value = selectedLocal.value.length === props.countries.length;
  emit("update:selected", selectedLocal.value);
}

// Select All
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
  color: #222;
}

.country-selector.dark .dropdown {
  background: #2a2a2a;
  border-color: #555;
  color: #eee;
}

.arrow {
  float: right;
}

.dropdown-menu {
  position: absolute;
  width: 100%;
  max-height: 260px;
  overflow-y: auto;
  border: 1px solid #aaa;
  background: white;
  z-index: 9999;
  padding: 5px;
  color: #222;
}

.country-selector.dark .dropdown-menu {
  background: #2b2b2b;
  border-color: #555;
  color: #eee;
}

.search-box {
  width: 100%;
  padding: 6px;
  margin-bottom: 6px;
  box-sizing: border-box;
  border: 1px solid #ccc;
}

.country-selector.dark .search-box {
  background: #1f1f1f;
  border-color: #666;
  color: #eee;
}

.item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 3px 2px;
}

.all {
  background: #f3f3f3;
  padding: 4px;
}

.country-selector.dark .all {
  background: #3a3a3a;
}
</style>
