<script setup lang="ts">
import AddLocation from '@/Components/AddLocation.vue';
import Location from '@/Components/Location.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { provide } from 'vue';

const props = defineProps({
  locations: Object,
  notificationTypes: Array,
});

provide('notificationTypes', props.notificationTypes);
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
      <div class="mx-auto max-w-7xl max-w-[900px] sm:px-6 lg:px-8">
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
              v-if="locations.length > 0"
              class="my-4 h-0.5 rounded-full border-0 bg-gradient-to-r from-gray-800 via-gray-50 to-gray-800"
            />

            <AddLocation />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
