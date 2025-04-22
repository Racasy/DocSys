<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

// Assuming these props will be passed from the controller
defineProps({
    adminName: String,
    stats: Object // Contains admin-specific statistics
});

// Helper function for status badge styling (reused from Dashboard.vue)
function getStatusClass(status) {
  switch(status) {
    case 'pending': return 'bg-yellow-100 text-yellow-800';
    case 'approved': return 'bg-green-100 text-green-800';
    case 'denied': return 'bg-red-100 text-red-800';
    default: return 'bg-gray-100 text-gray-800';
  }
}
</script>

<template>
    <Head title="Admina Panelis" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-2xl font-bold text-gray-800">Sveiks, {{ adminName }}!</h2>
                </div>

                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                Tev ir {{ stats.in_progress }} iesniegumi kas jāapstrādā!
                            </h3>
                            <p class="text-sm text-gray-500">Laiks ķerties pie darba!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">Sistēmas Statistika</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Total Requests -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-gray-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Kopējie iesniegumi</p>
                                <p class="text-3xl font-bold text-gray-800">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- In Progress -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-blue-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-blue-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Iesniegti</p>
                                <p class="text-3xl font-bold text-blue-600">{{ stats.in_progress }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-yellow-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-yellow-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Gaida iesniegumu</p>
                                <p class="text-3xl font-bold text-yellow-600">{{ stats.pending }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Approved -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-green-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-green-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Apstiprināti</p>
                                <p class="text-3xl font-bold text-green-600">{{ stats.approved }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Denied -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-red-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-red-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Atteikti</p>
                                <p class="text-3xl font-bold text-red-600">{{ stats.denied }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Average Processing Time -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-purple-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-purple-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Vid. iesnieguma laiks</p>
                                <p class="text-3xl font-bold text-purple-600">{{ stats.avg_processing_time }} <span class="text-lg font-normal">dienas</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Users -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-indigo-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-indigo-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Aktīvie lietotāji (vismaz 1 iesn. pēdējās 30 dienās)</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ stats.active_users }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Overdue Requests -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-orange-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-orange-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Nokavētie iesniegumi</p>
                                <p class="text-3xl font-bold text-orange-600">{{ stats.overdue }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Submissions -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-teal-100 transition-all duration-300 hover:shadow-lg">
                        <div class="flex items-center p-5">
                            <div class="flex-shrink-0 bg-teal-50 p-3 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-medium text-gray-500">Šodien iesniegti</p>
                                <p class="text-3xl font-bold text-teal-600">{{ stats.today_submissions }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
