<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    user: Object,
    years: Array,
});
</script>

<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">Gadi - {{ user.name }}</h1>
                    <Link 
                        :href="route('admin.users.index')" 
                        class="inline-flex items-center text-gray-600 hover:text-gray-800 text-sm font-medium"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Atpakaļ
                    </Link>
                </div>
                
                <div class="p-6">
                    <div v-if="years.length === 0" class="text-center py-8 text-gray-500">
                        Nav atrasts neviens gads
                    </div>
                    
                    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                        <Link 
                            v-for="year in years" 
                            :key="year" 
                            :href="route('admin.users.years.requests', { user: user.id, year: year })"
                            class="block bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition duration-200 overflow-hidden"
                        >
                            <div class="p-4 text-center">
                                <h3 class="text-xl font-bold text-gray-800">{{ year }}</h3>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>