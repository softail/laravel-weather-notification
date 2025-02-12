<script setup lang="ts">
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Toggle from '@/Components/Toggle.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, inject, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
  location: Object,
});

const notificationTypes = inject('notificationTypes');

const form = useForm({
  location: props.location.id,
  notify_by: computed(() => {
    let items = [];

    notificationTypes.forEach((item) => {
      if (props.location.notify_by.includes(item)) {
        items.push(item);
      } else {
        items.push(null);
      }
    });

    return items
  }),
});

const showSettings = ref(false);
const confirmingLocationDeletion = ref(false);

const handleClickOutside = (event) => {
  const settings = event.target.closest('.settings');

  if (!settings && !event.target.classList.contains('settings-button')) {
    showSettings.value = false;
  }
};

const deleteLocation = () => {
  form.delete(route('locations.destroy', props.location.id), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => alert('Error deleting location!'),
    onFinish: () => {
      showSettings.value = false;
    },
  });
};

const updateLocation = () => {
  form.patch(route('locations.update', props.location.id), {
    preserveScroll: true,
    onSuccess: () => (showSettings.value = false),
    onError: () => alert('Error updating location!'),
    onFinish: () => {},
  });
};

const closeModal = () => {
  confirmingLocationDeletion.value = false;
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
    <div class="w-full text-2xl dark:text-gray-300">{{ location.name }}</div>

    <button
      @click="showSettings = !showSettings"
      class="settings-button ml-4 flex h-12 w-16 items-center justify-center rounded-md bg-gray-800 p-0 text-3xl transition hover:bg-gray-500"
    >
      &middot; &middot; &middot;
    </button>

    <div
      v-show="showSettings"
      class="settings absolute right-0 top-[105%] z-[10] rounded-md bg-gray-500 p-4 text-lg shadow-lg transition"
    >
      <div>
        <h3>Notify By</h3>

        <div class="mt-4 flex flex-col space-y-4">
          <Toggle
            v-for="(type, index) in notificationTypes"
            :key="index"
            :value="type"
            v-model="form.notify_by[index]"
          />
        </div>
      </div>

      <hr class="my-4" />

      <div class="flex justify-between space-x-4">
        <button
          @click="updateLocation"
          class="w-full rounded-md bg-green-800 px-3 py-1 transition hover:bg-green-500"
        >
          Save
        </button>

        <button
          @click="confirmingLocationDeletion = true"
          class="w-full rounded-md bg-red-800 px-3 py-1 transition hover:bg-red-500"
        >
          Delete
        </button>
      </div>
    </div>
  </div>

  <Modal :show="confirmingLocationDeletion" @close="closeModal">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
        Are you sure you want to delete your account?
      </h2>

      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        Once location is deleted, you will no longer receive notifications.
      </p>

      <div class="mt-6 flex justify-end">
        <SecondaryButton @click="closeModal"> Cancel</SecondaryButton>

        <DangerButton
          class="ms-3"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="deleteLocation"
        >
          Delete Location
        </DangerButton>
      </div>
    </div>
  </Modal>
</template>
