<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    description: '',
    category: '',
    file: null,
});

const filePreview = ref(null);

function handleFileChange(e) {
    const file = e.target.files[0];
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
            form.reset('name', 'description', 'category','file');
            filePreview.value = null;
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Add Asset" />
        <template #header>
            <div class="flex items-center gap-4">
                <Link href="/assets" class="text-black hover:underline transition">&larr; Back to Assets</Link>
            </div>
        </template>
        <div class="py-12 bg-gradient-to-br from-white via-gray-100 to-gray-200 min-h-screen">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-10 rounded-2xl shadow-2xl border border-gray-200 animate-fade-in">
                    <div class="mb-8 text-center">
                        <h3 class="text-2xl font-bold text-black mb-2">Upload a New Asset</h3>
                        <p class="text-gray-500">Fill in the details and upload your media file.</p>
                    </div>
                    <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <label class="block text-black font-semibold mb-1">Name</label>
                            <input v-model="form.name" type="text"
                                class="mt-1 block w-full border border-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-black focus:border-black transition bg-gray-50 text-black"
                                required />
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div>
                            <label class="block text-black font-semibold mb-1">Description</label>
                            <textarea v-model="form.description"
                                class="mt-1 block w-full border border-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-black focus:border-black transition bg-gray-50 text-black"></textarea>
                            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{
                                form.errors.description }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-black font-semibold mb-1">Category</label>
                            <select v-model="form.category"
                                class="mt-1 block w-full border border-gray-400 rounded-lg px-3 py-2 focus:ring-2 focus:ring-black focus:border-black transition bg-gray-50 text-black"
                                required>
                                <option value="media">Media</option>
                                <option value="tvc">TVC</option>
                                <option value="ppt">PPT</option>
                                <option value="reel">Reel</option>
                            </select>
                            <div v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category
                                }}</div>
                        </div>
                        <div>
                            <label class="block text-black font-semibold mb-1">File</label>
                            <input type="file" @change="handleFileChange"
                                class="mt-1 block w-full file:bg-black file:text-white file:rounded file:px-4 file:py-2 file:border-0 file:font-semibold file:cursor-pointer bg-gray-50 text-black"
                                required />
                            <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ form.errors.file }}</div>
                            <div v-if="filePreview" class="mt-4 flex justify-center">
                                <img :src="filePreview" alt="Preview" class="max-h-40 rounded shadow border" />
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-black hover:bg-gray-900 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-black"
                            :disabled="form.processing">
                            <span v-if="form.processing" class="animate-spin mr-2">&#9696;</span>
                            Upload Asset
                        </button>
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