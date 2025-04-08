<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    status: {
        type: String,
    },
});

const isDarkMode = ref(false);

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
};

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <div :class="['min-h-screen transition-colors duration-200', isDarkMode ? 'bg-gray-900' : 'bg-[#FFEDD8]']">
        <!-- Header -->
        <header :class="['fixed w-full top-0 z-10 transition-colors duration-200', isDarkMode ? 'bg-gray-900' : 'bg-[#FFEDD8]']">
            <div class="container mx-auto flex justify-between items-center p-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="/images/logo2.png" alt="Logo" class="h-8" />
                </div>

                <!-- Dark Mode Toggle and Language -->
                <div class="flex items-center space-x-4">
                    <button
                        @click="toggleDarkMode"
                        class="p-2 rounded-full hover:bg-opacity-80"
                        :class="isDarkMode ? 'bg-[#EABE6C]' : 'bg-[#891652]'"
                    >
                        <svg v-if="isDarkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    <select :class="['px-2 py-1 rounded border transition-colors duration-200',
                        isDarkMode ? 'bg-gray-800 text-white border-gray-700' : 'bg-[#EABE6C] text-[#891652] border-[#d4a258]']">
                        <option>LV</option>
                    </select>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex min-h-screen pt-12">
            <!-- Left Side - Forgot Password Form -->
            <div class="w-full md:w-1/2 flex items-center justify-center -mt-16">
                <div :class="['w-96 p-8 rounded-lg shadow-lg transition-colors duration-200',
                    isDarkMode ? 'bg-gray-800' : 'bg-white']">
                    <Head title="Forgot Password" />

                    <h2 :class="['text-2xl font-bold mb-6', isDarkMode ? 'text-white' : 'text-[#891652]']">
                        Atjaunot paroli
                    </h2>

                    <div :class="['mb-4 text-sm', isDarkMode ? 'text-gray-300' : 'text-gray-600']">
                        Aizmirsi paroli? Neraizējies. Vienkārši ievadi savu e-pasta adresi un mēs nosūtīsim paroles atiestatīšanas saiti, kas ļaus tev izvēlēties jaunu paroli.
                    </div>

                    <div
                        v-if="status"
                        class="mb-4 text-sm font-medium text-green-600"
                    >
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label :class="['block text-sm mb-2', isDarkMode ? 'text-gray-300' : 'text-[#891652]']">
                                E-pasts
                            </label>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                :class="['w-full p-2 rounded border transition-colors duration-200',
                                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-[#FFEDD8] border-[#d4a258] text-[#891652]']"
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-[#891652] text-white py-2 rounded hover:bg-[#d4a258] transition-colors"
                            :class="{ 'opacity-70': form.processing }"
                            :disabled="form.processing"
                        >
                            Nosūtīt paroles atiestatīšanas saiti
                        </button>

                        <div class="mt-4 text-center">
                            <a :href="route('login')" class="text-[#891652] hover:text-[#d4a258] text-sm transition-colors">
                                Atgriezties uz pieslēgšanās lapu
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Side - Image -->
            <div class="hidden md:flex md:w-1/2 items-center justify-center p-8">
                <div class="w-96">
                    <img src="/images/dashboard.svg" alt="Reset password illustration" class="w-full" />
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer :class="['fixed bottom-0 w-full border-t transition-colors duration-200',
            isDarkMode ? 'bg-gray-900 border-gray-800' : 'bg-[#FFEDD8] border-[#d4a258]']">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-[#891652]'">
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-[#d4a258] transition-colors">Lietošanas instrukcija</a>
                        <a href="#" class="hover:text-[#d4a258] transition-colors">Es hz</a>
                        <a href="#" class="hover:text-[#d4a258] transition-colors">Es hz</a>
                        <a href="#" class="hover:text-[#d4a258] transition-colors">Es hz</a>
                    </div>
                    <div>
                        © "SIA Abrams Business Services" Visas tiesības aizsargātas.
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
