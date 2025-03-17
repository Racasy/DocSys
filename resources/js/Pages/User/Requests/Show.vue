<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  documentRequest: Object,
});

// Form for file upload
const form = useForm({
  comment: '',
  files: null, // We'll append actual File objects here
});

const filesInput = ref(null);

// Handle file selection
function handleFiles(event) {
  form.files = event.target.files; // Attach selected files to the form
}

function submitForm() {
  if (!form.files || form.files.length === 0) {
    alert("Please select at least one file.");
    return;
  }

  form.post(route('user.requests.upload', props.documentRequest.id), {
    forceFormData: true, // Ensures multipart/form-data
    onSuccess: () => {
      // Reset form after success
      form.reset('comment', 'files');
      if (filesInput.value) filesInput.value.value = ''; // Clear file input
    },
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
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <!-- Request Info Card -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
          <h1 class="text-2xl font-bold text-gray-800">Iesniegums #{{ documentRequest.id }}</h1>
          <span class="px-3 py-1 rounded-full text-sm font-medium" :class="getStatusClass(documentRequest.status)">
            {{ documentRequest.status }}
          </span>
        </div>
        
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-md p-4">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Tituls</h3>
              <p class="text-lg font-medium text-gray-900">{{ documentRequest.title }}</p>
            </div>
            <div class="bg-gray-50 rounded-md p-4">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Termiņš</h3>
              <p class="text-lg font-medium text-gray-900">{{ documentRequest.deadline }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Uploaded Documents Card -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-xl font-semibold text-gray-800">Augšupielādētie dokumenti</h2>
        </div>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nosaukums</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentārs</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="doc in documentRequest.documents" :key="doc.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ doc.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ doc.file_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  <div v-if="doc.comments && doc.comments.length > 0" class="space-y-2">
                    <div v-for="c in doc.comments" :key="c.id" class="bg-gray-50 rounded-md p-2">
                      <p class="font-medium text-gray-700">{{ c.user.name }}</p>
                      <p class="text-gray-600">{{ c.comment }}</p>
                    </div>
                  </div>
                  <p v-else class="text-gray-400 italic">No comments</p>
                </td>
              </tr>
              <tr v-if="!documentRequest.documents || documentRequest.documents.length === 0">
                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                  Nekas nav augšupielādēts
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Upload Form Card -->
      <div v-if="documentRequest.status !== 'approved'" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-xl font-semibold text-gray-800">Augšupielādēt dokumentu</h2>
        </div>
        
        <div class="p-6">
          <form @submit.prevent="submitForm" class="space-y-6">
            <div>
              <label for="files" class="block text-sm font-medium text-gray-700 mb-2">Izvēlies failus</label>
              <div class="flex items-center">
                <label class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none cursor-pointer">
                  <span>Izvēlies failus</span>
                  <input
                    id="files"
                    type="file"
                    ref="filesInput"
                    multiple
                    @change="handleFiles"
                    class="sr-only"
                  />
                </label>
                <span class="ml-3 text-sm text-gray-500" v-if="form.files && form.files.length > 0">
                  {{ form.files.length }} file(s) selected
                </span>
              </div>
              <InputError :message="form.errors?.[0]?.message" class="mt-2" />
            </div>

            <div>
              <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Komentārs (pēc izvēles)</label>
              <textarea
                id="comment"
                v-model="form.comment"
                rows="3"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                placeholder="Add any notes or comments about the uploaded files"
              ></textarea>
              <InputError :message="form.errors.comment" class="mt-2" />
            </div>

            <div>
              <PrimaryButton :disabled="form.processing" class="w-full sm:w-auto">
                <span v-if="form.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Augšupielādējas...
                </span>
                <span v-else>Augšupielādēt</span>
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>