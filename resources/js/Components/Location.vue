<script setup lang="ts">
import Toggle from '@/Components/Toggle.vue';
import { onBeforeUnmount, onMounted } from 'vue';

const showSettings = (event) => {
  const closestCard = event.target.closest('div');

  if (closestCard) {
    closestCard.querySelector('.settings').style.display = 'block';
  }
};

const handleClickOutside = (event) => {
  const settings = event.target.closest('.settings');

  if (!settings && !event.target.classList.contains('settings-button')) {
    document.querySelector('.settings').style.display = 'none';
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative mt-2 flex flex-row justify-between">
    <div class="w-full text-2xl dark:text-gray-300">Paris</div>

    <button
      @click="showSettings"
      class="settings-button ml-4 flex h-12 w-16 items-center justify-center rounded-md bg-gray-800 p-0 text-3xl transition hover:bg-gray-500"
    >
      &middot; &middot; &middot;
    </button>

    <div
      ref="target"
      class="settings absolute right-0 top-[105%] hidden rounded-md bg-gray-500 p-4 text-lg shadow-lg"
    >
      <div>
        <h3>Notify By</h3>

        <div class="mt-4 flex flex-col space-y-4">
          <Toggle value="Email" />
          <Toggle value="SMS" />
        </div>
      </div>

      <hr class="my-4" />

      <button
        class="w-full rounded-md bg-red-800 px-2 transition hover:bg-red-500"
      >
        Delete
      </button>
    </div>
  </div>
</template>
