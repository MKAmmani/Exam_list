<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Admin Dashboard" />
    <div class="min-h-screen bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold">Admin Dashboard</h1>
            <p class="mt-4">Admin panel coming soon...</p>
            <Link href="/dashboard" class="mt-4 inline-block text-primary hover:underline">
                Go to Dashboard
            </Link>
        </div>
    </div>
</template>
