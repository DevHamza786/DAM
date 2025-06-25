<template>
    <Head title="Companies" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Companies</h2>
                <Link :href="route('companies.create')" class="inline-flex items-center px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Company
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Search Bar -->
                        <div class="mb-6">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    @input="handleSearch"
                                    placeholder="Search companies by name, email, or phone..."
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                />
                            </div>
                        </div>

                        <EasyDataTable
                            :headers="headers"
                            :items="tableItems"
                            :server-options="serverOptions"
                            :server-items-length="totalItems"
                            @update:options="onOptionsUpdate"
                            table-class-name="customize-table"
                            show-index
                            alternating
                            :rows-items="[10, 25, 50, 100]"
                            rows-per-page-message="Rows per page"
                        >
                            <template #item-logo="{ logo, name }">
                                <div class="flex items-center py-2">
                                    <div class="h-12 w-12 flex-shrink-0">
                                        <img v-if="logo" :src="'/storage/' + logo" class="h-12 w-12 rounded-lg object-cover shadow-sm" :alt="name">
                                        <div v-else class="h-12 w-12 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template #item-name="item">
                                <a
                                    class="font-medium text-gray-900 cursor-pointer hover:underline"
                                    :href="route('assets.index', { company_id: item.id })"
                                >
                                    {{ item.name }}
                                </a>
                            </template>
                            <template #item-assets_count="item">
                                <span class="font-semibold text-gray-700">{{ item.assets_count }}</span>
                            </template>
                            <template #item-status="{ is_active }">
                                <span :class="[
                                    'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                ]">
                                    {{ is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </template>
                            <template #item-actions="item">
                                <div class="flex justify-start space-x-3">
                                    <Link :href="route('companies.edit', item.id)" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </Link>
                                    <button v-if="item.assets_count === 0" @click="deleteCompany(item)" class="inline-flex items-center text-red-600 hover:text-red-900">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </template>
                        </EasyDataTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import 'vue3-easy-data-table/dist/style.css';
import EasyDataTable from 'vue3-easy-data-table';
import debounce from 'lodash/debounce';
import axios from 'axios';

const props = defineProps({
    companies: {
        type: Object,
        required: true
    }
});

const headers = [
    { text: "Logo", value: "logo", sortable: false, width: 100 },
    { text: "Name", value: "name", sortable: true },
    { text: "Assets", value: "assets_count", sortable: false, width: 80 },
    { text: "Status", value: "status", sortable: true, width: 100 },
    { text: "Actions", value: "actions", sortable: false, width: 150 }
];

const serverOptions = ref({
    page: 1,
    rowsPerPage: 10,
    sortBy: 'name',
    sortType: 'asc'
});

const searchQuery = ref('');

const totalItems = computed(() => props.companies.total);
const tableItems = computed(() => props.companies.data || []);

onMounted(() => {
    serverOptions.value.page = props.companies.current_page;
    serverOptions.value.rowsPerPage = props.companies.per_page;
    console.log('Mounted: props.companies', props.companies);
    console.log('Mounted: tableItems', tableItems.value);
});

const handleSearch = debounce((e) => {
    router.get(route('companies.index'), {
        search: searchQuery.value,
        page: 1
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

const onOptionsUpdate = (newOptions) => {
    console.log('Pagination event:', newOptions);
    console.log('Before request: props.companies', props.companies);
    console.log('Before request: tableItems', tableItems.value);
    router.get(route('companies.index'), {
        page: newOptions.page,
        perPage: newOptions.rowsPerPage,
        sort: newOptions.sortBy,
        order: newOptions.sortType,
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: (page) => {
            console.log('After request: Inertia page', page);
            console.log('After request: props.companies', props.companies);
            console.log('After request: tableItems', tableItems.value);
        }
    });
};

const deleteCompany = (company) => {
    if (confirm(`Are you sure you want to delete ${company.name}?`)) {
        router.delete(route('companies.destroy', company.id));
    }
};
</script>

<style>
.customize-table {
    --easy-table-border: 1px solid #e5e7eb;
    --easy-table-row-border: 1px solid #e5e7eb;
    --easy-table-header-font-size: 0.875rem;
    --easy-table-body-row-font-size: 0.875rem;
    --easy-table-header-height: 45px;
    --easy-table-body-row-height: 50px;
    --easy-table-header-background-color: #f9fafb;
    --easy-table-header-font-color: #4b5563;
    --easy-table-body-row-hover-background-color: #f3f4f6;
}

.vue3-easy-data-table {
    @apply bg-white rounded-lg;
}

.vue3-easy-data-table__header {
    @apply bg-gray-50 font-semibold;
}

.vue3-easy-data-table__header-item {
    @apply text-xs font-medium text-gray-500 uppercase tracking-wider;
}

.vue3-easy-data-table__main {
    @apply border-t border-gray-200;
}

.vue3-easy-data-table__body-item {
    @apply text-sm text-gray-900;
}

.vue3-easy-data-table__footer {
    @apply bg-white border-t border-gray-200 py-3 px-4;
    min-height: 60px;
}

/* Rows per page selector styling */
.vue3-easy-data-table__rows-per-page-selector {
    @apply border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500;
    min-width: 75px !important;
    height: 32px !important;
    position: relative !important;
}

.vue3-easy-data-table__rows-per-page-selector select {
    @apply appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-1 text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500;
    width: 100% !important;
    height: 100% !important;
    cursor: pointer;
}

/* Custom dropdown arrow */
.vue3-easy-data-table__rows-per-page-selector::after {
    content: '';
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 5px solid #6b7280;
    pointer-events: none;
}

/* Dropdown options styling */
.vue3-easy-data-table__rows-per-page-selector select option {
    @apply py-2 px-3 text-gray-900 bg-white hover:bg-indigo-50;
}

.vue3-easy-data-table__rows-per-page {
    @apply flex items-center gap-2;
    margin-right: 1rem !important;
}

.vue3-easy-data-table__page-button {
    @apply text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-md px-3 py-1;
    min-width: 32px !important;
    height: 32px !important;
}

.vue3-easy-data-table__page-button-active {
    @apply bg-indigo-50 text-indigo-600 font-semibold;
}

.vue3-easy-data-table__page-button-disabled {
    @apply opacity-50 cursor-not-allowed;
}

/* Alternating row colors */
.vue3-easy-data-table__body-row:nth-child(even) {
    background-color: #f9fafb;
}

/* Hover effect */
.vue3-easy-data-table__body-row:hover {
    background-color: #f3f4f6;
}

/* Fix for rows per page container */
.vue3-easy-data-table__footer .footer-top {
    min-height: 40px !important;
    padding: 8px 0;
    position: relative;
    z-index: 10;
}

/* Fix for pagination container */
.vue3-easy-data-table__footer .footer-bottom {
    min-height: 40px !important;
    padding: 8px 0;
}

/* Custom select wrapper */
.vue3-easy-data-table__rows-per-page {
    position: relative;
    z-index: 20;
}

/* Rows per page text */
.vue3-easy-data-table__rows-per-page span {
    @apply text-sm text-gray-700;
    white-space: nowrap;
}
</style>
