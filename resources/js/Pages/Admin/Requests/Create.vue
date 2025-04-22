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
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h1 class="text-2xl font-bold text-gray-800">Izveidot Jaunu Iesniegumu</h1>
        </div>
        
        <div class="p-6">
          <form @submit.prevent="submitRequest" class="space-y-6 max-w-lg">
            <div>
              <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Piešķirt lietotājam</label>
              <select
                id="user_id"
                v-model="form.user_id"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
              >
                <option value="">-- Izvēlies --</option>
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
              <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tituls</label>
              <input
                type="text"
                id="title"
                v-model="form.title"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                placeholder="piem. 'Par Maiju'"
              />
              <InputError :message="form.errors.title" class="mt-2" />
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Apraksts</label>
              <textarea
                id="description"
                v-model="form.description"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                rows="4"
                placeholder="Optional details about the document request..."
              ></textarea>
              <InputError :message="form.errors.description" class="mt-2" />
            </div>

            <div>
              <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Termmiņš</label>
              <input
                type="date"
                id="deadline"
                v-model="form.deadline"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md"
              />
              <InputError :message="form.errors.deadline" class="mt-2" />
            </div>

            <div class="flex items-center justify-between pt-4">
              <Link 
                href="/admin/requests" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to All Requests
              </Link>
              
              <PrimaryButton :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700">
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Creating...
                </span>
                <span v-else>Create Request</span>
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>