<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  location: Object,
  forecast: Object,
});

const currentTemperature = computed(() => {
  return props.forecast?.current?.temperature;
});

const tabOptions = ['Today', 'Hourly', 'Daily'];
</script>

<template>
  <Head :title="location?.name" />

  <AuthenticatedLayout>
    <template #header>
      <h2
        class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
      >
        {{ location?.name }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl max-w-[900px] sm:px-6 lg:px-8">
        <div
          class="mx-4 bg-white shadow-sm sm:rounded-lg md:mx-0 dark:bg-gray-800"
        >
          <div class="sm:hidden">
            <label for="tabs" class="sr-only">Choose Weather Information</label>
            <select
              id="tabs"
              class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
            >
              <option v-for="(option, index) in tabOptions" :key="index">
                {{ option }}
              </option>
            </select>
          </div>

          <ul
            class="hidden rounded-lg text-center text-sm font-medium text-gray-500 shadow-sm sm:flex dark:divide-gray-700 dark:text-gray-400"
          >
            <li
              v-for="(option, index) in tabOptions"
              :key="index"
              class="w-full focus-within:z-10"
            >
              <a
                v-if="index === 0"
                href="#"
                class="active inline-block w-full rounded-s-lg border-r border-gray-200 bg-gray-100 p-4 text-gray-900 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white"
                aria-current="page"
              >
                {{ option }}
              </a>

              <a
                v-else
                href="#"
                class="inline-block w-full border-r border-gray-200 bg-white p-4 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:hover:text-white"
              >Hourly</a
              >
            </li>
          </ul>
        </div>

        <div
          class="text-3xl mt-4 p-4 dark:text-white bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
        >
          <div>
            Current Temperature
            <span
              class="w-24 text-end drop-shadow-sm"
              :class="{
                'text-blue-600': currentTemperature < -25,
                'text-blue-300': currentTemperature < -10,
                'text-blue-200': currentTemperature < 0,
                'text-white': currentTemperature === 0,
                'text-orange-200': currentTemperature > 0,
                'text-orange-300': currentTemperature > 10,
                'text-orange-600': currentTemperature > 25,
              }"
            >
              {{ currentTemperature }}°C
            </span>
          </div>

          <div>
            Precipitation {{ forecast?.current.precipitation }}
          </div>

          <div>
            Wind speed {{ forecast?.current.wind_speed }} km/h.
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
