<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { defineProps, ref, onMounted, onUnmounted } from 'vue';
import { useForm, router, Head } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
  documentRequest: Object,
});

const uploadForm = useForm({
  comment: '',
  files: null,
});

const commentForm = useForm({
  comment: '',
});

const filesInput = ref(null);
const timeLeft = ref('');
const showSubmitConfirm = ref(false);

function calculateTimeLeft() {
  const deadlineDate = new Date(props.documentRequest.deadline);
  const now = new Date();
  const diff = deadlineDate - now;

  if (diff < 0) {
    timeLeft.value = 'Termiņš ir beidzies';
    return;
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

  const timeParts = [];
  
  if (days > 0) {
    timeParts.push(`${days} ${days === 1 ? 'diena' : 'dienas'}`);
    timeParts.push(`${hours} ${hours === 1 ? 'stunda' : 'stundas'}`);
  } else {
    timeParts.push(`${hours} ${hours === 1 ? 'stunda' : 'stundas'}`);
  }
  
  timeParts.push(`${minutes} ${minutes === 1 ? 'minūte' : 'minūtes'}`);

  // Format the output string
  if (timeParts.length > 2) {
    timeLeft.value = `${timeParts[0]}, ${timeParts[1]} un ${timeParts[2]} atlikušas`;
  } else {
    timeLeft.value = `${timeParts[0]} un ${timeParts[1]} atlikušas`;
  }
}

onMounted(() => {
  calculateTimeLeft();
  const interval = setInterval(calculateTimeLeft, 60000);
  onUnmounted(() => clearInterval(interval));
});

function handleFiles(event) {
  uploadForm.files = event.target.files;
}

function uploadFiles() {
  if (!uploadForm.files || uploadForm.files.length === 0) {
    alert("Lūdzu, izvēlies vismaz vienu failu.");
    return;
  }

  uploadForm.post(route('user.requests.upload', props.documentRequest.id), {
    forceFormData: true,
    onSuccess: () => {
      uploadForm.reset('comment', 'files');
      if (filesInput.value) filesInput.value.value = '';
    },
  });
}

function submitComment() {
  commentForm.post(route('user.requests.comment', props.documentRequest.id), {
    preserveScroll: true,
    onSuccess: () => commentForm.reset('comment'),
  });
}

function deleteDoc(id) {
  if (!confirm('Vai tiešām vēlies dzēst šo dokumentu?')) return;

  router.delete(route('user.documents.destroy', id), {
    preserveScroll: true,
    onError: () => alert('Neizdevās dzēst dokumentu')
  });
}

function showSubmitConfirmation() {
  showSubmitConfirm.value = true;
}

function confirmSubmit() {
  router.post(
    route('user.requests.submit', props.documentRequest.id),
    {}, // Add empty data object
    {
      onSuccess: () => {
        showSubmitConfirm.value = false;
      },
      onError: (errors) => {
        console.error('Submit error:', errors);
        showSubmitConfirm.value = false;
      },
      onFinish: () => {
        showSubmitConfirm.value = false;
      }
    }
  );
}

function cancelSubmit() {
  showSubmitConfirm.value = false;
}

function handleModalBackdropClick(event) {
  if (event.target === event.currentTarget) {
    showSubmitConfirm.value = false;
  }
}

function getStatusClass(status) {
  switch(status) {
    case 'pending': return 'bg-yellow-100 text-yellow-800';
    case 'in_progress': return 'bg-blue-100 text-blue-800';
    case 'approved': return 'bg-green-100 text-green-800';
    case 'denied': return 'bg-red-100 text-red-800';
    default: return 'bg-gray-100 text-gray-800';
  }
}

function getStatusTranslation(status) {
  switch(status) {
    case 'pending': return 'Nav iesniegts';
    case 'in_progress': return 'Iesniegts';
    case 'approved': return 'Apstiprināts';
    case 'denied': return 'Noraidīts';
    default: return status;
  }
}
</script>

<template>
  <Head title="Iesniegumi" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <!-- Request Info Card -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
          <h1 class="text-2xl font-bold text-gray-800">
            Iesniegums #{{ documentRequest.id }}
          </h1>
          <div class="flex items-center space-x-4">
            <span
              class="px-3 py-1 rounded-full text-sm font-medium"
              :class="getStatusClass(documentRequest.status)"
            >
              {{ getStatusTranslation(documentRequest.status) }}
            </span>
            <!-- Top Submit Button -->
            <button
              v-if="documentRequest.status === 'pending' && documentRequest.documents?.length"
              @click="showSubmitConfirmation"
              class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow-sm transition-colors flex items-center"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Iesniegt dokumentus
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-md p-4">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">
                Tituls
              </h3>
              <p class="text-lg font-medium text-gray-900">
                {{ documentRequest.title }}
              </p>
            </div>
            <div class="bg-gray-50 rounded-md p-4">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">
                Termiņš
              </h3>
              <p class="text-lg font-medium text-gray-900">
                {{ documentRequest.deadline }}
                <span v-if="timeLeft"> ({{ timeLeft }}) </span>
              </p>
            </div>
          </div>
          <div v-if="documentRequest.submitted_at" class="bg-gray-50 rounded-md p-4">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">
              Iesniegts
            </h3>
            <p class="text-lg font-medium text-gray-900">
              {{ documentRequest.submitted_at }}
            </p>
          </div>
        </div>
      </div>

      <!-- Uploaded Documents Card -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-xl font-semibold text-gray-800">
            Augšupielādētie dokumenti
          </h2>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nosaukums</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Darbības</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="doc in documentRequest.documents"
                :key="doc.id"
                class="hover:bg-gray-50"
              >
                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                  {{ doc.id }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  {{ doc.file_name }}
                </td>
                <td class="px-6 py-4 text-sm">
                  <a
                    :href="route('documents.download', doc.id)"
                    target="_blank"
                    class="text-blue-600 hover:underline"
                  >
                    Skatīt
                  </a>
                  <button
                    v-if="documentRequest.status === 'pending'"
                    @click="deleteDoc(doc.id)"
                    class="ml-4 text-red-600 hover:underline"
                  >
                    Dzēst
                  </button>
                </td>
              </tr>
              <tr v-if="!documentRequest.documents?.length">
                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                  Nekas nav augšupielādēts
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Comments Section -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-xl font-semibold text-gray-800">Komentāri</h2>
        </div>
        <div class="p-6 space-y-4">
          <div v-if="documentRequest.comments?.length" class="space-y-4">
            <div
              v-for="comment in documentRequest.comments"
              :key="comment.id"
              class="bg-gray-50 rounded-md p-4"
            >
              <p class="font-medium text-gray-700">{{ comment.user.name }}</p>
              <p class="text-gray-600">{{ comment.comment }}</p>
              <p class="text-xs text-gray-400">{{ comment.created_at }}</p>
            </div>
          </div>
          <p v-else class="text-gray-500 italic">Nav komentāru</p>

          <!-- Comment Form -->
          <form @submit.prevent="submitComment" class="space-y-4">
            <div>
              <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                Pievienot komentāru
              </label>
              <textarea
                id="comment"
                v-model="commentForm.comment"
                rows="3"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                placeholder="Raksti savu komentāru šeit"
              ></textarea>
              <InputError :message="commentForm.errors.comment" class="mt-2" />
            </div>
            <PrimaryButton :disabled="commentForm.processing">
              <span v-if="commentForm.processing">Notiek...</span>
              <span v-else>Pievienot</span>
            </PrimaryButton>
          </form>
        </div>
      </div>

      <!-- Upload not available notice -->
      <div
        v-if="documentRequest.status !== 'pending'"
        class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 p-4 rounded mb-6"
      >
        <p class="font-semibold">Augšupielāde nav pieejama</p>
        <p>
          Dokumentu vairs nevar augšupielādēt, jo iesnieguma statuss ir
          <strong>{{ getStatusTranslation(documentRequest.status) }}</strong>.
        </p>
      </div>

      <!-- Upload Form (only if pending) -->
      <div v-if="documentRequest.status === 'pending'" class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h2 class="text-xl font-semibold text-gray-800">Augšupielādēt dokumentu</h2>
        </div>
        <div class="p-6">
          <form @submit.prevent="uploadFiles" class="space-y-6">
            <div>
              <label for="files" class="block text-sm font-medium text-gray-700 mb-2">
                Izvēlies failus
              </label>
              <div class="flex items-center">
                <label
                  class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer"
                >
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
                <span class="ml-3 text-sm text-gray-500" v-if="uploadForm.files?.length">
                  {{ uploadForm.files.length }} failu izvēlēti
                </span>
              </div>
              <InputError :message="uploadForm.errors?.[0]?.message" class="mt-2" />
            </div>
            <div>
              <PrimaryButton :disabled="uploadForm.processing" class="w-full sm:w-auto">
                <span v-if="uploadForm.processing" class="flex items-center">
                  <svg
                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      class="opacity-25"
                      cx="12"  
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"
                    />
                    <path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    />
                  </svg>
                  Augšupielādējas...
                </span>
                <span v-else>Augšupielādēt</span>
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>

      <!-- Bottom "Iesniegt" button (enhanced) -->
      <div
        v-if="documentRequest.status === 'pending' && documentRequest.documents?.length"
        class="mt-6"
      >
        <button
          @click="showSubmitConfirmation"
          class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md shadow-md transition-colors flex items-center justify-center"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Iesniegt dokumentus
        </button>
        <p class="text-center text-sm text-gray-500 mt-2">
          Pēc iesniegšanas vairs nevarēsiet veikt izmaiņas
        </p>
      </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div v-if="showSubmitConfirm" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50" @click="handleModalBackdropClick">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4 animate-modal" @click.stop>
        <div class="flex items-center justify-center mb-4 text-green-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 text-center mb-1">Iesniegt dokumentus</h3>
        <p class="text-center text-gray-600 mb-6">
          Vai tiešām vēlaties iesniegt šos dokumentus? Pēc iesniegšanas vairs nevarēsiet augšupielādēt vai dzēst dokumentus.
        </p>
        
        <div class="flex justify-center space-x-4">
          <button 
            @click="cancelSubmit" 
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
          >
            Atcelt
          </button>
          <button 
            @click="confirmSubmit" 
            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
          >
            Iesniegt
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.animate-modal {
  animation: modalEnter 0.3s ease-out;
}

@keyframes modalEnter {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>