<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const selectedRole = ref(null);

const roles = [
    {
        value: 'admin',
        label: 'Admin',
        icon: 'admin_panel_settings',
        description: 'Full system access to manage users, settings, and all exam operations.',
        iconColor: 'text-primary',
        bgColor: 'bg-primary/10',
        hoverBgColor: 'group-hover:bg-primary/20',
    },
    {
        value: 'exam_officer',
        label: 'Exam Officer',
        icon: 'fact_check',
        description: 'Manage exam schedules, venues, and student examination records.',
        iconColor: 'text-primary',
        bgColor: 'bg-primary/10',
        hoverBgColor: 'group-hover:bg-primary/20',
    },
];

const form = useForm({
    email: '',
    password: '',
    role: 'admin',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const selectRole = (roleValue) => {
    selectedRole.value = roleValue;
    form.role = roleValue;
};

const goBack = () => {
    selectedRole.value = null;
    form.email = '';
    form.password = '';
};
</script>

<template>
    <body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased">
        <div class="flex min-h-screen">
            <!-- Split Screen: Left Side (Content) -->
            <div
                class="flex flex-1 flex-col justify-center px-6 py-12 lg:px-24 xl:px-32 bg-background-light dark:bg-background-dark">
                <div class="mx-auto w-full max-w-lg">
                    <div class="flex items-center gap-3 mb-10">
                        <div class="bg-primary p-2 rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-white">school</span>
                        </div>
                        <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">BUK Faculty of
                            Computing</h2>
                    </div>

                    <!-- Role Selection Screen -->
                    <div v-if="!selectedRole">
                        <div>
                            <h1 class="text-3xl font-black leading-tight tracking-tight text-slate-900 dark:text-white">
                                Exam Management System
                            </h1>
                            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">
                                Select your role to continue to the login page.
                            </p>
                        </div>

                        <div class="mt-10 grid gap-6">
                            <div v-for="role in roles" :key="role.value"
                                @click="selectRole(role.value)"
                                class="group cursor-pointer rounded-2xl border-2 border-slate-200 dark:border-slate-700 p-6 transition-all duration-200 hover:border-primary/50 hover:shadow-lg hover:shadow-primary/10 dark:hover:border-primary/50">
                                <div class="flex items-start gap-4">
                                    <div :class="[role.bgColor, role.hoverBgColor, 'flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl transition-all duration-200']">
                                        <span :class="[role.iconColor, 'material-symbols-outlined text-2xl']">{{ role.icon }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-primary">
                                            {{ role.label }}
                                        </h3>
                                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                                            {{ role.description }}
                                        </p>
                                        <div class="mt-4 flex items-center gap-2 text-sm font-semibold text-primary">
                                            <span>Select Role</span>
                                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="mt-10 text-center text-sm text-slate-500 dark:text-slate-500">
                            Need technical assistance?
                            <a class="font-semibold leading-6 text-primary hover:text-primary/80" href="#">Contact
                                Faculty Support</a>
                        </p>
                    </div>

                    <!-- Login Form Screen -->
                    <div v-else>
                        <div>
                            <button @click="goBack"
                                class="mb-4 flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-primary dark:text-slate-400 dark:hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-lg">arrow_back</span>
                                Back to Role Selection
                            </button>
                            <h1 class="text-3xl font-black leading-tight tracking-tight text-slate-900 dark:text-white">
                                {{ selectedRole === 'admin' ? 'Admin' : 'Exam Officer' }} Login
                            </h1>
                            <p class="mt-3 text-sm text-slate-600 dark:text-slate-400">
                                Enter your credentials to access the {{ selectedRole === 'admin' ? 'admin' : 'exam officer' }} dashboard.
                            </p>
                        </div>

                        <div class="mt-10">
                            <form @submit.prevent="submit" class="space-y-6" method="POST">
                                <div>
                                    <label class="block text-sm font-semibold leading-6 text-slate-900 dark:text-white"
                                        for="email">Email</label>
                                    <div class="mt-2">
                                        <input v-model="form.email" autocomplete="username"
                                            class="block w-full rounded-lg border-0 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-primary/20 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 dark:bg-slate-800/50 dark:text-white dark:ring-slate-700"
                                            id="email" name="email" placeholder="e.g. admin@buk.edu.ng" required=""
                                            type="text" />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>

                                <div>
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-semibold leading-6 text-slate-900 dark:text-white"
                                            for="password">Password</label>
                                    </div>
                                    <div class="mt-2 relative">
                                        <input v-model="form.password" autocomplete="current-password"
                                            class="block w-full rounded-lg border-0 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-primary/20 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 dark:bg-slate-800/50 dark:text-white dark:ring-slate-700"
                                            id="password" name="password" placeholder="••••••••" required=""
                                            type="password" />
                                        <button
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-primary"
                                            type="button">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.password" />
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <label class="flex items-center">
                                            <input v-model="form.remember"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                id="remember-me" name="remember-me" type="checkbox" />
                                            <span class="ml-2 block text-sm text-gray-900 dark:text-gray-400">Remember
                                                me</span>
                                        </label>
                                    </div>
                                    <div class="text-sm leading-6">
                                        <a class="font-semibold text-primary hover:text-primary/80" href="#">Forgot
                                            password?</a>
                                    </div>
                                </div>

                                <div>
                                    <button :disabled="form.processing"
                                        class="flex w-full justify-center rounded-lg bg-primary px-3 py-3 text-sm font-bold leading-6 text-slate-900 shadow-sm hover:bg-primary/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                        type="submit">
                                        <span v-if="form.processing" class="material-symbols-outlined animate-spin mr-2">progress_activity</span>
                                        Sign In as {{ selectedRole === 'admin' ? 'Admin' : 'Exam Officer' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <p class="mt-10 text-center text-sm text-slate-500 dark:text-slate-500">
                            Wrong role?
                            <button @click="goBack" class="font-semibold text-primary hover:text-primary/80">
                                Select a different role
                            </button>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Split Screen: Right Side (Image Overlay) -->
            <div class="relative hidden w-0 flex-1 lg:block">
                <div class="absolute inset-0 h-full w-full bg-slate-900/40 mix-blend-multiply z-10"></div>
                <div class="absolute inset-0 flex flex-col justify-end p-20 z-20">
                    <div class="max-w-xl">
                        <span
                            class="inline-flex items-center rounded-full bg-primary/20 px-3 py-1 text-xs font-medium text-primary ring-1 ring-inset ring-primary/30 mb-4 backdrop-blur-md">
                            Bayero University Kano
                        </span>
                        <h3 class="text-4xl font-bold text-white mb-4">Empowering the next generation of computing
                            professionals.</h3>
                        <p class="text-lg text-slate-200">The Faculty of Computing provides a world-class environment
                            for research, learning, and innovation in the digital age.</p>
                    </div>
                </div>
                <div class="h-full w-full bg-center bg-no-repeat bg-cover"
                    data-alt="Modern university library with students studying and computers"
                    style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuCVXna6aHcoZiwp9E5ZnnGQZiNagsqfk-r-1yzP6s21BeHaku22n2AiDDKOPvjfefwZ0Wo4ZCrW_XHw6JSrD8es8I_bwRi_NHZ_Nnw9FqCSaE77dSiVMV4f7CwPSzp0Rh9GUAasup9yNh73F4Le4zsqXqDToTLWlS45QhTizg1RRtAdcuMhF2hrs69KBOHgr11b0us9ELXt2n1IEbO71Z46HI02rqYL7_MTklrDFF1g6zRIdNckpyh9QhCZHS2zLcWeVM7Rh7vPyQc');">
                </div>
            </div>
        </div>

        <!-- Support Icon Floating -->
        <div class="fixed bottom-6 right-6 z-50">
            <button
                class="flex items-center justify-center rounded-full w-12 h-12 bg-primary text-slate-900 shadow-xl hover:scale-105 transition-transform">
                <span class="material-symbols-outlined">help</span>
            </button>
        </div>
    </body>
</template>
