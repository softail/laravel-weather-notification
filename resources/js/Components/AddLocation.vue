<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

interface Coordinates {
  lat: number | null;
  lon: number | null;
}

interface Suggestion {
  name: string;
  lat: string;
  lon: string;
}

const newLocation = ref<string>('');
const errorMessage = ref<string>('');
const suggestions = ref<Suggestion[]>([]);
const showDropdown = ref<boolean>(false);
const coordinates = ref<Coordinates>({ lat: null, lon: null });
let debounceTimeout: ReturnType<typeof setTimeout> | undefined;

const canAddLocation = computed(() => {
  return !newLocation.value || newLocation.value.length < 3;
});

const emit = defineEmits(['success']);

const handleAddNewLocation = () => {
  axios
    .post(route('locations.store'), {
      name: newLocation.value,
      coordinates: coordinates.value,
    })
    .then((response) => {
      emit('success');
      newLocation.value = '';
      router.reload({ only: ['locations'] });
    })
    .catch((error) => {
      errorMessage.value = error.response.data.message;
    });
};

const fetchTowns = async (query: string) => {
  if (!query) {
    suggestions.value = [];
    return;
  }

  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?format=json&q=${query}&addressdetails=1`,
    );
    const data = await response.json();

    suggestions.value = data
      .map((place: { display_name: string; lat: string; lon: string }) => ({
        name: place.display_name,
        lat: place.lat,
        lon: place.lon,
      }))
      .slice(0, 5);
  } catch (error) {
    console.error('Error fetching town names:', error);
  }
};

watch(newLocation, (newVal) => {
  clearTimeout(debounceTimeout);

  errorMessage.value = '';

  debounceTimeout = setTimeout(() => {
    if (newVal.length > 2) {
      fetchTowns(newVal);
      showDropdown.value = true;
    } else {
      suggestions.value = [];
      showDropdown.value = false;
    }
  }, 400);
});

const closeDropdown = () => {
  setTimeout(() => {
    showDropdown.value = false;
  }, 500);
};

const selectTown = (town: Suggestion) => {
  newLocation.value = town.name;
  coordinates.value = { lat: parseFloat(town.lat), lon: parseFloat(town.lon) };
  showDropdown.value = false;
};
</script>

<template>
  <InputLabel value="Enter New Location" />

  <div class="relative">
    <TextInput
      v-model="newLocation"
      placeholder="Enter town name..."
      @focus="showDropdown = true"
      @blur="closeDropdown"
      class="w-full rounded-md border text-lg text-black"
    />

    <InputError :message="errorMessage" />

    <ul
      v-if="showDropdown && suggestions.length"
      class="absolute mt-2 w-full rounded-md bg-gray-200 dark:bg-gray-600 text-gray-700 opacity-95 dark:text-gray-300"
    >
      <li
        v-for="(town, index) in suggestions"
        :key="index"
        @click="selectTown(town)"
        class="cursor-pointer p-2 hover:bg-gray-300 hover:dark:bg-gray-700"
      >
        {{ town.name }}
      </li>
    </ul>
  </div>

  <div class="mt-4 flex flex-row justify-end">
    <button
      :disabled="canAddLocation"
      @click="handleAddNewLocation"
      class="right inline-block max-w-[180px] rounded-md bg-green-400 px-4 py-2 text-lg text-white transition hover:bg-green-500 disabled:bg-gray-500 dark:bg-green-700"
    >
      Add
    </button>
  </div>
</template>
