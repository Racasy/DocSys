<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { defineProps, ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  documentRequest: Object
});

const MAX_STAMPS = 5;

// Store current edit mode per document
const editMode = ref({});

// Form for stamps
const stampsForm = ref({});

// Initialize forms for each document
props.documentRequest.documents.forEach(doc => {
  // Initialize stamp forms for each document
  stampsForm.value[doc.id] = {
    edit_mode: false,
    stamps: doc.stamps && doc.stamps.length > 0 
      ? [...doc.stamps.map(s => ({
          debit: s.debit_account,
          credit: s.credit_account,
          amount: s.amount
        }))]
      : [{ debit: '', credit: '', amount: '' }]
  };
  
  // Set edit mode state
  editMode.value[doc.id] = false;
});

// Loading state for each document
const loading = ref({});

// Computed property to determine if max stamps reached for a document
const hasMaxStamps = (docId) => {
  return stampsForm.value[docId].stamps.length >= MAX_STAMPS;
};

// Add a new empty stamp to a document
function addNewStamp(docId) {
  if (stampsForm.value[docId].stamps.length < MAX_STAMPS) {
    stampsForm.value[docId].stamps.push({ debit: '', credit: '', amount: '' });
  }
}

// Remove a stamp from a document
function removeStamp(docId, index) {
  if (stampsForm.value[docId].stamps.length > 1) {
    stampsForm.value[docId].stamps.splice(index, 1);
  }
}

// Toggle edit mode for a document
function toggleEditMode(docId) {
  editMode.value[docId] = !editMode.value[docId];
  
  // If switching to edit mode, update the form with current stamps
  if (editMode.value[docId]) {
    const doc = props.documentRequest.documents.find(d => d.id === docId);
    if (doc.stamps && doc.stamps.length > 0) {
      stampsForm.value[docId].stamps = doc.stamps.map(s => ({
        debit: s.debit_account,
        credit: s.credit_account,
        amount: s.amount
      }));
    } else {
      stampsForm.value[docId].stamps = [{ debit: '', credit: '', amount: '' }];
    }
    stampsForm.value[docId].edit_mode = true;
  } else {
    stampsForm.value[docId].edit_mode = false;
  }
}

// Function to stamp document
function stampDocument(docId) {
  loading.value[docId] = true;

  const routeName = editMode.value[docId] 
    ? 'admin.documents.edit-stamps' 
    : 'admin.documents.stamp';

  router.post(route(routeName, docId), stampsForm.value[docId], {
    preserveScroll: true,
    onSuccess: () => {
      alert('Dokuments apzīmogots!');
      location.reload();
    },
    onError: (errors) => {
      console.error(errors);
      alert('Neizdevās apzīmogot dokumentu');
    },
    onFinish: () => {
      loading.value[docId] = false;
    }
  });
}

// Approve form
const approveForm = useForm({});

// Deny form, includes a comment field
// const denyForm = useForm({
//   comment: ''
// });

function approveRequest() {
  approveForm.post(route('admin.requests.approve', props.documentRequest.id), {
    onSuccess: () => {
      // Possibly do something on success
    },
  });
}

const showDenyDialog = ref(false);
const denyReason = ref('');

const denyRequest = () => {
  if (!denyReason.value.trim()) {
    alert('Lūdzu, ievadiet noraidījuma iemeslu!');
    return;
  }

  console.log('Attempting to deny request:', {
    route: route('admin.requests.deny', props.documentRequest.id),
    reason: denyReason.value
  });

  router.post(
    route('admin.requests.deny', props.documentRequest.id),
    { reason: denyReason.value },
    {
      preserveScroll: true,
      onSuccess: (page) => {
        console.log('Request denial successful:', page);
        showDenyDialog.value = false;
        denyReason.value = '';
        alert('Pieprasījums noraidīts!');
      },
      onError: (errors) => {
        console.error('Denial request errors:', errors);
        alert('Kļūda: ' + (errors.reason || errors.message || 'Noraidījums neizdevās'));
      },
    }
  );
};

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
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Uzņēmums</h3>
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
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zīmogi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="doc in documentRequest.documents" :key="doc.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ doc.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ doc.file_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <a :href="route('admin.documents.download', doc.id)" target="_blank" class="text-blue-600 hover:underline">Skatīt</a>
                </td>
                <td class="px-6 py-4">
                  <!-- Display existing stamps with edit button -->
                  <div v-if="doc.stamps && doc.stamps.length > 0 && !editMode[doc.id]">
                    <div v-for="(stamp, idx) in doc.stamps" :key="idx" class="mb-2 p-2 bg-gray-50 rounded">
                      <p><strong>Zīmogs #{{ idx + 1 }}</strong></p>
                      <p>D: {{ stamp.debit_account }}</p>
                      <p>K: {{ stamp.credit_account }}</p>
                      <p>Summa: {{ stamp.amount }} EUR</p>
                    </div>
                    <button @click="toggleEditMode(doc.id)" class="mt-2 px-3 py-1 bg-yellow-500 text-white rounded">
                      Rediģēt zīmogus
                    </button>
                  </div>
                  
                  <!-- Stamp form (either for new stamps or editing existing ones) -->
                  <div v-else>
                    <div v-for="(stamp, idx) in stampsForm[doc.id].stamps" :key="idx" class="mb-4 p-3 border rounded bg-gray-50">
                      <div class="flex justify-between items-center mb-2">
                        <h4 class="font-medium">Zīmogs #{{ idx + 1 }}</h4>
                        <button 
                          v-if="stampsForm[doc.id].stamps.length > 1" 
                          @click="removeStamp(doc.id, idx)" 
                          type="button" 
                          class="text-red-500 hover:text-red-700"
                        >
                          Dzēst
                        </button>
                      </div>
                      
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">Debets</label>
                          <input v-model="stamp.debit" placeholder="D" class="border px-2 py-1 w-full rounded" />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">Kredīts</label>
                          <input v-model="stamp.credit" placeholder="K" class="border px-2 py-1 w-full rounded" />
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-700 mb-1">Summa</label>
                          <input v-model="stamp.amount" placeholder="Summa" class="border px-2 py-1 w-full rounded" type="number" step="0.01" />
                        </div>
                      </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                      <button 
                        v-if="!hasMaxStamps(doc.id)" 
                        @click="addNewStamp(doc.id)" 
                        type="button"
                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                      >
                        + Pievienot zīmogu
                      </button>
                      
                      <button 
                        @click="stampDocument(doc.id)" 
                        :disabled="loading[doc.id]" 
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
                      >
                        <span v-if="loading[doc.id]">Apzīmogo...</span>
                        <span v-else>{{ editMode[doc.id] ? 'Saglabāt zīmogus' : 'Apzīmogot' }}</span>
                      </button>
                      
                      <button 
                        v-if="editMode[doc.id]" 
                        @click="toggleEditMode(doc.id)" 
                        type="button"
                        class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600"
                      >
                        Atcelt
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
              <tr v-if="!documentRequest.documents || documentRequest.documents.length === 0">
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
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
            <div class="flex-shrink-0">
              <PrimaryButton 
                :disabled="approveForm.processing" 
                @click="approveRequest"
                class="mb-3 sm:mb-0 bg-green-500 hover:bg-green-600 focus:ring-green-500"
              >
                <template v-if="approveForm.processing">
                  <span class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <!-- Spinner SVG -->
                    </svg>
                    Apstiprina...
                  </span>
                </template>
                <template v-else>
                  Apstiprināt
                </template>
              </PrimaryButton>
            </div>

            <!-- DENY -->
            <div class="flex-1">
              <PrimaryButton 
                @click="showDenyDialog = true"
                class="bg-red-500 hover:bg-red-600 focus:ring-red-500"
              >
                Noraidīt
              </PrimaryButton>
              <div v-if="showDenyDialog" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
                <div class="p-4 bg-white rounded-lg shadow-xl max-w-md w-full">
                  <h2 class="text-lg font-bold mb-2">Noraidīt pieprasījumu</h2>
                  <textarea
                    v-model="denyReason"
                    rows="4"
                    class="w-full p-2 border rounded mb-3"
                    placeholder="Iemesls..."
                  ></textarea>
                  <div class="flex justify-end gap-2">
                    <button
                      @click="showDenyDialog = false"
                      class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded"
                    >
                      Atcelt
                    </button>
                    <button
                      @click="denyRequest"
                      class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                    >
                      Apstiprināt
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>