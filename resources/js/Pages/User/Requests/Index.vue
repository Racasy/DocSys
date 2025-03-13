<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3'; // router or route
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
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">My Requests</h1>

      <!-- Example filters -->
      <div class="mb-4 space-x-2">
        <button @click="fetchRequests('')" class="bg-gray-300 px-2 py-1">All</button>
        <button @click="fetchRequests('pending')" class="bg-yellow-300 px-2 py-1">Pending</button>
        <button @click="fetchRequests('approved')" class="bg-green-300 px-2 py-1">Approved</button>
        <button @click="fetchRequests('denied')" class="bg-red-300 px-2 py-1">Denied</button>
      </div>

      <table class="min-w-full bg-white">
        <thead>
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Title</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="req in requests" :key="req.id">
            <td class="border px-4 py-2">{{ req.id }}</td>
            <td class="border px-4 py-2">{{ req.title }}</td>
            <td class="border px-4 py-2">{{ req.status }}</td>
            <td class="border px-4 py-2">
              <Link :href="route('user.requests.show', req.id)" class="text-blue-500">View/Upload</Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AuthenticatedLayout>
</template>
