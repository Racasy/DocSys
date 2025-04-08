<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

// State for search, filter, and sort
const searchQuery = ref(props.filters.searchQuery || '');
const sortOption = ref(props.filters.sortOption || 'name');
const filterOption = ref(props.filters.filterOption || 'all');

// Watch for changes and update the URL query parameters
watch([searchQuery, filterOption, sortOption], () => {
    router.get(
        route('admin.users.index'),
        {
            searchQuery: searchQuery.value,
            filterOption: filterOption.value,
            sortOption: sortOption.value,
        },
        {
            preserveState: true, // Preserve the state of the page
            preserveScroll: true, // Prevent scrolling to the top
        }
    );
});
</script>

<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h1 class="text-2xl font-bold text-gray-800">Uzņēmumi</h1>
                </div>

                <div class="p-6">
                    <!-- Search and filters -->
                    <div class="mb-6 space-y-4">
                        <!-- Search input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                v-model="searchQuery"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                placeholder="Meklēt uzņēmumus..."
                            />
                        </div>

                        <!-- Filters and Sort -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Filter dropdown -->
                            <div class="w-full sm:w-1/2">
                                <label for="filter" class="block mb-2 text-sm font-medium text-gray-900">Filtrēt pēc</label>
                                <select
                                    id="filter"
                                    v-model="filterOption"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                >
                                    <option value="all">Visi uzņēmumi</option>
                                    <option value="hasRequests">Ar pieprasījumiem</option>
                                    <option value="noRequests">Bez pieprasījumiem</option>
                                </select>
                            </div>

                            <!-- Sort dropdown -->
                            <div class="w-full sm:w-1/2">
                                <label for="sort" class="block mb-2 text-sm font-medium text-gray-900">Kārtot pēc</label>
                                <select
                                    id="sort"
                                    v-model="sortOption"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                >
                                    <option value="name">Nosaukums (A-Z)</option>
                                    <option value="name-desc">Nosaukums (Z-A)</option>
                                    <option value="requests">Iesniegumi (mazāk → vairāk)</option>
                                    <option value="requests-desc">Iesniegumi (vairāk → mazāk)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Results count -->
                    <div class="mb-4 text-sm text-gray-600">
                        Atrasti {{ users.total }} uzņēmumi
                    </div>

                    <div v-if="users.data.length === 0" class="text-center py-8 text-gray-500">
                        Nav atrasts neviens uzņēmums
                    </div>

                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <Link
                            v-for="user in users.data"
                            :key="user.id"
                            :href="route('admin.users.years', user.id)"
                            class="block bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition duration-200 overflow-hidden"
                        >
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2 truncate">{{ user.name }}</h3>
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="font-medium">{{ user.document_requests_count }}</span>
                                    <span class="ml-1">iesniegumi</span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-6 flex justify-center">
                        <div class="flex space-x-2">
                            <template v-for="link in users.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-4 py-2 border rounded-lg text-sm font-medium"
                                    :class="{
                                        'bg-blue-500 text-white': link.active,
                                        'bg-gray-100 text-gray-700': !link.active,
                                    }"
                                />
                                <span
                                    v-else
                                    class="px-4 py-2 border rounded-lg text-sm font-medium bg-gray-50 text-gray-400 cursor-not-allowed"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>