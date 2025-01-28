<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DarkModeToggle from '../Components/DarkModeToggle.vue';

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: {
    type: String,
    required: true,
  },
  phpVersion: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-[#FFEDD8] text-gray-800 dark:bg-[#2C2C2C] dark:text-gray-200">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-lg rounded-b-xl">
      <div class="container mx-auto flex justify-between items-center p-6">
        <!-- Logo -->
        <img src="/storage/logo2.png" alt="Logo" class="h-12 w-auto" />
        <!-- Navigation -->
        <nav class="flex items-center space-x-6">
          <Link
            v-if="$page.props.auth.user"
            :href="route('dashboard')"
            class="text-base font-semibold text-gray-700 dark:text-gray-300 hover:text-[#891652]"
          >
            Dashboard
          </Link>
          <template v-else>
            <Link
              v-if="canLogin"
              :href="route('login')"
              class="px-6 py-2 text-base font-semibold bg-[#EABE6C] text-[#891652] rounded-full shadow-md hover:bg-[#d4a258] hover:text-[#721341]"
            >
              Log in
            </Link>
            <Link
              v-if="canRegister"
              :href="route('register')"
              class="px-6 py-2 text-base font-semibold bg-[#891652] text-[#EABE6C] rounded-full shadow-md hover:bg-[#701241] hover:text-[#d4a258]"
            >
              Register
            </Link>
          </template>
          <!-- Dark Mode Toggle -->
          <DarkModeToggle />
        </nav>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="flex flex-col items-center justify-center flex-grow text-center py-16">
      <Head title="Welcome" />
      <h1 class="text-5xl font-extrabold text-[#891652] mb-6">
        Esi sveicināts DocSys
      </h1>
      <p class="text-lg text-gray-700 dark:text-gray-300 max-w-2xl">
        Šis ir filler text, es nezinu to te likt ;)
      </p>
      <div class="mt-10 flex space-x-6">
        <Link
          :href="route('login')"
          v-if="canLogin"
          class="px-8 py-3 rounded-full bg-[#EABE6C] text-[#891652] font-bold shadow-lg hover:bg-[#d4a258] hover:text-[#721341]"
        >
          Ienākt
        </Link>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#EABE6C] dark:bg-[#3A3A3A] text-center py-8">
      <p class="text-sm text-gray-800 dark:text-gray-300">
        <span class="text-gray-700 dark:text-gray-400">© 2025. SIA "Abrams Business Services" All rights reserved.</span><br />
        Veidots ar Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
      </p>
    </footer>
  </div>
</template>
