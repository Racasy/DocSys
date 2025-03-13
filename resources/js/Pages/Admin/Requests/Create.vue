<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  users: {
    type: Array,
    default: () => []
  }
});


const form = useForm({
  user_id: '',
  title: '',
  description: '',
  deadline: '',
});

function submitRequest() {
  form.post(route('admin.requests.store'), {
    onSuccess: () => {
      // Reset
      form.reset();
    },
  });
}
</script>

<template>
  <AdminLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Create New Document Request</h1>

      <form @submit.prevent="submitRequest" class="space-y-4 max-w-lg">

        <div>
          <label for="user_id" class="block font-semibold">Assign to User:</label>
          <select
            id="user_id"
            v-model="form.user_id"
            class="border p-2 w-full"
          >
            <option value="">-- Select User --</option>
            <option
              v-for="u in users"
              :key="u.id"
              :value="u.id"
            >
              {{ u.name }} ({{ u.email }})
            </option>
          </select>
          <InputError :message="form.errors.user_id" class="mt-2" />
        </div>

        <div>
          <label for="title" class="block font-semibold">Title:</label>
          <input
            type="text"
            id="title"
            v-model="form.title"
            class="border p-2 w-full"
            placeholder="e.g., Bank Statement"
          />
          <InputError :message="form.errors.title" class="mt-2" />
        </div>

        <div>
          <label for="description" class="block font-semibold">Description:</label>
          <textarea
            id="description"
            v-model="form.description"
            class="border p-2 w-full"
            rows="3"
            placeholder="Optional details..."
          ></textarea>
          <InputError :message="form.errors.description" class="mt-2" />
        </div>

        <div>
          <label for="deadline" class="block font-semibold">Deadline (optional):</label>
          <input
            type="date"
            id="deadline"
            v-model="form.deadline"
            class="border p-2"
          />
          <InputError :message="form.errors.deadline" class="mt-2" />
        </div>

        <div class="mt-4">
          <PrimaryButton :disabled="form.processing">
            {{ form.processing ? 'Creating...' : 'Create Request' }}
          </PrimaryButton>
        </div>
      </form>

      <div class="mt-6">
        <Link href="/admin/requests" class="text-blue-600">Back to All Requests</Link>
      </div>
    </div>
  </AdminLayout>
</template>
