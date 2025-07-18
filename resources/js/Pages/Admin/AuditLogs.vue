<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    logs: Object,
    filters: Object,
});

// State for search and filters
const nameSearch = ref(props.filters.name || '');
const selectedActions = ref(props.filters.actions || []);

// Watch for changes and update the URL query parameters
watch([nameSearch, selectedActions], () => {
    router.get(
        route('admin.audit.logs'),
        {
            name: nameSearch.value,
            actions: selectedActions.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}, { deep: true });

// Function to toggle action filters
function toggleAction(action) {
    if (selectedActions.value.includes(action)) {
        selectedActions.value = selectedActions.value.filter(a => a !== action);
    } else {
        selectedActions.value.push(action);
    }
}

// Function to clear filters
function clearFilters() {
    nameSearch.value = '';
    selectedActions.value = [];
}

// Generate readable action descriptions
const actionDescriptions = computed(() => {
    return {
        'changed_password': 'Changed password',
        'uploaded_document': 'Uploaded document',
        'login': 'Logged in',
    };
});

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleString('en-US', options);
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h1 class="text-2xl font-bold text-gray-800">Audit Logs</h1>
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
                                v-model="nameSearch"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                placeholder="Search by user name..."
                            />
                        </div>

                        <!-- Filters and Actions -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Action filters -->
                            <div class="w-full">
                                <label class="block mb-2 text-sm font-medium text-gray-900">Filter Actions</label>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="action in ['changed_password', 'uploaded_document', 'login']"
                                        :key="action"
                                        @click="toggleAction(action)"
                                        :class="{
                                            'bg-blue-500 text-white': selectedActions.includes(action),
                                            'bg-gray-100 text-gray-700 hover:bg-gray-200': !selectedActions.includes(action),
                                        }"
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                    >
                                        {{ actionDescriptions[action] }}
                                    </button>
                                </div>
                            </div>

                            <!-- Clear filters button -->
                            <div class="w-full sm:w-auto flex items-end">
                                <button
                                    @click="clearFilters"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors"
                                >
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Results count -->
                    <div class="mb-4 text-sm text-gray-600">
                        Found {{ logs.total }} logs
                    </div>

                    <div v-if="logs.data.length === 0" class="text-center py-8 text-gray-500">
                        No logs found
                    </div>

                    <div v-else class="space-y-4">
                        <div 
                            v-for="log in logs.data" 
                            :key="log.id" 
                            class="bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition duration-200 overflow-hidden"
                        >
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800 mb-1">
                                            {{ log.user?.name || `User (ID: ${log.user_id})` }}
                                        </h3>
                                        <div class="text-sm text-gray-600 mb-2">
                                            {{ actionDescriptions[log.action] || log.action }}
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ formatDate(log.created_at) }}
                                    </div>
                                </div>
                                
                                <details class="mt-3">
                                    <summary class="cursor-pointer text-blue-600 text-sm font-medium">
                                        View Details
                                    </summary>
                                    <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                            <div>
                                                <p class="font-medium text-gray-700">Action Type:</p>
                                                <p class="text-gray-900">{{ log.action }}</p>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-700">User ID:</p>
                                                <p class="text-gray-900">{{ log.user_id }}</p>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-700">Event:</p>
                                                <p class="text-gray-900">{{ log.event || 'N/A' }}</p>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-700">IP Address:</p>
                                                <p class="text-gray-900">{{ log.ip_address || 'N/A' }}</p>
                                            </div>
                                            <div class="md:col-span-2">
                                                <p class="font-medium text-gray-700">Description:</p>
                                                <p class="text-gray-900">{{ log.description || 'No additional details' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Links -->
                    <div class="mt-6 flex justify-center">
                        <div class="flex space-x-2">
                            <template v-for="link in logs.links" :key="link.label">
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