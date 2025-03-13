<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { defineProps, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  documentRequest: Object
});

// Approve form (could be an empty form or minimal data)
const approveForm = useForm({});

// Deny form, includes a comment field
const denyForm = useForm({
  comment: ''
});

function approveRequest() {
  approveForm.post(route('admin.requests.approve', props.documentRequest.id), {
    onSuccess: () => {
      // Possibly do something on success
    },
  });
}

function denyRequest() {
  denyForm.post(route('admin.requests.deny', props.documentRequest.id), {
    onSuccess: () => {
      denyForm.reset('comment');
    }
  });
}

// Generate file URL
function getFileUrl(filePath) {
  const filename = filePath.split('/').pop(); // Extract only the filename
  return route('admin.documents.view', filename);
}


</script>

<template>
  <AdminLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Request #{{ documentRequest.id }}</h1>
      <p><strong>Title:</strong> {{ documentRequest.title }}</p>
      <p><strong>Status:</strong> {{ documentRequest.status }}</p>
      <p><strong>Deadline:</strong> {{ documentRequest.deadline }}</p>
      <p><strong>User:</strong> {{ documentRequest.user.name }}</p>

      <h2 class="text-xl font-semibold mt-4">Uploaded Documents</h2>
      <table class="min-w-full bg-white mt-2">
        <thead>
          <tr>
            <th class="px-4 py-2">ID</th>
            <th class="px-4 py-2">Filename</th>
            <th class="px-4 py-2">Actions</th>
            <th class="px-4 py-2">Comments</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="doc in documentRequest.documents" :key="doc.id">
            <td class="border px-4 py-2">{{ doc.id }}</td>
            <td class="border px-4 py-2">{{ doc.file_name }}</td>
            <td class="border px-4 py-2">
              <a
                :href="getFileUrl(doc.file_path)"
                target="_blank"
                class="text-blue-500 hover:underline"
              >
                Open File
              </a>
            </td>
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

      <div v-if="['pending','in_progress'].includes(documentRequest.status)" class="mt-4 space-x-2">
        <!-- APPROVE -->
        <PrimaryButton :disabled="approveForm.processing" @click="approveRequest">
          {{ approveForm.processing ? 'Approving...' : 'Approve' }}
        </PrimaryButton>

        <!-- DENY -->
        <form @submit.prevent="denyRequest" class="inline-block ml-4">
          <input
            type="text"
            v-model="denyForm.comment"
            placeholder="Reason (optional)"
            class="border px-2 py-1"
          />
          <InputError :message="denyForm.errors.comment" class="mt-2" />

          <PrimaryButton :disabled="denyForm.processing" class="bg-red-500 ml-2">
            {{ denyForm.processing ? 'Denying...' : 'Deny' }}
          </PrimaryButton>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
