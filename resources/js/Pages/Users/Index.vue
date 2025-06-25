<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
                <Link :href="route('users.create')" class="inline-flex items-center px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Create User
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
                                    placeholder="Search users by name, email, or designation..."
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
                            <template #item-name="item">
                                <div class="font-medium text-gray-900">{{ item.name }}</div>
                                <div class="text-sm text-gray-500">{{ item.email }}</div>
                            </template>
                            <template #item-designation="item">
                                <div class="text-sm text-gray-900">{{ item.designation || '-' }}</div>
                            </template>
                            <template #item-roles="{ roles }">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="role in roles" :key="role.id" class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
                                        {{ role.name }}
                                    </span>
                                </div>
                            </template>
                            <template #item-status="{ is_enabled }">
                                <span :class="[
                                    'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                    is_enabled ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                ]">
                                    {{ is_enabled ? 'Active' : 'Inactive' }}
                                </span>
                            </template>
                            <template #item-actions="item">
                                <div class="flex justify-end space-x-3">
                                    <Link :href="route('users.edit', item.id)" class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </Link>
                                    <button v-if="!item.roles.some(role => role.name === 'admin')" @click="deleteUser(item)" class="inline-flex items-center text-red-600 hover:text-red-900">
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

const props = defineProps({
    users: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

const headers = [
    { text: "Name & Email", value: "name", sortable: true },
    { text: "Designation", value: "designation", sortable: true },
    { text: "Roles", value: "roles", sortable: false },
    { text: "Status", value: "status", sortable: true, width: 100 },
    { text: "Actions", value: "actions", sortable: false, width: 150 }
];

const serverOptions = ref({
    page: 1,
    rowsPerPage: 10,
    sortBy: 'name',
    sortType: 'asc'
});

const searchQuery = ref(props.filters.search || '');

const totalItems = computed(() => props.users.total);
const tableItems = computed(() => props.users.data || []);

onMounted(() => {
    serverOptions.value.page = props.users.current_page;
    serverOptions.value.rowsPerPage = props.users.per_page;
});

const handleSearch = debounce((e) => {
    router.get(route('users.index'), {
        search: searchQuery.value,
        page: 1
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

const onOptionsUpdate = (newOptions) => {
    router.get(route('users.index'), {
        page: newOptions.page,
        perPage: newOptions.rowsPerPage,
        sort: newOptions.sortBy,
        order: newOptions.sortType,
        search: searchQuery.value
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
        router.delete(route('users.destroy', user.id));
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
</style>
