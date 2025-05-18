<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#FFEDD8] relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 z-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2760%27%20height%3D%2760%27%20viewBox%3D%270%200%2060%2060%27%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%3E%3Cg%20fill%3D%27none%27%20fill-rule%3D%27evenodd%27%3E%3Cg%20fill%3D%27%23d4a258%27%20fill-opacity%3D%270.4%27%3E%3Cpath%20d%3D%27M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%27%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-10 pointer-events-none"></div>
        
        <!-- Navigation Bar -->
        <nav class="border-b border-[#d4a258] bg-[#EABE6C] shadow-lg sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link 
                            :href="route('dashboard')" 
                            class="relative flex items-center p-2 rounded-lg overflow-hidden group"
                        >
                            <span class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-transparent via-[#891652] to-transparent scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                            <img 
                                src="/images/MainLogo.png" 
                                alt="Logo" 
                                class="h-8 drop-shadow-md transform transition-transform duration-300 group-hover:-translate-y-0.5" 
                            />
                        </Link>
                    </div>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden sm:flex items-center space-x-8">
                        <Link
                            :href="route('dashboard')"
                            :class="{
                                'relative px-3 py-2 text-sm font-semibold text-white rounded bg-[#891652]/10 border-b-4 border-[#891652] shadow-md': route().current('dashboard'),
                                'relative px-3 py-2 text-sm font-semibold text-white rounded overflow-hidden hover:text-white transition-colors duration-300 ease-in-out': !route().current('dashboard')
                            }"
                        >
                            <span 
                                v-if="!route().current('dashboard')"
                                class="absolute inset-0 bg-white/10 scale-x-0 origin-left hover:scale-x-100 transition-transform duration-300"
                            ></span>
                            <span class="relative">Sākums</span>
                        </Link>
                        <Link
                            :href="route('user.requests.index')"
                            :class="{
                                'relative px-3 py-2 text-sm font-semibold text-white rounded bg-[#891652]/10 border-b-4 border-[#891652] shadow-md': route().current('user.requests.index'),
                                'relative px-3 py-2 text-sm font-semibold text-white rounded overflow-hidden hover:text-white transition-colors duration-300 ease-in-out': !route().current('user.requests.index')
                            }"
                        >
                            <span 
                                v-if="!route().current('user.requests.index')"
                                class="absolute inset-0 bg-white/10 scale-x-0 origin-left hover:scale-x-100 transition-transform duration-300"
                            ></span>
                            <span class="relative">Iesniegumi</span>
                        </Link>
                        <Link
                            :href="route('profile.edit')"
                            :class="{
                                'relative px-3 py-2 text-sm font-semibold text-white rounded bg-[#891652]/10 border-b-4 border-[#891652] shadow-md': route().current('profile.edit'),
                                'relative px-3 py-2 text-sm font-semibold text-white rounded overflow-hidden hover:text-white transition-colors duration-300 ease-in-out': !route().current('profile.edit')
                            }"
                        >
                            <span 
                                v-if="!route().current('profile.edit')"
                                class="absolute inset-0 bg-white/10 scale-x-0 origin-left hover:scale-x-100 transition-transform duration-300"
                            ></span>
                            <span class="relative">Profils</span>
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="px-4 py-2 text-sm font-semibold text-white bg-[#891652] rounded-full shadow-md transform transition-all duration-300 hover:bg-[#a61c61] hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 active:shadow-md"
                        >
                            Izrakstīties
                        </Link>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="sm:hidden p-2 text-white bg-[#891652]/20 rounded-full focus:outline-none focus:ring-2 focus:ring-white shadow transition-all duration-300 hover:bg-[#891652]/30"
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
                class="sm:hidden bg-[#EABE6C] shadow-md origin-top animate-[slideDown_0.3s_ease-out]"
            >
                <div class="space-y-1 py-3">
                    <Link
                        :href="route('dashboard')"
                        :class="{
                            'block mx-2 px-4 py-3 text-sm font-semibold rounded-lg bg-[#891652]/15 text-white border-l-4 border-[#891652] transition-all duration-300': route().current('dashboard'),
                            'block mx-2 px-4 py-3 text-sm font-semibold rounded-lg text-white transition-all duration-300 hover:bg-[#891652]/10 hover:translate-x-1': !route().current('dashboard')
                        }"
                    >
                        Sākums
                    </Link>
                    <Link
                        :href="route('user.requests.index')"
                        :class="{
                            'block mx-2 px-4 py-3 text-sm font-semibold rounded-lg bg-[#891652]/15 text-white border-l-4 border-[#891652] transition-all duration-300': route().current('user.requests.index'),
                            'block mx-2 px-4 py-3 text-sm font-semibold rounded-lg text-white transition-all duration-300 hover:bg-[#891652]/10 hover:translate-x-1': !route().current('user.requests.index')
                        }"
                    >
                        Iesniegumi
                    </Link>
                    <Link
                        :href="route('profile.edit')"
                        :class="{
                            'block mx-2 px-4 py-3 text-sm font-semibold rounded-lg bg-[#891652]/15 text-white border-l-4 border-[#891652] transition-all duration-300': route().current('profile.edit'),
                            'block mx-2 px-4 py-3 text-sm font-semibold rounded-lg text-white transition-all duration-300 hover:bg-[#891652]/10 hover:translate-x-1': !route().current('profile.edit')
                        }"
                    >
                        Profils
                    </Link>
                </div>

                <!-- Mobile User Info -->
                <div class="border-t border-[#d4a258] py-4 bg-[#891652]/5">
                    <div class="px-4 text-white">
                        <div class="text-base font-bold text-[#891652] drop-shadow">{{ $page.props.auth.user.name }}</div>
                        <div class="text-sm font-medium text-white/90 italic">{{ $page.props.auth.user.email }}</div>
                    </div>
                    <div class="mt-3 space-y-1 px-4">
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="block w-full text-left px-4 py-3 mx-2 rounded-lg text-sm font-semibold text-white bg-[#891652]/80 transition-all duration-300 hover:bg-[#891652] hover:translate-x-1"
                        >
                            Izrakstīties
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="p-6 relative z-10">
            <div class="max-w-7xl mx-auto bg-white/70 backdrop-blur-md rounded-2xl shadow-xl border border-[#EABE6C]/30 overflow-hidden animate-[fadeIn_0.5s_ease-out]">
                <slot />
            </div>
        </main>
        
        <!-- Decorative Footer Line -->
        <div class="fixed bottom-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#EABE6C] via-[#891652] to-[#EABE6C] z-20"></div>
    </div>
</template>

<style scoped>
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>