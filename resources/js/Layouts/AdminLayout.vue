<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#FFEDD8]">
        <!-- Navigation Bar -->
        <nav class="border-b border-gray-300 bg-[#EABE6C] shadow-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link :href="route('adminDashboard')">
                            <img src="/images/MainLogo.png" alt="Logo" class="h-8" />
                        </Link>
                    </div>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden sm:flex items-center space-x-8">
                        <Link
                            :href="route('adminDashboard')"
                            :class="{'border-b-4 border-[#891652]': route().current('dashboard'), 'text-white hover:text-[#d4a258]': !route().current('dashboard')}"
                            class="px-3 py-2 text-sm font-medium"
                        >
                            Sākums
                        </Link>
                        <Link
                            :href="route('admin.requests.all')"
                            :class="{'border-b-4 border-[#891652]': route().current('profile.edit'), 'text-white hover:text-[#d4a258]': !route().current('profile.edit')}"
                            class="px-3 py-2 text-sm font-medium"
                        >
                            Iesniegumi visi
                        </Link>
                        <Link
                            :href="route('admin.requests.pending')"
                            :class="{'border-b-4 border-[#891652]': route().current('profile.edit'), 'text-white hover:text-[#d4a258]': !route().current('profile.edit')}"
                            class="px-3 py-2 text-sm font-medium"
                        >
                            Iesniegumi procesā
                        </Link> 
                        <Link
                            :href="route('admin.requests.create')"
                            :class="{'border-b-4 border-[#891652]': route().current('profile.edit'), 'text-white hover:text-[#d4a258]': !route().current('profile.edit')}"
                            class="px-3 py-2 text-sm font-medium"
                        >
                            Izveidot iesniegumu
                        </Link> 
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="px-3 py-2 text-sm font-medium text-white bg-[#891652] hover:bg-[#d4a258] focus:outline-none focus:ring-2 focus:ring-white rounded-md"
                        >
                            Izrakstīties
                        </Link>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="sm:hidden p-2 text-white focus:outline-none focus:ring-2 focus:ring-white"
                    >
                        <svg
                            class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                v-if="!showingNavigationDropdown"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
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
            </div>

            <!-- Mobile Menu -->
            <div
                v-show="showingNavigationDropdown"
                class="sm:hidden bg-[#EABE6C]"
            >
                <div class="space-y-1 py-3">
                    <Link
                        :href="route('dashboard')"
                        :class="{'bg-[#d4a258] text-white': route().current('dashboard'), 'text-white hover:bg-[#891652] hover:text-white': !route().current('dashboard')}"
                        class="block px-4 py-2 text-sm font-medium"
                    >
                        Sākums
                    </Link>
                    <Link
                        :href="route('profile.edit')"
                        :class="{'bg-[#d4a258] text-white': route().current('profile.edit'), 'text-white hover:bg-[#891652] hover:text-white': !route().current('profile.edit')}"
                        class="block px-4 py-2 text-sm font-medium"
                    >
                        Profils
                    </Link>
                </div>

                <!-- Mobile User Info -->
                <div class="border-t border-gray-300 py-4">
                    <div class="px-4 text-white">
                        <div class="text-base font-medium">{{ $page.props.auth.user.name }}</div>
                        <div class="text-sm font-medium">{{ $page.props.auth.user.email }}</div>
                    </div>
                    <div class="mt-3 space-y-1 px-4">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="block w-full text-left text-sm font-medium text-white hover:bg-[#891652] hover:text-white py-2"
                        >
                            Izrakstīties
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>

<style scoped>
/* Add smooth transitions for hover effects */
a {
    transition: color 0.3s, background-color 0.3s, border-color 0.3s;
}
button {
    transition: background-color 0.3s, color 0.3s;
}
</style>
