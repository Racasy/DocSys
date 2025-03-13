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
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Request #{{ documentRequest.id }}</h1>
      <p><strong>Title:</strong> {{ documentRequest.title }}</p>
      <p><strong>Status:</strong> {{ documentRequest.status }}</p>
      <p><strong>Deadline:</strong> {{ documentRequest.deadline }}</p>

      <!-- Uploaded Documents Table -->
      <h2 class="text-xl font-semibold mt-4">Uploaded Documents</h2>
      <table class="min-w-full bg-white mt-2">
        <thead>
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Filename</th>
            <th class="px-4 py-2">Comments</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doc in documentRequest.documents" :key="doc.id">
            <td class="border px-4 py-2">{{ doc.id }}</td>
            <td class="border px-4 py-2">{{ doc.file_name }}</td>
            <td class="border px-4 py-2">
              <ul>
                <li v-for="c in doc.comments" :key="c.id">
                  <strong>{{ c.user.name }}:</strong> {{ c.comment }}
                </li>
              </ul>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Upload Form -->
      <div v-if="documentRequest.status !== 'approved'" class="mt-4">
        <h2 class="text-xl font-semibold">Upload Documents</h2>
        <form @submit.prevent="submitForm" class="mt-4 space-y-4">
          <div>
            <input
              type="file"
              ref="filesInput"
              multiple
              @change="handleFiles"
              class="mb-2 block"
            />
            <InputError :message="form.errors.files" class="mt-2" />
          </div>

          <div>
            <textarea
              v-model="form.comment"
              class="border mb-2 block w-1/2 h-20"
              placeholder="Add a comment (optional)"
            ></textarea>
            <InputError :message="form.errors.comment" class="mt-2" />
          </div>

          <PrimaryButton :disabled="form.processing">
            {{ form.processing ? 'Uploading...' : 'Upload' }}
          </PrimaryButton>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
