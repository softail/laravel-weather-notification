<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import Location from '@/Components/Location.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, provide, ref } from 'vue';

const props = defineProps({
  locations: Object,
  notificationTypes: Array,
});

provide('notificationTypes', props.notificationTypes);

const newLocation = ref('');

const canAddLocation = computed(() => {
  return !newLocation.value || newLocation.value.length < 3;
});

const handleAddNewLocation = () => {
  axios
    .post(route('locations.store'), { name: newLocation.value })
    .then((response) => {
      newLocation.value = '';
      router.reload({ only: ['locations'] });
    });
};
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2
        class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
      >
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl max-w-[800px] sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
          <div
            class="flex flex-col justify-center p-6 text-gray-900 dark:text-gray-100"
          >
            <div class="max-h-[50vh] overflow-y-auto">
              <Location
                v-for="location in locations"
                :key="location.id"
                :location="location"
              />
            </div>

            <hr
              class="my-4 h-0.5 rounded-full border-0 bg-gradient-to-r from-gray-800 via-gray-50 to-gray-800"
            />

            <InputLabel value="Enter New Location" />

            <TextInput
              v-model="newLocation"
              class="w-full rounded-md border text-lg text-black"
              placeholder="London"
            />

            <div class="mt-4 flex flex-row justify-end">
              <button
                :disabled="canAddLocation"
                @click="handleAddNewLocation"
                class="right inline-block max-w-[180px] rounded-md bg-green-700 px-4 py-2 text-lg text-white transition hover:bg-green-500 disabled:bg-gray-500"
              >
                Add
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
