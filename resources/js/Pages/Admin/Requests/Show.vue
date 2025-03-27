<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { defineProps, ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  documentRequest: Object
});

const form = ref({})
const loading = ref({})

props.documentRequest.documents.forEach(doc => {
  form.value[doc.id] = {
    debit: doc.stamp?.debit_account || '',
    credit: doc.stamp?.credit_account || '',
    amount: doc.stamp?.amount || '',
    comment: doc.stamp?.comment || '',
  }
})

function stampDocument(docId) {
  loading.value[docId] = true

  router.post(route('admin.documents.stamp', docId), form.value[docId], {
    preserveScroll: true,
    onSuccess: () => {
      alert('Dokuments apzīmogots!')
      location.reload()
    },
    onError: () => alert('Neizdevās apzīmogot dokumentu'),
    onFinish: () => {
      loading.value[docId] = false
    }
  })
}

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
            <div class="bg-gray-50 rounded-md p-4">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Lietotājs</h3>
              <p class="text-lg font-medium text-gray-900">{{ documentRequest.user.name }}</p>
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
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Darbības</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Komentārs</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="doc in documentRequest.documents" :key="doc.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ doc.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ doc.file_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <a :href="route('admin.documents.download', doc.id)" target="_blank" class="text-blue-600 hover:underline">Skatīt</a>
                </td>
                <td>
                  <input v-model="form[doc.id].debit" placeholder="D" class="border px-2 py-1 w-20 rounded" />
                  <input v-model="form[doc.id].credit" placeholder="K" class="border px-2 py-1 w-20 rounded" />
                  <input v-model="form[doc.id].amount" placeholder="Summa" class="border px-2 py-1 w-24 rounded" type="number" />
                  <textarea v-model="form[doc.id].comment" placeholder="Komentārs" class="w-full mt-1 border px-2 py-1 rounded"></textarea>

                  <button @click="stampDocument(doc.id)" :disabled="loading[doc.id]" class="mt-2 px-3 py-1 bg-blue-600 text-white rounded">
                    <span v-if="loading[doc.id]">Apzīmogo...</span>
                    <span v-else>Apzīmogot</span>
                  </button>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  <div v-if="doc.comments && doc.comments.length > 0" class="space-y-2">
                    <div v-for="c in doc.comments" :key="c.id" class="bg-gray-50 rounded-md p-2">
                      <p class="font-medium text-gray-700">{{ c.user.name }}</p>
                      <p class="text-gray-600">{{ c.comment }}</p>
                    </div>
                  </div>
                  <p v-else class="text-gray-400 italic">Nav komentārs</p>
                </td>
              </tr>
              <tr v-if="!documentRequest.documents || documentRequest.documents.length === 0">
                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                  Nav nekas augšupielādēts
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Approval Actions Card -->
      <div v-if="['pending','in_progress'].includes(documentRequest.status)" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-xl font-semibold text-gray-800">Darbības</h2>
        </div>
        
        <div class="p-6 space-y-4">
          <div class="flex flex-col sm:flex-row sm:space-x-4">
            <!-- APPROVE -->
            <PrimaryButton 
              :disabled="approveForm.processing" 
              @click="approveRequest"
              class="mb-3 sm:mb-0"
            >
              <span v-if="approveForm.processing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Approving...
              </span>
              <span v-else>Approve</span>
            </PrimaryButton>

            <!-- DENY -->
            <form @submit.prevent="denyRequest" class="flex flex-col sm:flex-row sm:items-start">
              <div class="flex-grow mb-3 sm:mb-0 sm:mr-3">
                <textarea
                  v-model="denyForm.comment"
                  rows="2"
                  placeholder="Reason for denial (optional)"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                ></textarea>
                <InputError :message="denyForm.errors.comment" class="mt-2" />
              </div>
              
              <PrimaryButton 
                :disabled="denyForm.processing" 
                class="bg-red-500 hover:bg-red-600 focus:ring-red-500"
              >
                <span v-if="denyForm.processing" class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Denying...
                </span>
                <span v-else>Deny</span>
              </PrimaryButton>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>