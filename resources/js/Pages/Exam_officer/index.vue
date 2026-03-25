<script setup>
import { Link, Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    academicSession: Object,
    stats: Object,
    upcomingExams: Array,
    user: Object,
});

const searchQuery = ref('');
const isSearching = ref(false);

// Computed property for filtered exams
const filteredExams = computed(() => {
    if (!searchQuery.value) return props.upcomingExams;
    
    const query = searchQuery.value.toLowerCase();
    return props.upcomingExams.filter(exam => 
        exam.course_code.toLowerCase().includes(query) ||
        exam.course_title.toLowerCase().includes(query)
    );
});

// Search function
const performSearch = () => {
    isSearching.value = true;
    // In a real app, this would call an API endpoint
    setTimeout(() => {
        isSearching.value = false;
    }, 500);
};

// Navigate to allocation page
const runAllocation = (examId) => {
    router.visit(`/allocations/run?exam=${examId}`);
};

// View allocation results
const viewAllocationResults = (allocationId) => {
    router.visit(`/allocations/${allocationId}/view`);
};
</script>

<template>
    <Head title="Dashboard - Exam Officer" />
    <body class="bg-background text-on-surface antialiased">
        <!-- SideNavBar -->
        <aside
            class="h-screen w-64 fixed left-0 top-0 z-40 bg-white dark:bg-emerald-950 shadow-xl flex flex-col p-4 gap-2 font-public-sans text-sm tracking-wide">
            <!-- Brand Header -->
            <div class="flex items-center gap-3 px-2 py-4 mb-6">
                <div
                    class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-on-primary" data-icon="school">school</span>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-emerald-700 dark:text-emerald-300 leading-none">BUK Exam Portal
                    </h1>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Exam Officer</p>
                </div>
            </div>
            <!-- Navigation Links -->
            <nav class="flex-1 space-y-1">
                <Link class="flex items-center gap-3 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg shadow-emerald-500/20 transition-all duration-300 ease-in-out"
                    href="/exam-officer">
                    <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
                    <span class="font-semibold">Dashboard</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-emerald-200/60 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 rounded-xl transition-all duration-300 ease-in-out"
                    href="/academic-sessions">
                    <span class="material-symbols-outlined" data-icon="event">event</span>
                    <span>Academic Sessions</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-emerald-200/60 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 rounded-xl transition-all duration-300 ease-in-out"
                    href="/courses">
                    <span class="material-symbols-outlined" data-icon="menu_book">menu_book</span>
                    <span>Courses</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-emerald-200/60 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 rounded-xl transition-all duration-300 ease-in-out"
                    href="/students">
                    <span class="material-symbols-outlined" data-icon="groups">groups</span>
                    <span>Students</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-emerald-200/60 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 rounded-xl transition-all duration-300 ease-in-out"
                    href="/exams">
                    <span class="material-symbols-outlined" data-icon="schedule">schedule</span>
                    <span>Exams</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-emerald-200/60 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 rounded-xl transition-all duration-300 ease-in-out"
                    href="/allocations">
                    <span class="material-symbols-outlined" data-icon="assignment">assignment</span>
                    <span>Allocations</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-emerald-200/60 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 rounded-xl transition-all duration-300 ease-in-out"
                    href="/venues">
                    <span class="material-symbols-outlined" data-icon="location_on">location_on</span>
                    <span>Venues</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-emerald-200/60 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 rounded-xl transition-all duration-300 ease-in-out"
                    href="/reports">
                    <span class="material-symbols-outlined" data-icon="assessment">assessment</span>
                    <span>Reports</span>
                </Link>
            </nav>
            <!-- Footer Navigation -->
            <div class="pt-4 border-t border-slate-100 dark:border-emerald-900/50 space-y-1">
                <Link class="flex items-center gap-3 px-4 py-3 text-slate-500 dark:text-emerald-200/60 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 rounded-xl transition-all duration-300 ease-in-out"
                    href="/help">
                    <span class="material-symbols-outlined" data-icon="help">help</span>
                    <span>Help Center</span>
                </Link>
                <Link href="/logout" method="POST" as="button"
                    class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all duration-300 ease-in-out">
                    <span class="material-symbols-outlined" data-icon="logout">logout</span>
                    <span>Logout</span>
                </Link>
            </div>
        </aside>
        <!-- Main Content Area -->
        <main class="ml-64 min-h-screen">
            <!-- TopAppBar -->
            <header
                class="sticky top-0 z-50 bg-white/80 dark:bg-emerald-950/80 backdrop-blur-md shadow-lg shadow-emerald-900/5 px-6 py-3 flex justify-between items-center w-full">
                <div class="flex items-center flex-1 max-w-xl gap-4">
                    <!-- Academic Session Indicator -->
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg border border-emerald-200 dark:border-emerald-800">
                        <span class="material-symbols-outlined text-emerald-600 text-sm" data-icon="calendar_today">calendar_today</span>
                        <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">
                            {{ academicSession?.name || '2024/2025' }}
                        </span>
                    </div>
                    <!-- Search Bar -->
                    <div class="relative w-full group">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"
                            data-icon="search">search</span>
                        <input
                            v-model="searchQuery"
                            @input="performSearch"
                            class="w-full bg-surface-container-low dark:bg-emerald-900/20 border-none rounded-xl pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500 transition-all font-public-sans"
                            placeholder="Search exams, venues or students..."
                            type="text" />
                        <span v-if="isSearching" class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500 animate-spin">progress_activity</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 ml-6">
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/50 transition-colors active:scale-95">
                        <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                    </button>
                    <button
                        class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-600 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/50 transition-colors active:scale-95">
                        <span class="material-symbols-outlined" data-icon="settings">settings</span>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200 dark:bg-emerald-800 mx-2"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-on-surface">{{ user?.name || 'Exam Officer' }}</p>
                            <p class="text-[10px] text-slate-500 font-medium capitalize">{{ user?.role?.replace('_', ' ') || 'Exam Officer' }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary border-2 border-white shadow-sm">
                            <span class="material-symbols-outlined text-2xl">account_circle</span>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Page Content -->
            <div class="p-8 space-y-8">
                <!-- Welcome Header & Action -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h2
                            class="text-4xl font-black tracking-tighter text-emerald-900 dark:text-emerald-100 leading-tight">
                            Welcome back, {{ user?.name?.split(' ')[0] || 'Exam Officer' }}</h2>
                        <p class="text-on-surface-variant font-medium mt-1">Faculty of Computing Exam Portal Dashboard
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <Link
                            class="bg-surface-container-low text-on-surface font-bold px-5 py-3 rounded-xl border border-outline-variant/30 flex items-center gap-2 hover:bg-white hover:shadow-md transition-all"
                            href="/courses/import">
                            <span class="material-symbols-outlined" data-icon="upload_file">upload_file</span>
                            Import CSV
                        </Link>
                        <Link
                            class="bg-surface-container-low text-on-surface font-bold px-5 py-3 rounded-xl border border-outline-variant/30 flex items-center gap-2 hover:bg-white hover:shadow-md transition-all"
                            href="/exams/create">
                            <span class="material-symbols-outlined" data-icon="event_add">event_add</span>
                            Create Exam
                        </Link>
                        <Link
                            class="bg-primary text-on-primary font-bold px-6 py-3 rounded-xl shadow-lg shadow-primary/20 flex items-center gap-2 hover:brightness-105 active:scale-95 transition-all"
                            href="/allocations/run">
                            <span class="material-symbols-outlined font-bold" data-icon="play_arrow">play_arrow</span>
                            Run Allocation
                        </Link>
                    </div>
                </div>
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Pending Allocations -->
                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant/20 group hover:shadow-xl transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined" data-icon="pending_actions">pending_actions</span>
                            </div>
                            <span
                                v-if="stats?.pendingAllocations > 0"
                                class="text-[10px] font-black bg-amber-100 text-amber-700 px-2 py-1 rounded-full tracking-widest uppercase">Action Needed</span>
                        </div>
                        <p class="text-3xl font-black text-on-surface leading-none">{{ stats?.pendingAllocations || 0 }}</p>
                        <p class="text-sm text-slate-500 font-bold mt-2">Pending Allocations</p>
                    </div>
                    <!-- Total Courses -->
                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant/20 group hover:shadow-xl transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined" data-icon="menu_book">menu_book</span>
                            </div>
                            <span
                                class="text-[10px] font-black bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full tracking-widest uppercase">Active</span>
                        </div>
                        <p class="text-3xl font-black text-on-surface leading-none">{{ stats?.totalCourses || 0 }}</p>
                        <p class="text-sm text-slate-500 font-bold mt-2">Total Courses</p>
                    </div>
                    <!-- Total Students -->
                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant/20 group hover:shadow-xl transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined" data-icon="groups">groups</span>
                            </div>
                            <span
                                class="text-[10px] font-black bg-blue-100 text-blue-700 px-2 py-1 rounded-full tracking-widest uppercase">Enrolled</span>
                        </div>
                        <p class="text-3xl font-black text-on-surface leading-none">{{ stats?.totalStudents || 0 }}</p>
                        <p class="text-sm text-slate-500 font-bold mt-2">Total Students</p>
                    </div>
                    <!-- Upcoming Exams -->
                    <div
                        class="bg-white p-6 rounded-2xl shadow-sm border border-outline-variant/20 group hover:shadow-xl transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-primary/20 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                                <span class="material-symbols-outlined" data-icon="event_note">event_note</span>
                            </div>
                            <span
                                class="text-[10px] font-black bg-primary/20 text-primary px-2 py-1 rounded-full tracking-widest uppercase">This Week</span>
                        </div>
                        <p class="text-3xl font-black text-on-surface leading-none">{{ stats?.upcomingExams || 0 }}</p>
                        <p class="text-sm text-slate-500 font-bold mt-2">Upcoming Exams</p>
                    </div>
                </div>
                <!-- Bento Grid Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Table Section -->
                    <div
                        class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-outline-variant/10 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-on-surface">Upcoming Exams</h3>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">Scheduled examinations for this week</p>
                            </div>
                            <Link
                                class="text-emerald-600 text-xs font-black uppercase tracking-widest hover:underline"
                                href="/exams">View All</Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-surface-container-low/50">
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Course Code</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Course Title</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Date &amp; Time</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Students</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <tr v-for="exam in filteredExams" :key="exam.id" class="hover:bg-emerald-50/30 transition-colors">
                                        <td class="px-6 py-5">
                                            <span class="font-bold text-on-surface">{{ exam.course_code }}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <p class="text-sm font-medium text-on-surface">{{ exam.course_title }}</p>
                                        </td>
                                        <td class="px-6 py-5">
                                            <p class="text-xs font-semibold text-slate-500">{{ exam.date }}, {{ exam.time }}</p>
                                        </td>
                                        <td class="px-6 py-5">
                                            <p class="text-xs font-medium text-on-surface">{{ exam.students_count }}</p>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                v-if="exam.allocated"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase">Allocated</span>
                                            <span
                                                v-else
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 uppercase">Pending</span>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <button
                                                v-if="exam.allocated"
                                                @click="viewAllocationResults(exam.id)"
                                                class="bg-surface-container-low text-on-surface-variant text-[10px] font-black uppercase px-3 py-1.5 rounded-lg border border-outline-variant/20 hover:bg-white hover:shadow-sm transition-all">
                                                View Results
                                            </button>
                                            <button
                                                v-else
                                                @click="runAllocation(exam.id)"
                                                class="bg-primary text-on-primary text-[10px] font-black uppercase px-3 py-1.5 rounded-lg shadow-md hover:shadow-lg transition-all">
                                                Allocate
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="!filteredExams || filteredExams.length === 0">
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <span class="material-symbols-outlined text-slate-300 text-4xl mb-2">event_busy</span>
                                            <p class="text-slate-500 text-sm">
                                                {{ searchQuery ? 'No exams found matching your search' : 'No upcoming exams scheduled' }}
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Secondary Info Section (Bento) -->
                    <div class="space-y-6">
                        <!-- Venue Capacity Card -->
                        <div class="bg-emerald-900 text-white p-6 rounded-3xl shadow-xl relative overflow-hidden group">
                            <div class="relative z-10">
                                <h3 class="text-lg font-bold mb-1">Venue Utilization</h3>
                                <p class="text-emerald-300 text-xs mb-6">Current faculty capacity usage</p>
                                <div class="flex items-end gap-2 mb-2">
                                    <span class="text-4xl font-black">{{ stats?.venueUtilization || 82 }}%</span>
                                    <span class="text-emerald-400 text-xs font-bold pb-1">+5% from last session</span>
                                </div>
                                <div class="w-full bg-emerald-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-primary h-full w-[82%] rounded-full"></div>
                                </div>
                            </div>
                            <span
                                class="material-symbols-outlined absolute -bottom-6 -right-6 text-emerald-800 text-9xl rotate-12 opacity-50 pointer-events-none"
                                data-icon="room_preferences">room_preferences</span>
                        </div>
                        <!-- Quick Actions -->
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-outline-variant/10">
                            <h3 class="text-lg font-bold text-on-surface mb-4">Quick Actions</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <Link
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-surface-container-low hover:bg-emerald-50 group transition-all"
                                    href="/courses/import">
                                    <span
                                        class="material-symbols-outlined text-emerald-600 mb-2 group-hover:scale-110 transition-transform"
                                        data-icon="upload_file">upload_file</span>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-tight text-on-surface-variant text-center">Import CSV</span>
                                </Link>
                                <Link
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-surface-container-low hover:bg-emerald-50 group transition-all"
                                    href="/exams/create">
                                    <span
                                        class="material-symbols-outlined text-emerald-600 mb-2 group-hover:scale-110 transition-transform"
                                        data-icon="event_add">event_add</span>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-tight text-on-surface-variant text-center">Create Exam</span>
                                </Link>
                                <Link
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-surface-container-low hover:bg-emerald-50 group transition-all"
                                    href="/students">
                                    <span
                                        class="material-symbols-outlined text-emerald-600 mb-2 group-hover:scale-110 transition-transform"
                                        data-icon="groups">groups</span>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-tight text-on-surface-variant text-center">Manage Students</span>
                                </Link>
                                <Link
                                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-surface-container-low hover:bg-emerald-50 group transition-all"
                                    href="/allocations/run">
                                    <span
                                        class="material-symbols-outlined text-emerald-600 mb-2 group-hover:scale-110 transition-transform"
                                        data-icon="play_arrow">play_arrow</span>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-tight text-on-surface-variant text-center">Run Allocation</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</template>