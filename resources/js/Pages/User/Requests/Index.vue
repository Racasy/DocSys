<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    requests: Object,
    filters: Object,
});

// State for search, filter, and sort
const searchQuery = ref(props.filters.searchQuery || '');
const sortOption = ref(props.filters.sortOption || 'created_at');
const filterOption = ref(props.filters.filterOption || 'all');

// Watch for changes and update the URL query parameters
watch([searchQuery, filterOption, sortOption], () => {
    router.get(
        route('user.requests.index'),
        {
            searchQuery: searchQuery.value,
            filterOption: filterOption.value,
            sortOption: sortOption.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
});

function getStatusClass(status) {
    switch(status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'in_progress': return 'bg-blue-100 text-blue-800';
        case 'approved': return 'bg-green-100 text-green-800';
        case 'denied': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusText(status) {
    return {
        pending: 'Nav iesniegts',
        in_progress: 'Iesniegts',
        approved: 'Apstiprināts',
        denied: 'Noraidīts'
    }[status] || status;
}
</script>

<template>
  <Head title="Iesniegumi" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
          <h1 class="text-2xl font-bold text-gray-800">Mani iesniegumi</h1>
        </div>

        <div class="p-6">
          <!-- Search and filters -->
          <div class="mb-6 space-y-4">
            <!-- Search input -->
            <div class="relative">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <input
                type="text"
                v-model="searchQuery"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                placeholder="Meklēt iesniegumus..."
              />
            </div>

            <!-- Filters and Sort -->
            <div class="flex flex-col sm:flex-row gap-4">
              <!-- Filter dropdown -->
              <div class="w-full sm:w-1/2">
                <label for="filter" class="block mb-2 text-sm font-medium text-gray-900">Statuss</label>
                <select
                  id="filter"
                  v-model="filterOption"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                >
                  <option value="all">Visi</option>
                  <option value="pending">Nav iesniegts</option>
                  <option value="in_progress">Iesniegts</option>
                  <option value="approved">Apstiprināts</option>
                  <option value="denied">Noraidīts</option>
                </select>
              </div>

              <!-- Sort dropdown -->
              <div class="w-full sm:w-1/2">
                <label for="sort" class="block mb-2 text-sm font-medium text-gray-900">Kārtot pēc</label>
                <select
                  id="sort"
                  v-model="sortOption"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                >
                  <option value="created_at">Jaunākie pirmie</option>
                  <option value="created_at-desc">Vecākie pirmie</option>
                  <option value="deadline">Tuvākais termiņš</option>
                  <option value="deadline-desc">Tālākais termiņš</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Results count -->
          <div class="mb-4 text-sm text-gray-600">
            Atrasti {{ requests.total }} iesniegumi
          </div>

          <div v-if="requests.data.length === 0" class="text-center py-8 text-gray-500">
            Nav atrasts neviens iesniegums
          </div>

          <div v-else class="grid grid-cols-1 gap-4">
            <Link
              v-for="request in requests.data"
              :key="request.id"
              :href="route('user.requests.show', request.id)"
              class="block bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition duration-200 overflow-hidden"
            >
              <div class="p-5">
                <div class="flex items-center justify-between mb-2">
                  <h3 class="text-lg font-semibold text-gray-800 truncate">{{ request.title }}</h3>
                  <span class="px-2 py-1 text-sm rounded-full" :class="getStatusClass(request.status)">
                    {{ getStatusText(request.status) }}
                  </span>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                  <p class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Izveidots: {{ new Date(request.created_at).toLocaleDateString('lv-LV') }}
                  </p>
                  <p class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Termiņš: {{ new Date(request.deadline).toLocaleDateString('lv-LV') }}
                  </p>
                  <p class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Dokumenti: {{ request.documents_count }}
                  </p>
                </div>
              </div>
            </Link>
          </div>

          <!-- Pagination Links -->
          <div class="mt-6 flex justify-center">
            <div class="flex space-x-2">
              <template v-for="link in requests.links" :key="link.label">
                <Link
                  v-if="link.url"
                  :href="link.url"
                  v-html="link.label"
                  class="px-4 py-2 border rounded-lg text-sm font-medium"
                  :class="{
                    'bg-blue-500 text-white': link.active,
                    'bg-gray-100 text-gray-700': !link.active,
                  }"
                />
                <span
                  v-else
                  class="px-4 py-2 border rounded-lg text-sm font-medium bg-gray-50 text-gray-400 cursor-not-allowed"
                  v-html="link.label"
                />
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>