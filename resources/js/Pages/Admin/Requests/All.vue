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
</script>

<template>
  <AdminLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">All Requests</h1>
      <Link href="/admin/requests/create" class="bg-brandPurple text-white px-4 py-2">
        Create New Request
      </Link>

      <!-- Filter buttons -->
      <div class="mb-4 space-x-2">
        <button @click="filterStatus('')" class="bg-gray-300 px-2 py-1">All</button>
        <button @click="filterStatus('pending')" class="bg-yellow-300 px-2 py-1">Pending</button>
        <button @click="filterStatus('approved')" class="bg-green-300 px-2 py-1">Approved</button>
        <button @click="filterStatus('denied')" class="bg-red-300 px-2 py-1">Denied</button>
      </div>

      <table class="min-w-full bg-white">
        <thead>
          <tr>
            <th class="px-4 py-2 cursor-pointer" @click="sortBy('id')">ID</th>
            <th class="px-4 py-2 cursor-pointer" @click="sortBy('title')">Title</th>
            <th class="px-4 py-2">User</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="req in requests" :key="req.id">
            <td class="border px-4 py-2">{{ req.id }}</td>
            <td class="border px-4 py-2">{{ req.title }}</td>
            <td class="border px-4 py-2">{{ req.user.name }}</td>
            <td class="border px-4 py-2">{{ req.status }}</td>
            <td class="border px-4 py-2 space-x-2">
              <Link :href="route('admin.requests.show', req.id)" class="text-blue-500">View</Link>
              <button @click="confirmDelete(req.id)" class="text-red-500">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteConfirm" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg">
          <h2 class="text-lg font-semibold mb-4">Are you sure?</h2>
          <p>This action cannot be undone.</p>
          <div class="mt-4 space-x-2">
            <button @click="deleteRequest" class="bg-red-500 text-white px-4 py-2">Delete</button>
            <button @click="showDeleteConfirm = false" class="bg-gray-300 px-4 py-2">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
