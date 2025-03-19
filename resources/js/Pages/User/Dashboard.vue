<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    latestRequest: Object // This comes from Laravel
});

// Helper function to get status badge styling
function getStatusClass(status) {
  switch(status) {
    case 'pending':
      return 'bg-yellow-100 text-yellow-800';
    case 'approved':
      return 'bg-green-100 text-green-800';
    case 'denied':
      return 'bg-red-100 text-red-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-2xl font-bold text-gray-800">Sveiks!</h2>
                </div>
                
                <div class="p-6">
                    <div v-if="latestRequest" class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Jauns iesniegums</h3>
                                <p class="text-sm text-gray-500">Tev ir jauns iesniegums, kas gaida tavu uzmanību!</p>
                            </div>
                        </div>
                        
                        <!-- Latest Request Card -->
                        <a :href="'/user/requests/' + latestRequest.id" class="block bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition duration-150 ease-in-out">
                            <div class="p-5">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <h4 class="text-xl font-semibold text-gray-800">{{ latestRequest.title }}</h4>
                                        <div class="flex items-center mt-2">
                                            <span class="mr-2 text-sm text-gray-600">Termiņš:</span>
                                            <span class="text-sm font-medium text-gray-800">{{ latestRequest.deadline }}</span>
                                        </div>
                                        <div class="mt-2" v-if="latestRequest.status">
                                            <span class="px-2 py-1 text-xs rounded-full" :class="getStatusClass(latestRequest.status)">
                                                {{ latestRequest.status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div v-else class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Viss kārtībā</h3>
                                <p class="text-sm text-gray-500">Tav nav jaunu iesniegumu šobrīd!</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Button 
                    <div class="flex justify-center">
                        <a 
                            :href="latestRequest ? '/user/requests/' + latestRequest.id : '/user/requests'" 
                            class="w-full sm:w-2/3 flex items-center justify-between px-6 py-4 rounded-lg shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :class="latestRequest ? 'bg-blue-500 hover:bg-blue-600 text-white focus:ring-blue-500' : 'bg-gray-500 hover:bg-gray-600 text-white focus:ring-gray-500'"
                        >
                            <span class="font-semibold">{{ latestRequest ? latestRequest.title : 'Skatīt visus' }}</span>
                            <div class="flex items-center">
                                <span v-if="latestRequest" class="hidden sm:inline-block mr-4 text-sm text-gray-100">{{ latestRequest.due_date }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </a>
                    </div>-->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>