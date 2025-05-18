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

function getStatusClass(status) {
  switch(status) {
    case 'pending': return 'bg-yellow-100 text-yellow-800';
    case 'approved': return 'bg-green-100 text-green-800';
    case 'denied': return 'bg-red-100 text-red-800';
    default: return 'bg-gray-100 text-gray-800';
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
          <span
            class="px-3 py-1 rounded-full text-sm font-medium"
            :class="getStatusClass(documentRequest.status)"
          >
            {{ documentRequest.status }}
          </span>
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
          <strong>{{ documentRequest.status }}</strong>.
        </p>
      </div>

      <!-- Upload Form (only if pending) -->
      <div v-if="documentRequest.status === 'pending'" class="bg-white rounded-lg shadow-md overflow-hidden">
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

      <!-- "Iesniegt" button: finalize request -->
      <div
        v-if="documentRequest.status === 'pending' && documentRequest.documents?.length"
        class="mt-6 flex"
      >
        <form :action="route('user.requests.submit', documentRequest.id)" method="POST">
          <PrimaryButton
            @click.prevent="() => router.post(route('user.requests.submit', documentRequest.id))"
          >
            Iesniegt
          </PrimaryButton>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>