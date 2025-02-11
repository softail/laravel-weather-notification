<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Location from '@/Components/Location.vue';
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
            <Location />

            <hr
              class="my-4 h-0.5 rounded-full border-0 bg-gradient-to-r from-gray-800 via-gray-50 to-gray-800"
            />

            <InputLabel value="Enter New Location" />

            <TextInput
              model-value=""
              class="w-full rounded-md border text-lg text-black"
              placeholder="London"
            />

            <div class="mt-4 flex flex-row justify-between">
              <button
                class="inline-block max-w-[180px] rounded-md bg-blue-700 px-4 py-2 text-lg text-white transition hover:bg-blue-500"
              >
                Add New
              </button>

              <button
                class="right inline-block max-w-[180px] rounded-md bg-green-700 px-4 py-2 text-lg text-white transition hover:bg-green-500"
              >
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
