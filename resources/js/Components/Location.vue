<script setup lang="ts">
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Toggle from '@/Components/Toggle.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, inject, onBeforeUnmount, onMounted, ref } from 'vue';

interface Location {
  id: number;
  name: string;
  notify_by: string[];
  current_temperature: number;
}

const props = defineProps<{ location: Location }>();

const emit = defineEmits<{
  (e: 'success'): void;
}>();

const notificationTypes = inject<string[]>('notificationTypes', []);
const notifyBy = computed(() => {
  let items: any[] = [];

  notificationTypes.forEach((item) => {
    if (props.location.notify_by.includes(item)) {
      items.push(item);
    } else {
      items.push(null);
    }
  });

  return items;
});

const form = useForm({
  location: props.location.id,
  notify_by: notifyBy.value
});

const showSettings = ref(false);
const confirmingLocationDeletion = ref(false);

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement | null;
  if (!target) return;

  const settings = target.closest('.settings');
  if (!settings && !target.classList.contains('settings-button')) {
    showSettings.value = false;
  }
};

const deleteLocation = () => {
  form.delete(route('locations.destroy', props.location.id), {
    preserveScroll: true,
    onSuccess: closeModal,
    onError: () => alert('Error deleting location!'),
    onFinish: () => {
      emit('success');
      showSettings.value = false;
    },
  });
};

const updateLocation = () => {
  form.patch(route('locations.update', props.location.id), {
    preserveScroll: true,
    onSuccess: () => (showSettings.value = false),
    onError: () => alert('Error updating location!'),
    onFinish: () => {
      emit('success');
    },
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
  <div class="relative flex flex-row items-center justify-between">
    <Link
      :href="route('locations.show', location.id)"
      class="flex w-full items-center justify-between rounded px-3 py-2 hover:bg-gray-100 lg:text-xl xl:text-2xl dark:text-gray-300 hover:dark:bg-gray-500"
    >
      <span class="w-full">{{ location.name }}</span>

      <span class="px-2">&bull;</span>

      <span
        class="w-24 text-end drop-shadow-sm"
        :class="{
          'text-blue-600': location.current_temperature < -25,
          'text-blue-300': location.current_temperature < -10,
          'text-blue-200': location.current_temperature < 0,
          'text-white': location.current_temperature === 0,
          'text-orange-200': location.current_temperature > 0,
          'text-orange-300': location.current_temperature > 10,
          'text-orange-600': location.current_temperature > 25,
        }"
      >
        {{ location.current_temperature }}°C
      </span>
    </Link>

    <button
      @click="showSettings = !showSettings"
      class="settings-button flex h-12 w-16 items-center justify-center rounded-md bg-white p-0 text-3xl transition hover:bg-gray-100 dark:bg-gray-800 hover:dark:bg-gray-500"
    >
      &middot; &middot; &middot;
    </button>

    <div
      v-show="showSettings"
      class="settings absolute right-0 top-[105%] z-[10] rounded-md bg-gray-50 p-4 text-lg shadow-lg transition dark:bg-gray-500"
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
          class="w-full rounded-md bg-green-400 px-3 py-1 text-white shadow-md transition hover:bg-green-500 dark:bg-green-700"
        >
          Save
        </button>

        <button
          @click="confirmingLocationDeletion = true"
          class="w-full rounded-md bg-red-400 px-3 py-1 text-white shadow-md transition hover:bg-red-500 dark:bg-red-700"
        >
          Delete
        </button>
      </div>
    </div>
  </div>

  <Modal :show="confirmingLocationDeletion" @close="closeModal">
    <div class="p-6">
      <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
        Are you sure you want to delete {{ location.name }}?
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
