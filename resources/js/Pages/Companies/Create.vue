<template>
    <Head title="Create Company" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Company
            </h2>
        </template>

        <CompanyForm submit-button-text="Create Company" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CompanyForm from './Form.vue';
import { Head } from '@inertiajs/vue3';
</script>
