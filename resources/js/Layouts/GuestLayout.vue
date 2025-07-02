<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import Loader from '@/Components/Loader.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const loading = ref(false);

onMounted(() => {
    router.on('start', () => { loading.value = true; });
    router.on('finish', () => { loading.value = false; });
    router.on('error', () => { loading.value = false; });
    router.on('invalid', () => { loading.value = false; });
});
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <Loader v-if="loading" />
        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
