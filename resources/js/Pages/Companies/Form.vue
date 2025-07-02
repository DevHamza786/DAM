<template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form @submit.prevent="submit" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <InputLabel for="name" value="Company Name" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <!-- Logo -->
                    <div>
                        <InputLabel for="logo" value="Company Logo" />
                        <input
                            type="file"
                            id="logo"
                            @change="e => form.logo = e.target.files[0]"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-1"
                            accept="image/*"
                        />
                        <InputError :message="form.errors.logo" class="mt-2" />
                        <div v-if="company?.logo" class="mt-2 flex justify-center items-center bg-gray-200 border border-gray-400 rounded p-2" style="min-height: 80px; min-width: 120px;">
                            <img :src="'/storage/' + company.logo" class="h-20 w-auto max-w-full object-contain" :alt="company?.name">
                        </div>
                    </div>
                    <!-- Status -->
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.is_active" />
                            <span class="ml-2 text-sm text-gray-600">Active</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center justify-end mt-6 gap-4">
                    <Link
                        :href="route('companies.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400"
                    >
                        Cancel
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ submitButtonText }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Link } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    company: {
        type: Object,
        default: () => ({})
    },
    submitButtonText: {
        type: String,
        default: 'Save'
    }
});

const form = useForm({
    name: props.company?.name ?? '',
    logo: null,
    is_active: props.company?.is_active ?? true
});

const submit = () => {
    if (props.company?.id) {
        const data = new FormData();
        data.append('name', form.name ?? '');
        data.append('is_active', form.is_active ? 1 : 0);
        if (form.logo) {
            data.append('logo', form.logo);
        }
        data.append('_method', 'PUT');
        form.post(route('companies.update', props.company.id), data, {
            preserveScroll: true,
            forceFormData: true
        });
    } else {
        form.post(route('companies.store'));
    }
};

// Ensure form.name is always up-to-date when changing logo
watch(() => props.company?.name, (newName) => {
    if (!form.name) {
        form.name = newName || '';
    }
});
</script>
