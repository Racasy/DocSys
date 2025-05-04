<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
});

const searchQuery = ref(props.filters.searchQuery || '');
const sortOption = ref(props.filters.sortOption || 'name');
const selectedUser = ref(null);

watch([searchQuery, sortOption], () => {
    router.get(route('admin.users'), 
        { searchQuery: searchQuery.value, sortOption: sortOption.value },
        { preserveState: true, preserveScroll: true }
    );
});

const toggleDisable = (user) => {
    router.put(route('admin.user.toggleDisable', user.id), {
        onSuccess: () => {
            user.is_disabled = !user.is_disabled;
        }
    });
};

const showDeleteConfirm = ref(false);
const userToDelete = ref(null);

const deleteUser = (userId) => {
    userToDelete.value = userId;
    showDeleteConfirm.value = true;
};

const confirmDelete = () => {
    router.delete(route('admin.user.delete', userToDelete.value), {
        onSuccess: () => {
            selectedUser.value = null;
            showDeleteConfirm.value = false;
            userToDelete.value = null;
        }
    });
};

const cancelDelete = () => {
    showDeleteConfirm.value = false;
    userToDelete.value = null;
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-5xl mx-auto p-6">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100">
                <!-- Header -->
                <div class="px-8 py-5 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h1 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Lietotāju pārvalde
                    </h1>
                </div>

                <!-- Search and Controls -->
                <div class="p-6 space-y-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                placeholder="Meklēt lietotājus..."
                                class="w-full pl-10 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            />
                        </div>
                        <select 
                            v-model="sortOption" 
                            class="p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        >
                            <option value="name">A-Z</option>
                            <option value="name-desc">Z-A</option>
                        </select>
                    </div>

                    <!-- User List -->
                    <div class="space-y-3">
                        <div v-for="user in users.data" :key="user.id" class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow transition-all duration-200">
                            <!-- User Summary -->
                            <div 
                                class="p-4 flex justify-between items-center cursor-pointer hover:bg-gray-50 transition-colors"
                                @click="selectedUser = selectedUser?.id === user.id ? null : user"
                            >
                                <div class="flex items-center">
                                    <div class="bg-blue-100 text-blue-700 rounded-full h-10 w-10 flex items-center justify-center font-medium mr-3">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ user.name }}</h3>
                                        <p class="text-sm text-gray-600">{{ user.email }}</p>
                                    </div>
                                </div>
                                <span 
                                    class="px-3 py-1 text-sm font-medium rounded-full" 
                                    :class="user.is_disabled ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                >
                                    {{ user.is_disabled ? 'Atspējots' : 'Aktīvs' }}
                                </span>
                            </div>

                            <!-- Management Panel -->
                            <div 
                                v-if="selectedUser?.id === user.id" 
                                class="p-5 border-t bg-gray-50 space-y-4 animate-fadeIn"
                            >
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <p class="text-sm flex items-center">
                                        <span class="mr-2">Statuss:</span>
                                        <span 
                                            class="font-medium px-3 py-1 rounded-full text-sm"
                                            :class="user.is_disabled ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                        >
                                            {{ user.is_disabled ? 'Atspējots' : 'Aktīvs' }}
                                        </span>
                                    </p>
                                    <div class="flex gap-3">
                                        <button
                                            @click="toggleDisable(user)"
                                            class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                                            :class="user.is_disabled ? 
                                                'bg-green-600 hover:bg-green-700 focus:ring-green-500' : 
                                                'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400'"
                                        >
                                            <span class="flex items-center">
                                                <svg v-if="user.is_disabled" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ user.is_disabled ? 'Aktivizēt' : 'Atspējot' }}
                                            </span>
                                        </button>
                                        <button
                                            @click="deleteUser(user.id)"
                                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        >
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Dzēst Lietotāju
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center space-x-1 pt-4">
                        <template v-for="link in users.links" :key="link.label">
                            <Link
                                :href="link.url"
                                v-html="link.label"
                                class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                :class="link.active ? 
                                    'bg-blue-600 text-white' : 
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4 animate-modal">
                <div class="flex items-center justify-center mb-4 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mb-1">Dzēst lietotāju</h3>
                <p class="text-center text-gray-600 mb-6">Vai tiešām vēlaties dzēst šo lietotāju? Šo darbību nevar atsaukt. Viss saistībā ar šo lietotāju tiks neatgriezeniski dzēsts!</p>
                
                <div class="flex justify-center space-x-4">
                    <button 
                        @click="cancelDelete" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                    >
                        Atcelt
                    </button>
                    <button 
                        @click="confirmDelete" 
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        Dzēst
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.animate-fadeIn {
    animation: fadeIn 0.3s ease-in-out;
}

.animate-modal {
    animation: modalEnter 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes modalEnter {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>