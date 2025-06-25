<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    companies: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: '',
    company_id: '',
    category: '',
    description: '',
    file: null
});

const filePreview = ref(null);

function handleFileChange(e) {
    const file = e.target.files[0];

    if (!file) {
        form.file = null;
        filePreview.value = null;
        return;
    }

    const maxSizeInMB = 1024; // 1GB
    const maxSizeInBytes = maxSizeInMB * 1024 * 1024;

    if (file.size > maxSizeInBytes) {
        form.setError('file', `File is too large. Maximum size is 1GB.`);
        form.file = null;
        e.target.value = ''; // Reset the file input
        filePreview.value = null;
        return;
    }

    form.clearErrors('file');
    form.file = file;

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (event) => {
            filePreview.value = event.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        filePreview.value = null;
    }
}

function submit() {
    form.post(route('assets.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset('name', 'description', 'category', 'company_id', 'file');
            filePreview.value = null;
        },
    });
}

function formatBytes(bytes, decimals = 2) {
    if (!bytes || bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>

<template>
    <Head title="Upload New Asset" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Upload New Asset
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <InputLabel for="name" value="Asset Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Company -->
                            <div>
                                <InputLabel for="company" value="Company" />
                                <select
                                    id="company"
                                    v-model="form.company_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Company</option>
                                    <option v-for="company in companies" :key="company.id" :value="company.id">
                                        {{ company.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.company_id" class="mt-2" />
                            </div>

                            <!-- Category -->
                            <div>
                                <InputLabel for="category" value="Category" />
                                <select
                                    id="category"
                                    v-model="form.category"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="">Select Category</option>
                                    <option value="Credentials">Credentials</option>
                                    <option value="Showreel">Showreel</option>
                                    <option value="Images Albums">Images Albums</option>
                                    <option value="PPT Template">PPT Template</option>
                                    <option value="Logo Files">Logo Files</option>
                                    <option value="Creative Work">Creative Work</option>
                                    <option value="Case Studies">Case Studies</option>
                                    <option value="Documents">Documents</option>
                                </select>
                                <InputError :message="form.errors.category" class="mt-2" />
                            </div>

                            <!-- File -->
                            <div>
                                <InputLabel for="file" value="File" />
                                <input
                                    type="file"
                                    id="file"
                                    @input="handleFileChange"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-1"
                                    required
                                />
                                <InputError :message="form.errors.file" class="mt-2" />

                                <!-- Progress Bar -->
                                <div v-if="form.progress" class="mt-2">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-600">
                                            {{ formatBytes(form.file.size * form.progress.percentage / 100) }} / {{ formatBytes(form.file.size) }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-600">{{ form.progress.percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: form.progress.percentage + '%' }"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <InputLabel for="description" value="Description" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <Link
                                :href="route('assets.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Upload Asset
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.7s;
}
</style>
