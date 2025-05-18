<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const isDarkMode = ref(false);

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
};

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
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

            </div>
        </header>

        <!-- Main Content -->
        <main class="flex min-h-screen pt-12">
            <!-- Left Side - Login Form -->
            <div class="w-1/2 flex items-center justify-center -mt-16">
                <div :class="['w-96 p-8 rounded-lg shadow-lg transition-colors duration-200',
                    isDarkMode ? 'bg-gray-800' : 'bg-white']">
                    <h2 :class="['text-2xl font-bold mb-6', isDarkMode ? 'text-white' : 'text-[#891652]']">
                        Pieslēgties
                    </h2>
                    
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label :class="['block text-sm mb-2', isDarkMode ? 'text-gray-300' : 'text-[#891652]']">
                                E-pasts
                            </label>
                            <input 
                                type="email"
                                v-model="form.email"
                                :class="['w-full p-2 rounded border transition-colors duration-200',
                                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-[#FFEDD8] border-[#d4a258] text-[#891652]']"
                            />
                        </div>
                        
                        <div class="mb-6">
                            <label :class="['block text-sm mb-2', isDarkMode ? 'text-gray-300' : 'text-[#891652]']">
                                Parole
                            </label>
                            <input 
                                type="password"
                                v-model="form.password"
                                :class="['w-full p-2 rounded border transition-colors duration-200',
                                    isDarkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-[#FFEDD8] border-[#d4a258] text-[#891652]']"
                            />
                            <a :href="route('password.request')" class="text-[#891652] hover:text-[#d4a258] text-sm mt-1 block transition-colors">
                                Aizmirsi paroli?
                            </a>
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-[#891652] text-white py-2 rounded hover:bg-[#d4a258] transition-colors"
                        >
                            Pieslēgties
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Side - Image -->
            <div class="w-1/2 flex items-center justify-center p-8">
                <div class="w-96">
                    <img src="/images/dashboard.svg" alt="Main illustration" class="w-full" />
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer :class="['fixed bottom-0 w-full border-t transition-colors duration-200',
            isDarkMode ? 'bg-gray-900 border-gray-800' : 'bg-[#FFEDD8] border-[#d4a258]']">
            <div class="container mx-auto px-4 py-3">
                <div class="flex justify-between text-sm" :class="isDarkMode ? 'text-gray-400' : 'text-[#891652]'">

                    <div>
                        © "SIA Abrams Business Services" Visas tiesības aizsargātas.
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>