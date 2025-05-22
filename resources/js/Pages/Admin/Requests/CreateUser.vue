<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
});

const submit = () => {
    form.post(route('admin.users.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                    <h1 class="text-2xl font-bold text-gray-800">Izveidot jaunu lietotāju</h1>
                </div>

                <div class="p-6">
                    <!-- Success message -->
                    <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ $page.props.flash.success }}
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name input -->
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-[#891652]">Nosaukums</label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="bg-[#FFEDD8] border border-[#d4a258] text-[#891652] text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Uzņēmuma nosaukums..."
                                required
                            />
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Email input -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-[#891652]">E-pasts</label>
                            <input
                                type="email"
                                id="email"
                                v-model="form.email"
                                class="bg-[#FFEDD8] border border-[#d4a258] text-[#891652] text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Uzņēmuma e-pasts..."
                                required
                            />
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <!-- Submit button -->
                        <div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-[#891652] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#d4a258] focus:ring-4 focus:ring-blue-300 disabled:opacity-50"
                            >
                                Izveidot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>