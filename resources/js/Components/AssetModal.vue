<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 overflow-y-auto py-8">
    <div class="bg-gray-900 rounded-xl p-6 max-w-4xl w-full relative shadow-2xl my-auto">
      <button @click="$emit('close')" class="absolute top-2 right-2 text-white text-2xl hover:text-red-400">&times;</button>
      <div v-if="asset">
        <!-- Image Preview -->
        <img v-if="asset.mime_type && asset.mime_type.startsWith('image/')" :src="fileUrl" class="w-full max-w-full max-h-[60vh] object-contain mb-4 rounded" />
        <!-- Video Preview -->
        <video v-else-if="asset.mime_type && asset.mime_type.startsWith('video/')" :src="fileUrl" controls class="w-full max-w-full max-h-[60vh] object-contain mb-4 rounded"></video>
        <!-- PDF Preview -->
        <iframe
          v-else-if="asset.mime_type === 'application/pdf'"
          :src="fileUrl + '#toolbar=1&view=FitH'"
          class="w-full h-[80vh] min-h-[600px] object-contain mb-4 rounded border"
          style="background: #fff;"
        ></iframe>
        <!-- PPT/PPTX Preview -->
        <div
          v-else-if="asset.mime_type === 'application/vnd.ms-powerpoint' || asset.mime_type === 'application/vnd.openxmlformats-officedocument.presentationml.presentation'"
          class="w-full h-[80vh] min-h-[600px] flex flex-col items-center justify-center bg-gray-800 rounded"
        >
          <div class="text-center">
            <svg class="h-32 w-32 text-orange-400 mx-auto mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-2xl font-bold text-white mb-2">PowerPoint Presentation</h3>
            <p class="text-gray-400 mb-6">{{ asset.name }}</p>
            <a
              :href="fileUrl"
              target="_blank"
              class="inline-flex items-center px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Download Presentation
            </a>
          </div>
        </div>
        <!-- DOCX Download Option -->
        <div
          v-else-if="asset.mime_type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'"
          class="w-full h-[80vh] min-h-[600px] flex flex-col items-center justify-center bg-gray-800 rounded"
        >
          <div class="text-center">
            <svg class="h-32 w-32 text-blue-400 mx-auto mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-2xl font-bold text-white mb-2">Word Document</h3>
            <p class="text-gray-400 mb-6">{{ asset.name }}</p>
            <a
              :href="fileUrl"
              target="_blank"
              class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-lg transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Download Document
            </a>
          </div>
        </div>
        <!-- Fallback for other files -->
        <div v-else class="mb-4 text-white">No Preview Available</div>
        <!-- Details -->
        <div class="text-white space-y-1 mt-4">
          <div><span class="font-bold">Name:</span> {{ asset.name }}</div>
          <div><span class="font-bold">Description:</span> {{ asset.description }}</div>
          <div><span class="font-bold">Type:</span> {{ asset.mime_type }}</div>
          <div><span class="font-bold">Size:</span> {{ asset.file_size }} bytes</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';

const props = defineProps({
  show: Boolean,
  asset: Object
});

const fileUrl = computed(() => {
  if (!props.asset) return '';
  return props.asset.file_path.startsWith('http')
    ? props.asset.file_path
    : '/storage/' + props.asset.file_path;
});

// Safe origin for Office Viewer
const origin = ref('');
onMounted(() => {
  origin.value = window.location.origin;
});
</script>
