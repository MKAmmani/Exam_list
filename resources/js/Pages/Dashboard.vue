<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    userType: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        <div v-if="userType === 'admin'">
                            <h3 class="text-lg font-semibold mb-4">Admin Dashboard</h3>
                            <p>Welcome, Administrator! You have full access to manage the exam system.</p>
                        </div>
                        <div v-else-if="userType === 'exam_officer'">
                            <h3 class="text-lg font-semibold mb-4">Exam Officer Dashboard</h3>
                            <p>Welcome, Exam Officer! You can manage exam schedules and student information.</p>
                        </div>
                        <div v-else>
                            You're logged in!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
