<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  requests: {
    type: Array,
    default: () => []
  }
});

// For demonstration, we show how you might handle filtering
const statusFilter = ref('');

function fetchRequests(status) {
  statusFilter.value = status;
  // Example server-side filter usage:
  router.get(route('user.requests.index'), { status: statusFilter.value }, {
    preserveState: true,
    preserveScroll: true
  });
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h1 class="text-2xl font-bold text-gray-800">Mani iesniegumi</h1>
        </div>

        <!-- Filter buttons -->
        <div class="p-6 bg-white border-b border-gray-200">
          <div class="flex flex-wrap gap-2">
            <button 
              @click="fetchRequests('')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === '' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            >
              Visi
            </button>
            <button 
              @click="fetchRequests('pending')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100'"
            >
              Procesā
            </button>
            <button 
              @click="fetchRequests('approved')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === 'approved' ? 'bg-green-500 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100'"
            >
              Apstiprināts
            </button>
            <button 
              @click="fetchRequests('denied')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === 'denied' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100'"
            >
              Atteikti
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tituls</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Darbība</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="req in requests" :key="req.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ req.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ req.title }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                    :class="{
                      'bg-yellow-100 text-yellow-800': req.status === 'pending',
                      'bg-green-100 text-green-800': req.status === 'approved',
                      'bg-red-100 text-red-800': req.status === 'denied',
                      'bg-gray-100 text-gray-800': !['pending', 'approved', 'denied'].includes(req.status)
                    }">
                    {{ req.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <Link 
                    :href="route('user.requests.show', req.id)" 
                    class="text-indigo-600 hover:text-indigo-900 font-medium"
                  >
                    Skatīt
                  </Link>
                </td>
              </tr>
              <tr v-if="requests.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                  Iesniegumi nav atrasti
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>