<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  documentRequest: Object,
});

// Data for upload form
const form = useForm({
  comment: '',
  files: null, // We'll append the actual File objects at submit
});

const filesInput = ref(null);

function submitForm() {
  // We need to create a FormData behind the scenes
  // But `useForm` does that for us if we pass an object or call form.post with formData
  const data = new FormData();

  // Extract files
  const files = filesInput.value?.files;
  if (files) {
    for (let i = 0; i < files.length; i++) {
      data.append('files[]', files[i]);
    }
  }

  data.append('comment', form.comment);

  // Make the request
  form.post(route('user.requests.upload', props.documentRequest.id), {
    data,
    forceFormData: true,
    // We can handle onSuccess, onError, etc. if needed
    onSuccess: () => {
      // Reset the comment or do any UI feedback
      form.reset('comment');
      if (filesInput.value) filesInput.value.value = ''; // clear file input
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

      <!-- Upload form -->
      <div v-if="documentRequest.status !== 'approved'" class="mt-4">
        <h2 class="text-xl font-semibold">Upload Documents</h2>
        <form @submit.prevent="submitForm" class="mt-4 space-y-4">
          <div>
            <input
              type="file"
              ref="filesInput"
              multiple
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
