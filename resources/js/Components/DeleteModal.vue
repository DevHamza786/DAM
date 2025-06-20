<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md w-full text-center relative">
      <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
      <div class="flex flex-col items-center">
        <svg class="h-16 w-16 text-red-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <h2 class="text-xl font-bold mb-2 text-gray-800">Delete Asset?</h2>
        <p class="mb-6 text-gray-500">Are you sure you want to delete <span class="font-semibold">{{ assetName }}</span>? This action cannot be undone.</p>
        <div class="flex justify-center gap-4">
          <button @click="$emit('close')" class="px-6 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold">Cancel</button>
          <button @click="$emit('confirm')" class="px-6 py-2 rounded bg-red-500 hover:bg-red-600 text-white font-semibold">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  assetName: String
});
defineEmits(['close', 'confirm']);
</script>
