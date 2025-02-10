<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
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
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
          <div class="flex flex-col justify-center space-y-4 p-6 text-gray-900 dark:text-gray-100">
            <label for="location" class="text-lg"> Enter Your Location </label>

            <input
              id="location"
              type="text"
              placeholder="London"
              class="rounded-md border text-black text-lg"
            />

            <button
              class="mx-auto inline-block rounded-md bg-green-500 px-4 py-2 text-xl text-white"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
