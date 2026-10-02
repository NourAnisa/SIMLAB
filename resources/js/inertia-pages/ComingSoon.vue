<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from './Layouts/AppLayout.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Fitur Segera Hadir'
    },
    role: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <Head :title="title" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg text-center py-20">
                    <div class="text-6xl mb-4">🚧</div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Fitur Segera Hadir</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        Maaf, fitur {{ title }} untuk pengguna {{ role }} saat ini masih dalam tahap pengembangan dan akan segera tersedia pada pembaruan berikutnya.
                    </p>
                    <Link :href="`/${role}/dashboard`" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Kembali ke Dashboard
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
