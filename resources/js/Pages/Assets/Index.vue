<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, watch } from 'vue';
import AssetModal from '@/Components/AssetModal.vue';
import DeleteModal from '@/Components/DeleteModal.vue';
import { toast } from 'vue3-toastify';
import AssetEditModal from '@/Components/AssetEditModal.vue';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    assetsByCategory: Object,
    userRole: String,
    companies: Array,
    filters: Object,
    flash: Object,
});

const showModal = ref(false);
const selectedAsset = ref(null);
const sliderRefs = ref({});
const showDeleteModal = ref(false);
const assetToDelete = ref(null);
const search = ref(props.filters?.search || '');
const showEditModal = ref(false);
const selectedCompanyId = ref(props.filters?.company_id || (props.companies[0]?.id || ''));

function openModal(asset) {
    selectedAsset.value = asset;
    showModal.value = true;
}
function closeModal() {
    showModal.value = false;
    selectedAsset.value = null;
}

function setSliderRef(category, el) {
    if (el) sliderRefs.value[category] = el;
}

function scrollSlider(category, direction) {
    const slider = sliderRefs.value[category];
    if (slider) {
        const scrollAmount = slider.offsetWidth * 0.8;
        slider.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    }
}

function confirmDelete(asset) {
    assetToDelete.value = asset;
    showDeleteModal.value = true;
}

function handleDeleteConfirmed() {
    deleteAsset(assetToDelete.value);
    showDeleteModal.value = false;
}

function deleteAsset(asset) {
    router.delete(`/assets/${asset.id}`, {
        preserveState: true,
        onSuccess: (page) => {
            if (page.props.flash && page.props.flash.success) {
                toast.success(page.props.flash.success);
            }
            if (page.props.flash && page.props.flash.error) {
                toast.error(page.props.flash.error);
            }
        },
        onError: () => {
            toast.error('Failed to delete asset. Please try again.');
        }
    });
}

function submitSearch() {
    router.get('/assets', { search: search.value || undefined }, { preserveState: true, replace: true });
}

function clearSearch() {
    search.value = '';
}

watch(search, (newVal) => {
  if (newVal === '') {
    router.get('/assets', {}, { preserveState: true, replace: true });
  }
});

function editAsset(asset) {
    selectedAsset.value = asset;
    showEditModal.value = true;
}

function closeEditModal() {
    showEditModal.value = false;
    selectedAsset.value = null;
}

function refreshAssets() {
    // Yahan Inertia reload ya API call kar ke assets list refresh karen
    // router.reload() ya router.visit(route('assets.index')) etc.
}

function selectCompany(companyId) {
    selectedCompanyId.value = companyId;
    router.get('/assets', {
        ...props.filters,
        company_id: companyId || undefined,
        search: search.value || undefined
    }, { preserveState: true, replace: true });
}

watch(() => props.companies, (companies) => {
  if (!selectedCompanyId.value && companies.length > 0) {
    selectedCompanyId.value = companies[0].id;
  }
}, { immediate: true });

onMounted(() => {
    if (!props.filters?.company_id && props.companies.length > 0) {
        selectCompany(props.companies[0].id);
    }
});

</script>

<template>
    <AuthenticatedLayout>
        <Head title="Assets" />
        <template #header>
            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-black leading-tight">Assets</h2>
                    <div class="flex items-center gap-4">
                        <!-- Search Bar (before New Asset button) -->
                        <form @submit.prevent="submitSearch" class="flex gap-2">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search by file name..."
                                class="px-8 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 font-bold"
                                style="min-width: 220px;"
                            />
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 bg-black text-white px-8 py-3 rounded-full shadow-lg hover:scale-105 hover:shadow-2xl transition-all duration-200 font-bold tracking-wide"
                            >
                                Search
                            </button>
                            <button
                                v-if="search"
                                type="button"
                                @click="clearSearch"
                                class="px-2 py-2 bg-gray-200 text-gray-600 rounded-full hover:bg-gray-300 transition"
                            >
                                ✕
                            </button>
                        </form>
                        <!-- New Asset Button -->
                        <Link
                            href="/assets/create"
                            class="inline-flex items-center gap-2 bg-black text-white px-8 py-3 rounded-full shadow-lg hover:scale-105 hover:shadow-2xl transition-all duration-200 font-bold tracking-wide"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Asset
                        </Link>
                    </div>
                </div>
                <!-- Company Tabs -->
                 <hr>
                <div class="flex gap-2 mt-2 overflow-x-auto flex-nowrap pb-2" style="scrollbar-width: thin;">
                    <!-- Responsive horizontal scroll for company tabs -->
                    <button
                        v-for="company in companies"
                        :key="company.id"
                        :class="['px-4 py-2 rounded-full font-semibold whitespace-nowrap', selectedCompanyId == company.id ? 'bg-black text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300']"
                        @click="selectCompany(company.id)"
                    >
                        {{ company.name }}
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-for="(assets, category) in assetsByCategory" :key="category" class="mb-12">
                    <h3 class="text-2xl font-bold text-black mb-4">{{ category }}</h3>
                    <div class="relative">
                        <!-- Left Button -->
                        <button
                            v-if="assets.length > 4"
                            @click="scrollSlider(category, -1)"
                            class="absolute -left-8 top-1/2 -translate-y-1/2 z-10 bg-white/80 hover:bg-white text-black rounded-full shadow p-2"
                            style="min-width: 40px;"
                        >
                            &#8592;
                        </button>

                        <!-- Slider -->
                        <div
                            class="flex gap-6 overflow-x-auto scroll-smooth py-2"
                            :ref="el => setSliderRef(category, el)"
                            style="scrollbar-width: none;"
                        >
                            <div
                                v-for="asset in assets"
                                :key="asset.id"
                                @click="openModal(asset)"
                                class="min-w-[320px] max-w-xs w-full relative group cursor-pointer bg-gradient-to-br from-gray-800 via-gray-900 to-black rounded-2xl shadow-2xl overflow-hidden transition-transform duration-300 hover:scale-105 hover:shadow-blue-500/50 hover:ring-4 hover:ring-blue-400/40"
                            >
                                <!-- Image Preview -->
                                <img
                                    v-if="asset.mime_type && asset.mime_type.startsWith('image/')"
                                    :src="asset.file_path.startsWith('http') ? asset.file_path : '/storage/' + asset.file_path"
                                    alt="Asset Image"
                                    class="w-full h-48 object-cover"
                                />

                                <!-- Video Preview -->
                                <video
                                    v-else-if="asset.mime_type && asset.mime_type.startsWith('video/')"
                                    :src="asset.file_path.startsWith('http') ? asset.file_path : '/storage/' + asset.file_path"
                                    class="w-full h-48 object-cover"
                                    controls
                                    preload="metadata"
                                ></video>

                                <!-- PDF Preview -->
                                <div
                                    v-else-if="asset.mime_type === 'application/pdf'"
                                    class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-gray-700 via-gray-800 to-gray-900"
                                >
                                    <a
                                        :href="asset.file_path.startsWith('http') ? asset.file_path : '/storage/' + asset.file_path"
                                        target="_blank"
                                        @click.stop
                                        class="flex flex-col items-center"
                                    >
                                        <svg class="h-16 w-16 text-red-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-white font-bold">PDF File</span>
                                    </a>
                                </div>

                                <!-- DOC/DOCX Preview -->
                                <div
                                    v-else-if="asset.mime_type === 'application/msword' || asset.mime_type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'"
                                    class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-gray-700 via-gray-800 to-gray-900"
                                >
                                    <a
                                        :href="asset.file_path.startsWith('http') ? asset.file_path : '/storage/' + asset.file_path"
                                        target="_blank"
                                        @click.stop
                                        class="flex flex-col items-center"
                                    >
                                        <svg class="h-16 w-16 text-blue-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-white font-bold">DOC File</span>
                                    </a>
                                </div>

                                <!-- PPT Preview -->
                                <div
                                    v-else-if="asset.mime_type && (asset.mime_type.includes('presentation') || asset.mime_type.includes('powerpoint'))"
                                    class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-gray-700 via-gray-800 to-gray-900"
                                >
                                    <a
                                        :href="asset.file_path.startsWith('http') ? asset.file_path : '/storage/' + asset.file_path"
                                        target="_blank"
                                        @click.stop
                                        class="flex flex-col items-center"
                                    >
                                        <svg class="h-16 w-16 text-orange-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="text-white font-bold">PPT File</span>
                                    </a>
                                </div>

                                <!-- Fallback for other file types -->
                                <div v-else class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-gray-700 via-gray-800 to-gray-900">
                                    <span class="text-white">No Preview</span>
                                </div>

                                <!-- Futuristic Hover Overlay -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4"
                                >
                                    <div class="backdrop-blur-md bg-white/10 rounded-lg p-2">
                                        <h3 class="text-lg font-bold text-white text-center truncate">{{ asset.name }}</h3>
                                    </div>
                                </div>

                                <div class="absolute top-2 right-2 flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Edit icon: show for admin and uploader -->
                                    <button
                                        v-if="['admin', 'uploader'].includes(userRole)"
                                        @click.stop="editAsset(asset)"
                                        class="text-blue-400 hover:text-blue-600"
                                        title="Edit"
                                    >
                                        <!-- Edit SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2h6" />
                                        </svg>
                                    </button>
                                    <!-- Delete icon: show for admin only -->
                                    <button
                                        v-if="userRole === 'admin'"
                                        @click.stop="confirmDelete(asset)"
                                        class="text-red-400 hover:text-red-600"
                                        title="Delete"
                                    >
                                        <!-- Delete SVG -->

                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Button -->
                        <button
                            v-if="assets.length > 4"
                            @click="scrollSlider(category, 1)"
                            class="absolute -right-8 top-1/2 -translate-y-1/2 z-10 bg-white/80 hover:bg-white text-black rounded-full shadow p-2"
                            style="min-width: 40px;"
                        >
                            &#8594;
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <AssetModal :show="showModal" :asset="selectedAsset" @close="closeModal" />
        <DeleteModal
            :show="showDeleteModal"
            :assetName="assetToDelete?.name"
            @close="showDeleteModal = false"
            @confirm="handleDeleteConfirmed"
        />
        <AssetEditModal
            :show="showEditModal"
            :asset="selectedAsset"
            :companies="companies"
            @close="closeEditModal"
            @updated="refreshAssets"
        />
    </AuthenticatedLayout>
</template>

<style>
/* Hide scrollbar for horizontal scroll on company tabs */
.flex-nowrap::-webkit-scrollbar {
  height: 6px;
}
.flex-nowrap::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 4px;
}
.flex-nowrap::-webkit-scrollbar-track {
  background: transparent;
}
</style>
