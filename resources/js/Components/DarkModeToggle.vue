<template>
    <button
      @click="toggleDarkMode"
      class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 shadow-md hover:bg-gray-300 dark:hover:bg-gray-600 transition"
      aria-label="Toggle Dark Mode"
    >
      <span v-if="isDarkMode" class="text-sm font-medium">☀️ Gaišais režims</span>
      <span v-else class="text-sm font-medium">🌙 Tumšais režīms</span>
    </button>
  </template>
  
  <script setup>
  import { ref, watchEffect } from 'vue';
  
  // Track dark mode state
  const isDarkMode = ref(localStorage.getItem('theme') === 'dark');
  
  // Function to toggle dark mode
  const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
  };
  
  // Apply dark mode based on state
  watchEffect(() => {
    const html = document.documentElement;
    if (isDarkMode.value) {
      html.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    } else {
      html.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    }
  });
  </script>
  