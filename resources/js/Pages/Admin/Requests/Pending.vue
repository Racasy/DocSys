<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  requests: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <AdminLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Pending Requests</h1>

      <table class="min-w-full bg-white">
        <thead>
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Title</th>
            <th class="px-4 py-2">User</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="req in requests" :key="req.id">
            <td class="border px-4 py-2">{{ req.id }}</td>
            <td class="border px-4 py-2">{{ req.title }}</td>
            <td class="border px-4 py-2">{{ req.user.name }}</td>
            <td class="border px-4 py-2">
              <Link :href="route('admin.requests.show', req.id)" class="text-blue-500">View</Link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>
