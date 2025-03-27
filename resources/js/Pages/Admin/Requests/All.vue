<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { defineProps, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  requests: {
    type: Array,
    default: () => [],
  }
});

const sort_by = ref('');
const statusFilter = ref('');
const showDeleteConfirm = ref(false);
const deleteRequestId = ref(null);


function filterStatus(status) {
  statusFilter.value = status;
  fetchData();
}

function sortBy(field) {
  sort_by.value = field;
  fetchData();
}

function fetchData() {
  router.get('/admin/requests', {
    status: statusFilter.value,
    sort_by: sort_by.value
  }, { preserveState: true, preserveScroll: true });
}

// Show confirmation modal
function confirmDelete(requestId) {
  deleteRequestId.value = requestId;
  showDeleteConfirm.value = true;
}

// Delete the request
function deleteRequest() {
  router.delete(route('admin.requests.destroy', deleteRequestId.value), {
    onSuccess: () => {
      showDeleteConfirm.value = false;
    }
  });
}

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
  <AdminLayout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header with title and create button -->
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
          <h1 class="text-2xl font-bold text-gray-800">Visi iesniegumi</h1>
          <Link 
            href="/admin/requests/create" 
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Manuāli izveidot iesniegumu
          </Link>
        </div>
        
        <!-- Filter buttons -->
        <div class="p-6 bg-white border-b border-gray-200">
          <div class="flex flex-wrap gap-2">
            <button 
              @click="filterStatus('')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === '' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
            >
              Visi
            </button>
            <button 
              @click="filterStatus('pending')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100'"
            >
              Nav iesniegti
            </button>
            <button 
              @click="filterStatus('in_progress')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === 'in_progress' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'"
            >
              Iesniegti
            </button>
            <button 
              @click="filterStatus('approved')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === 'approved' ? 'bg-green-500 text-white' : 'bg-green-50 text-green-700 hover:bg-green-100'"
            >
              Apstiprināts
            </button>
            <button 
              @click="filterStatus('denied')" 
              class="px-4 py-2 rounded-md font-medium transition-colors duration-200 ease-in-out"
              :class="statusFilter === 'denied' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100'"
            >
              Atteikti
            </button>
          </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th 
                  scope="col" 
                  @click="sortBy('id')"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                >
                  <div class="flex items-center">
                    ID
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="{'text-gray-400': sort_by !== 'id', 'text-gray-700': sort_by === 'id'}">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                  </div>
                </th>
                <th 
                  scope="col" 
                  @click="sortBy('title')"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                >
                  <div class="flex items-center">
                    Tituls
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="{'text-gray-400': sort_by !== 'title', 'text-gray-700': sort_by === 'title'}">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                  </div>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lietotājs</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Darbība</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="req in requests" :key="req.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ req.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ req.title }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ req.user.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                    :class="{
                      'bg-yellow-100 text-yellow-800': req.status === 'pending',
                      'bg-blue-100 text-blue-800': req.status === 'in_progress',
                      'bg-green-100 text-green-800': req.status === 'approved',
                      'bg-red-100 text-red-800': req.status === 'denied',
                      'bg-gray-100 text-gray-800': !['pending', 'in_progress', 'approved', 'denied'].includes(req.status)
                    }">
                    {{ 
                      req.status === 'pending' ? 'Nav iesniegts' :
                      req.status === 'in_progress' ? 'Iesniegts' :
                      req.status === 'approved' ? 'Apstiprināts' :
                      req.status === 'denied' ? 'Atteikts' :
                      req.status
                    }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-3">
                    <Link 
                      :href="route('admin.requests.show', req.id)" 
                      class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                    >
                      View
                    </Link>
                    <button 
                      @click="confirmDelete(req.id)" 
                      class="text-red-600 hover:text-red-900 transition-colors duration-200"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="requests.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                  No requests found
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteConfirm" class="fixed inset-0 overflow-y-auto z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDeleteConfirm = false"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Delete Request
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to delete this request? This action cannot be undone.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button 
              type="button" 
              @click="deleteRequest" 
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Delete
            </button>
            <button 
              type="button" 
              @click="showDeleteConfirm = false" 
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>