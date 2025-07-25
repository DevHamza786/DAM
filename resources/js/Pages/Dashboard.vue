<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { format } from 'date-fns';
import { router } from '@inertiajs/vue3'

const props = defineProps({
    statistics: Object,
    recentActivity: Object,
    user: Object,
    error: {
        type: String,
        default: null
    },
    search: String
});

const formatFileSize = (bytes) => {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const formatDate = (date) => {
    return format(new Date(date), 'MMM dd, yyyy HH:mm');
};

const search = ref(props.search || '');

function onSearch() {
    router.get(route('dashboard'), { search: search.value }, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
}

function goToPage(url) {
    if (url) {
        router.visit(url, { scroll: true })
    }
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">
                    Welcome back, {{ user.name }}!
                </h2>
                <div class="text-sm text-gray-500">
                    Last login: {{ formatDate(user.last_login_at || new Date()) }}
                </div>
            </div>
        </template>

        <div class="py-12 bg-gray-100 min-h-screen">
            <!-- Error Alert -->
            <div v-if="error" class="max-w-7xl mx-auto px-6 lg:px-8 mb-6">
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                {{ error }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Images Card -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100">Images</p>
                                <h3 class="text-4xl font-bold mt-1">{{ statistics.images }}</h3>
                            </div>
                            <div class="bg-blue-400/30 p-3 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Videos Card -->
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100">Videos</p>
                                <h3 class="text-4xl font-bold mt-1">{{ statistics.videos }}</h3>
                            </div>
                            <div class="bg-purple-400/30 p-3 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Presentations Card -->
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100">Presentations</p>
                                <h3 class="text-4xl font-bold mt-1">{{ statistics.presentations }}</h3>
                            </div>
                            <div class="bg-orange-400/30 p-3 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Documents Card -->
                    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100">Documents</p>
                                <h3 class="text-4xl font-bold mt-1">{{ statistics.documents }}</h3>
                            </div>
                            <div class="bg-blue-500/30 p-3 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- PDFs Card -->
                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100">PDFs</p>
                                <h3 class="text-4xl font-bold mt-1">{{ statistics.pdfs }}</h3>
                            </div>
                            <div class="bg-red-400/30 p-3 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total Storage Card -->
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100">Total Storage</p>
                                <h3 class="text-4xl font-bold mt-1">{{ formatFileSize(statistics.total_size) }}</h3>
                            </div>
                            <div class="bg-green-400/30 p-3 rounded-lg">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Recent Activity</h3>
                        <div class="flex items-center">
                            <input
                                v-model="search"
                                @input="onSearch"
                                type="text"
                                placeholder="Search by file name..."
                                class="border rounded-lg px-4 py-2 w-full max-w-xs"
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div v-for="activity in recentActivity.data" :key="activity.id" class="flex items-start space-x-4 p-4 bg-gray-50 rounded-xl">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V8" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ activity.causer ? activity.causer.name : 'System' }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ activity.description }}
                                </p>
                                <p v-if="activity.properties?.attributes?.name || activity.properties?.old?.name" class="text-sm text-gray-700">
                                    <span class="font-semibold">File:</span>
                                    {{ activity.properties.attributes?.name || activity.properties.old?.name }}
                                    <span v-if="activity.properties.attributes?.file_size || activity.properties.old?.file_size">
                                        ({{ formatFileSize(activity.properties.attributes?.file_size || activity.properties.old?.file_size) }})
                                    </span>
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ formatDate(activity.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Pagination -->
                    <div class="flex justify-center mt-6 gap-1">
                        <button
                            v-for="link in recentActivity.links"
                            :key="link.label"
                            :disabled="!link.url"
                            @click="goToPage(link.url)"
                            v-html="link.label"
                            class="px-3 py-1 rounded"
                            :class="{
                                'bg-blue-500 text-white': link.active,
                                'bg-gray-200 text-gray-700': !link.active
                            }"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.bg-gradient-to-br {
    transition: all 0.3s ease;
}
.bg-gradient-to-br:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
}
</style>
