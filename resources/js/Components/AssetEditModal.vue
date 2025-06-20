<template>
  <Modal :show="show" @close="close">
    <div class="bg-[#181f2a] shadow-2xl max-w-[1100px] w-full mx-auto p-0 overflow-hidden relative">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
        <!-- Left: File Preview & Info -->
        <div class="flex flex-col items-center justify-center py-10 px-6 bg-[#181f2a] h-full">
          <div class="flex items-center justify-center w-full h-full">
            <div class="w-full flex items-center justify-center bg-gray-900 rounded-xl overflow-hidden border border-gray-800 mb-6 min-h-[220px] max-h-[420px]">
              <template v-if="asset && isImage(asset.mime_type)">
                <img :src="fileUrl" :alt="asset.name" class="max-w-full max-h-[400px] object-contain" />
              </template>
              <template v-else-if="asset && isVideo(asset.mime_type)">
                <video :src="fileUrl" class="max-w-full max-h-[400px] object-contain" controls />
              </template>
              <template v-else-if="asset && isPDF(asset.mime_type)">
                <iframe
                  :src="fileUrl"
                  class="w-full h-[400px] bg-white rounded-md"
                  style="border:none;"
                />
              </template>
              <template v-else-if="asset && isPPT(asset.mime_type)">
                <iframe
                  :src="`https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(fileUrl)}`"
                  class="w-full h-[400px] bg-white rounded-md"
                  frameborder="0"
                  allowfullscreen
                />
              </template>
              <template v-else>
                <svg class="w-24 h-24 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
              </template>
            </div>
          </div>
          <div class="w-full mt-2">
            <div class="text-xl font-bold text-white mb-1 truncate">{{ asset?.name }}</div>
            <div class="text-base text-gray-400 mb-1 truncate">{{ asset?.description || 'No description' }}</div>
            <div class="flex flex-wrap gap-6 text-base text-gray-400 mb-2">
              <span><b>Type:</b> {{ asset?.mime_type || 'N/A' }}</span>
              <span v-if="asset?.file_size"><b>Size:</b> {{ formatFileSize(asset.file_size) }}</span>
            </div>
          </div>
        </div>
        <!-- Right: Edit Form -->
        <div class="bg-[#151a23] flex flex-col justify-center px-8 py-10 rounded-b-2xl md:rounded-bl-none md:rounded-r-2xl border-t md:border-t-0 md:border-l border-gray-800 min-w-[320px]">
          <form @submit.prevent="submit" class="space-y-6 w-full">
            <div>
              <label class="block text-base font-medium text-gray-400 mb-1">Name</label>
              <input v-model="form.name" class="w-full border border-gray-700 bg-[#232b3a] text-white rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none transition text-base" required />
            </div>
            <div>
              <label class="block text-base font-medium text-gray-400 mb-1">Description</label>
              <textarea v-model="form.description" rows="4" class="w-full border border-gray-700 bg-[#232b3a] text-white rounded-md px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none transition text-base" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
              <button type="button" @click="close" class="px-5 py-2 bg-gray-700 text-gray-200 rounded-md hover:bg-gray-600 transition text-base">Cancel</button>
              <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-base">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, watch, reactive, computed } from 'vue'
import Modal from './Modal.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  show: Boolean,
  asset: Object,
})

const emit = defineEmits(['close', 'updated'])

const form = reactive({
  name: '',
  description: '',
})

watch(() => props.asset, (val) => {
  if (val) {
    form.name = val.name
    form.description = val.description
  }
}, { immediate: true })

function close() {
  emit('close')
}

function submit() {
  router.put(`/assets/${props.asset.id}`, form, {
    onSuccess: () => {
      emit('updated')
      close()
    }
  })
}

// File type helpers
function isImage(mime) {
  return mime && mime.startsWith('image/')
}
function isVideo(mime) {
  return mime && mime.startsWith('video/')
}
function isPDF(mime) {
  return mime && mime === 'application/pdf'
}
function isPPT(mime) {
  return mime && (
    mime === 'application/vnd.ms-powerpoint' ||
    mime === 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
  )
}
function formatFileSize(size) {
  if (!size) return 'N/A'
  const i = Math.floor(Math.log(size) / Math.log(1024))
  return (
    (size / Math.pow(1024, i)).toFixed(2) * 1 +
    ' ' +
    ['bytes', 'KB', 'MB', 'GB', 'TB'][i]
  )
}

// Computed fileUrl for preview
const fileUrl = computed(() => {
  if (!props.asset || !props.asset.file_path) return '';
  return props.asset.file_path.startsWith('http')
    ? props.asset.file_path
    : '/storage/' + props.asset.file_path;
});
</script>
