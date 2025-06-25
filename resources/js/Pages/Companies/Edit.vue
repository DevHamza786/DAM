<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Company
            </h2>
        </template>

        <CompanyForm
            :company="company"
            submit-button-text="Update Company"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CompanyForm from './Form.vue';

defineProps({
    company: {
        type: Object,
        required: true
    }
});
</script>
