<template>
    <div class="min-h-screen bg-[#FFEDD8] flex flex-col">
      <!-- Sidebar - Desktop -->
      <aside
        :class="{
          'w-64': !sidebarCollapsed,
          'w-25': sidebarCollapsed,
          'hidden': isMobile && !showMobileMenu,
          'fixed inset-0 z-50': isMobile && showMobileMenu,
        }"
        class="bg-white border-r border-gray-200 transition-all duration-300 ease-in-out flex flex-col fixed h-screen shadow-md"
      >
        <!-- Sidebar Header with Logo -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
          <Link :href="route('adminDashboard')" class="flex items-center space-x-3" v-show="!sidebarCollapsed || isMobile">
            <img src="/images/MainLogo.png" alt="Logo" class="h-8" />
          </Link>
          <Link :href="route('adminDashboard')" class="flex items-center justify-center" v-show="sidebarCollapsed && !isMobile">
            <img src="/images/MainLogo.png" alt="Logo" class="h-8" />
          </Link>
          <button
            @click="toggleSidebar"
            class="text-[#891652] hover:text-[#b91d73] focus:outline-none"
            aria-label="Toggle Sidebar"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                v-if="sidebarCollapsed && !isMobile"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M13 5l7 7-7 7M5 5l7 7-7 7"
              />
              <path
                v-else-if="!isMobile"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
              />
              <path
                v-else
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
  
        <!-- Sidebar Navigation -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-2">
          <Link
            :href="route('adminDashboard')"
            :class="{
              'bg-purple-100 text-[#891652] border-l-4 border-[#891652]': route().current('adminDashboard'),
              'hover:bg-purple-50 hover:text-[#891652]': !route().current('adminDashboard'),
              'justify-center': sidebarCollapsed && !isMobile,
              'justify-start': !sidebarCollapsed || isMobile,
            }"
            class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors group"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
              />
            </svg>
            <span
              :class="{ 'opacity-0 w-0': sidebarCollapsed && !isMobile, 'ml-3': !sidebarCollapsed || isMobile }"
              class="transition-all duration-300"
            >
              Sākums
            </span>
            <span
              v-if="sidebarCollapsed && !isMobile"
              class="absolute left-full rounded-md px-2 py-1 ml-6 bg-purple-100 text-sm text-[#891652] invisible opacity-0 -translate-x-3 group-hover:visible group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"
            >
              Sākums
            </span>
          </Link>
  
          <Link
            :href="route('admin.users.index')"
            :class="{
              'bg-purple-100 text-[#891652] border-l-4 border-[#891652]': route().current('admin.users.index'),
              'hover:bg-purple-50 hover:text-[#891652]': !route().current('admin.users.index'),
              'justify-center': sidebarCollapsed && !isMobile,
              'justify-start': !sidebarCollapsed || isMobile,
            }"
            class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors group"
          >
  
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round"
              stroke-linejoin="round"
              d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
          </svg>
  
            <span
              :class="{ 'opacity-0 w-0': sidebarCollapsed && !isMobile, 'ml-3': !sidebarCollapsed || isMobile }"
              class="transition-all duration-300"
            >
              Uzņēmumi
            </span>
            <span
              v-if="sidebarCollapsed && !isMobile"
              class="absolute left-full rounded-md px-2 py-1 ml-6 bg-purple-100 text-sm text-[#891652] invisible opacity-0 -translate-x-3 group-hover:visible group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"
            >
              Uzņēmumi
            </span>
          </Link>
  
          <Link
            :href="route('admin.requests.all')"
            :class="{
              'bg-purple-100 text-[#891652] border-l-4 border-[#891652]': route().current('admin.requests.all'),
              'hover:bg-purple-50 hover:text-[#891652]': !route().current('admin.requests.all'),
              'justify-center': sidebarCollapsed && !isMobile,
              'justify-start': !sidebarCollapsed || isMobile,
            }"
            class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors group"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            <span
              :class="{ 'opacity-0 w-0': sidebarCollapsed && !isMobile, 'ml-3': !sidebarCollapsed || isMobile }"
              class="transition-all duration-300"
            >
              Iesniegumi visi
            </span>
            <span
              v-if="sidebarCollapsed && !isMobile"
              class="absolute left-full rounded-md px-2 py-1 ml-6 bg-purple-100 text-sm text-[#891652] invisible opacity-0 -translate-x-3 group-hover:visible group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"
            >
              Iesniegumi visi
            </span>
          </Link>
  
          <Link
            :href="route('admin.requests.pending')"
            :class="{
              'bg-purple-100 text-[#891652] border-l-4 border-[#891652]': route().current('admin.requests.pending'),
              'hover:bg-purple-50 hover:text-[#891652]': !route().current('admin.requests.pending'),
              'justify-center': sidebarCollapsed && !isMobile,
              'justify-start': !sidebarCollapsed || isMobile,
            }"
            class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors group"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            <span
              :class="{ 'opacity-0 w-0': sidebarCollapsed && !isMobile, 'ml-3': !sidebarCollapsed || isMobile }"
              class="transition-all duration-300"
            >
              Iesniegumi procesā
            </span>
            <span
              v-if="sidebarCollapsed && !isMobile"
              class="absolute left-full rounded-md px-2 py-1 ml-6 bg-purple-100 text-sm text-[#891652] invisible opacity-0 -translate-x-3 group-hover:visible group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"
            >
              Iesniegumi procesā
            </span>
          </Link>
  
          <Link
            :href="route('admin.requests.create')"
            :class="{
              'bg-purple-100 text-[#891652] border-l-4 border-[#891652]': route().current('admin.requests.create'),
              'hover:bg-purple-50 hover:text-[#891652]': !route().current('admin.requests.create'),
              'justify-center': sidebarCollapsed && !isMobile,
              'justify-start': !sidebarCollapsed || isMobile,
            }"
            class="flex items-center px-4 py-3 text-gray-700 rounded-lg transition-colors group"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 4v16m8-8H4"
              />
            </svg>
            <span
              :class="{ 'opacity-0 w-0': sidebarCollapsed && !isMobile, 'ml-3': !sidebarCollapsed || isMobile }"
              class="transition-all duration-300"
            >
              Izveidot iesniegumu
            </span>
            <span
              v-if="sidebarCollapsed && !isMobile"
              class="absolute left-full rounded-md px-2 py-1 ml-6 bg-purple-100 text-sm text-[#891652] invisible opacity-0 -translate-x-3 group-hover:visible group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"
            >
              Izveidot iesniegumu
            </span>
          </Link>
        </nav>
  
        <!-- User Profile Section -->
        <div class="border-t border-gray-200 p-4 bg-gradient-to-b from-white to-purple-50">
          <div
            :class="{ 'justify-center': sidebarCollapsed && !isMobile, 'justify-start': !sidebarCollapsed || isMobile }"
            class="flex items-center"
          >
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-[#891652] to-[#b91d73] flex items-center justify-center text-white font-semibold shadow-md">
              {{ page.props.auth?.user?.name?.charAt(0).toUpperCase() || 'U' }}
            </div>
            <div
              :class="{ 'opacity-0 w-0': sidebarCollapsed && !isMobile, 'ml-3': !sidebarCollapsed || isMobile }"
              class="transition-all duration-300"
            >
              <p class="text-sm font-medium text-gray-700">{{ page.props.auth?.user?.name }}</p>
              <p class="text-xs text-gray-500">{{ page.props.auth?.user?.email }}</p>
            </div>
          </div>
  
          <!-- Logout Button -->
          <Link
            :href="route('logout')"
            method="post"
            as="button"
            :class="{
              'justify-center w-full mt-4': sidebarCollapsed && !isMobile,
              'justify-start w-full mt-4': !sidebarCollapsed || isMobile,
            }"
            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#891652] to-[#b91d73] text-white rounded-lg hover:from-[#b91d73] hover:to-[#d82d93] transition-all duration-200 shadow-md"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
              />
            </svg>
            <span
              :class="{ 'opacity-0 w-0': sidebarCollapsed && !isMobile, 'ml-2': !sidebarCollapsed || isMobile }"
              class="transition-all duration-300"
            >
              Izrakstīties
            </span>
          </Link>
        </div>
      </aside>
  
      <!-- Mobile Overlay -->
      <div
        v-if="isMobile && showMobileMenu"
        @click="showMobileMenu = false"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
      ></div>
  
      <!-- Main Content -->
      <div :class="{ 'ml-64': !sidebarCollapsed && !isMobile, 'ml-20': sidebarCollapsed && !isMobile }" class="flex-1 flex flex-col overflow-hidden transition-all duration-300">
        <!-- Top Header for Mobile -->
        <header
          v-if="isMobile"
          class="bg-gradient-to-r from-[#891652] to-[#b91d73] text-white shadow-md h-16 flex items-center px-4 fixed top-0 left-0 right-0 z-30"
        >
          <button
            @click="toggleSidebar"
            class="text-white hover:text-gray-200 focus:outline-none mr-4"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-6 w-6"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
            </svg>
          </button>
          <div class="flex items-center">
            <img src="/images/MainLogo.png" alt="Logo" class="h-8" />
            <span class="font-semibold text-lg ml-3">Admin Panel</span>
          </div>
        </header>
  
        <!-- Page Content -->
        <main :class="{ 'pt-16': isMobile }" class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6">
          <!-- Decorative Elements -->
          <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply opacity-10 animate-blob"></div>
            <div class="absolute top-96 -left-20 w-72 h-72 bg-[#b91d73] rounded-full mix-blend-multiply opacity-10 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-24 right-96 w-60 h-60 bg-[#891652] rounded-full mix-blend-multiply opacity-10 animate-blob animation-delay-4000"></div>
          </div>
  
          <div class="relative z-10">
            <slot />
          </div>
        </main>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue';
  import { Link, usePage } from '@inertiajs/vue3';
  
  const page = usePage();
  const sidebarCollapsed = ref(localStorage.getItem('sidebarCollapsed') === 'true');
  const isMobile = ref(window.innerWidth < 768);
  const showMobileMenu = ref(false);

  defineProps({
    auth: {
        type: Object,
        default: () => ({ user: null }),
    },
  });
  
  // Save sidebar state to localStorage
  watch(sidebarCollapsed, (newVal) => {
    localStorage.setItem('sidebarCollapsed', newVal);
  });
  
  // Handle window resize
  const handleResize = () => {
    isMobile.value = window.innerWidth < 768;
    if (!isMobile.value) {
      showMobileMenu.value = false;
    }
  };
  
  onMounted(() => {
    window.addEventListener('resize', handleResize);
    return () => {
      window.removeEventListener('resize', handleResize);
    };
  });
  
  const toggleSidebar = () => {
    if (isMobile.value) {
      showMobileMenu.value = !showMobileMenu.value;
    } else {
      sidebarCollapsed.value = !sidebarCollapsed.value;
    }
  };
  </script>
  
  <style scoped>
  .transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
  }
  
  @keyframes blob {
    0% {
      transform: scale(1) translate(0px, 0px);
    }
    33% {
      transform: scale(1.1) translate(30px, -30px);
    }
    66% {
      transform: scale(0.9) translate(-30px, 30px);
    }
    100% {
      transform: scale(1) translate(0px, 0px);
    }
  }
  
  .animate-blob {
    animation: blob 7s infinite;
  }
  
  .animation-delay-2000 {
    animation-delay: 2s;
  }
  
  .animation-delay-4000 {
    animation-delay: 4s;
  }
  </style>
  