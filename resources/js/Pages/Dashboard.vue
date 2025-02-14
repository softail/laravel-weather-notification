<script setup lang="ts">
import AddLocation from '@/Components/AddLocation.vue';
import Location from '@/Components/Location.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { provide, ref } from 'vue';

const props = defineProps({
  locations: Object,
  notificationTypes: Array,
});

const showProgress = ref(false);
const pulseProgress = () => {
  showProgress.value = true;

  setTimeout(() => (showProgress.value = false), 600);
};

provide('notificationTypes', props.notificationTypes);
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl max-w-[900px] sm:px-6 lg:px-8">
        <div class="h-1">
          <hr
            v-show="showProgress"
            class="transition-all h-1 animate-pulse rounded-full border-0 bg-gradient-to-r from-white via-green-500 to-white dark:from-gray-800 dark:via-green-500 dark:to-gray-800"
          />
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
          <div
            class="flex flex-col justify-center p-6 text-gray-900 dark:text-gray-100"
          >
            <div class="max-h-[50vh] overflow-y-auto">
              <Location
                @success="pulseProgress"
                v-for="location in locations"
                :key="location.id"
                :location="location"
              />
            </div>

            <hr
              v-if="locations.length > 0"
              class="my-4 h-0.5 rounded-full border-0 bg-gradient-to-r from-white via-gray-800 to-white dark:from-gray-800 dark:via-gray-50 dark:to-gray-800"
            />

            <AddLocation @success="pulseProgress"/>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
