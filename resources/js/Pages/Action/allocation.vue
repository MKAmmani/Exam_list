<script setup>
import { Link, Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    exam: Object,
    allocation: Object,
    venues: Array,
    stats: Object,
});

const page = usePage();
const isAllocated = computed(() => props.allocation?.is_allocated || props.exam?.is_allocated || false);
const showStudentList = ref({});

// Get venue icon based on type
const getVenueIcon = (type) => {
    const icons = {
        'lecture_hall': 'stadium',
        'classroom': 'school',
        'hall': 'meeting_room',
    };
    return icons[type] || 'school';
};

// Watch for flash messages
watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success) {
        alert(newFlash.success);
    }
    if (newFlash?.error) {
        alert(newFlash.error);
    }
}, { deep: true });

const toggleStudentList = (venueId) => {
    showStudentList.value[venueId] = !showStudentList.value[venueId];
};

const runAllocation = () => {
    if (!confirm('Run automatic venue and seat allocation for this exam?')) return;

    router.post(`/allocations/run`, { exam_id: props.exam?.id }, {
        preserveScroll: true,
        onSuccess: () => {
            // Page will reload with allocation data
        },
        onError: (errors) => {
            alert(errors.allocation || errors.exam || 'Allocation failed');
        },
    });
};

const printMasterList = () => {
    window.print();
};

const viewAttendanceSheet = (venue) => {
    router.visit(`/allocations/${props.exam?.id}/attendance?venue_id=${venue.id}`, {
        preserveState: true,
    });
};
</script>

<template>
    <Head title="Allocation - Exam Officer" />
    <body
        class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen">
        <div class="flex flex-col min-h-screen">
            <!-- Top Navigation -->
            <header
                class="flex items-center justify-between border-b border-primary/10 bg-white dark:bg-background-dark px-6 md:px-10 py-3 sticky top-0 z-50">
                <div class="flex items-center gap-4">
                    <Link href="/exam-officer" class="text-primary">
                        <span class="material-symbols-outlined text-4xl">account_balance</span>
                    </Link>
                    <div>
                        <h2 class="text-slate-900 dark:text-slate-100 text-lg font-bold leading-tight">BUK Exam
                            Management</h2>
                        <p class="text-primary text-xs font-semibold uppercase tracking-wider">Exam Officer Portal</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex gap-2">
                        <button
                            class="flex items-center justify-center rounded-lg h-10 w-10 bg-primary/10 text-slate-900 dark:text-slate-100 hover:bg-primary/20 transition-colors">
                            <span class="material-symbols-outlined">notifications</span>
                        </button>
                        <button
                            class="flex items-center justify-center rounded-lg h-10 w-10 bg-primary/10 text-slate-900 dark:text-slate-100 hover:bg-primary/20 transition-colors">
                            <span class="material-symbols-outlined">account_circle</span>
                        </button>
                    </div>
                    <div class="hidden sm:block border-l border-primary/10 pl-4">
                        <p class="text-sm font-bold">{{ exam?.course_code || 'CSC3301' }}</p>
                        <p class="text-xs text-slate-500">Exam Office</p>
                    </div>
                </div>
            </header>
            <div class="flex flex-1 flex-col md:flex-row">
                <!-- Sidebar Navigation -->
                <nav
                    class="w-full md:w-64 border-r border-primary/10 bg-white dark:bg-background-dark p-4 flex flex-col gap-2">
                    <div class="mb-6 px-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-primary flex items-center justify-center text-white">
                                <span class="material-symbols-outlined">shield_person</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold">BUK Portal</p>
                                <p class="text-xs text-primary font-medium">Exam Officer Panel</p>
                            </div>
                        </div>
                    </div>
                    <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-primary/5 hover:text-primary transition-all"
                        href="/exam-officer">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="text-sm font-semibold">Dashboard</span>
                    </Link>
                    <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-primary/5 hover:text-primary transition-all"
                        href="/academic-sessions">
                        <span class="material-symbols-outlined">event</span>
                        <span class="text-sm font-semibold">Academic Sessions</span>
                    </Link>
                    <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-primary/5 hover:text-primary transition-all"
                        href="/courses">
                        <span class="material-symbols-outlined">menu_book</span>
                        <span class="text-sm font-semibold">Courses</span>
                    </Link>
                    <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-primary/5 hover:text-primary transition-all"
                        href="/students">
                        <span class="material-symbols-outlined">groups</span>
                        <span class="text-sm font-semibold">Students</span>
                    </Link>
                    <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-primary/5 hover:text-primary transition-all"
                        href="/exams">
                        <span class="material-symbols-outlined">schedule</span>
                        <span class="text-sm font-semibold">Exams</span>
                    </Link>
                    <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-primary text-white shadow-lg shadow-primary/20 transition-all"
                        href="/allocations">
                        <span class="material-symbols-outlined">groups</span>
                        <span class="text-sm font-semibold">Allocations</span>
                    </Link>
                    <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-primary/5 hover:text-primary transition-all"
                        href="/venues">
                        <span class="material-symbols-outlined">apartment</span>
                        <span class="text-sm font-semibold">Venues</span>
                    </Link>
                    <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-primary/5 hover:text-primary transition-all"
                        href="/reports">
                        <span class="material-symbols-outlined">description</span>
                        <span class="text-sm font-semibold">Reports</span>
                    </Link>
                    <div class="mt-auto pt-4 border-t border-primary/10">
                        <Link class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-500 hover:bg-red-50 transition-all"
                            href="/logout" method="post" as="button">
                            <span class="material-symbols-outlined">logout</span>
                            <span class="text-sm font-semibold">Logout</span>
                        </Link>
                    </div>
                </nav>
                <!-- Main Content Area -->
                <main class="flex-1 p-6 md:p-10 bg-background-light dark:bg-background-dark max-w-6xl mx-auto w-full">
                    <!-- Breadcrumbs -->
                    <div class="flex items-center gap-2 mb-6 text-sm">
                        <Link class="text-primary font-medium hover:underline" href="/exam-officer">Dashboard</Link>
                        <span class="text-slate-400 material-symbols-outlined text-sm">chevron_right</span>
                        <Link class="text-primary font-medium hover:underline" href="/allocations">Allocations</Link>
                        <span class="text-slate-400 material-symbols-outlined text-sm">chevron_right</span>
                        <span class="text-slate-500 font-medium">{{ isAllocated ? 'Allocation Results' : 'Run Allocation' }}</span>
                    </div>

                    <!-- PRE-ALLOCATION STATE -->
                    <div v-if="!isAllocated" class="space-y-8">
                        <!-- Exam Info Card -->
                        <div class="bg-white dark:bg-slate-800 border border-primary/10 rounded-2xl p-8 shadow-lg">
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-16 w-16 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                                        <span class="material-symbols-outlined text-4xl">event_note</span>
                                    </div>
                                    <div>
                                        <h1 class="text-3xl font-black text-slate-900 dark:text-slate-100">
                                            {{ exam?.course_code || 'CSC3301' }} - {{ exam?.course_title || 'Operating Systems' }}
                                        </h1>
                                        <p class="text-slate-500 mt-1">Set up venue and seat allocation for this examination</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Exam Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                                    <div class="flex items-center gap-2 text-slate-500 mb-2">
                                        <span class="material-symbols-outlined text-sm">calendar_today</span>
                                        <span class="text-xs font-bold uppercase">Date</span>
                                    </div>
                                    <p class="text-lg font-bold text-slate-900 dark:text-slate-100">
                                        {{ exam?.date || 'May 12, 2025' }}
                                    </p>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                                    <div class="flex items-center gap-2 text-slate-500 mb-2">
                                        <span class="material-symbols-outlined text-sm">schedule</span>
                                        <span class="text-xs font-bold uppercase">Time</span>
                                    </div>
                                    <p class="text-lg font-bold text-slate-900 dark:text-slate-100">
                                        {{ exam?.start_time || '09:00 AM' }} - {{ exam?.end_time || '11:00 AM' }}
                                    </p>
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                                    <div class="flex items-center gap-2 text-slate-500 mb-2">
                                        <span class="material-symbols-outlined text-sm">groups</span>
                                        <span class="text-xs font-bold uppercase">Students</span>
                                    </div>
                                    <p class="text-lg font-bold text-slate-900 dark:text-slate-100">
                                        {{ exam?.students_count || stats?.totalStudents || 350 }} Students
                                    </p>
                                </div>
                            </div>

                            <!-- Allocation Info -->
                            <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 mb-8">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-primary text-2xl">info</span>
                                    <div>
                                        <h3 class="font-bold text-slate-900 dark:text-slate-100 mb-1">How Automatic Allocation Works</h3>
                                        <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
                                            <li>• Students are distributed across available venues based on capacity</li>
                                            <li>• Sequential seat numbers are generated (e.g., A-001, A-002, B-001)</li>
                                            <li>• Venue capacity is never exceeded</li>
                                            <li>• Allocation completes in under 10 seconds</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Run Allocation Button -->
                            <div class="flex items-center gap-4">
                                <button
                                    @click="runAllocation"
                                    class="flex items-center gap-3 bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-xl font-bold text-lg transition-all shadow-lg shadow-primary/30 hover:shadow-xl hover:scale-105 active:scale-95">
                                    <span class="material-symbols-outlined">play_arrow</span>
                                    Run Automatic Allocation
                                </button>
                                <Link href="/exams" class="text-slate-500 hover:text-slate-700 font-semibold">
                                    Cancel
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- POST-ALLOCATION RESULTS -->
                    <div v-else class="space-y-8">
                        <!-- Page Header -->
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                            <div>
                                <h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                                    Allocation Results</h1>
                                <p class="text-slate-600 dark:text-slate-400 mt-2">
                                    {{ exam?.course_code || 'CSC3301' }} - Student distribution completed successfully
                                </p>
                            </div>
                            <div class="flex gap-3">
                                <button
                                    @click="printMasterList"
                                    class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-5 py-2.5 rounded-lg font-bold transition-all shadow-md">
                                    <span class="material-symbols-outlined">print</span>
                                    Print Master List
                                </button>
                                <Link
                                    :href="`/allocations/${allocation?.id}/download`"
                                    class="flex items-center gap-2 bg-slate-900 dark:bg-slate-700 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg font-bold transition-all shadow-md">
                                    <span class="material-symbols-outlined">download</span>
                                    Download CSV
                                </Link>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                            <div
                                class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-primary/10 shadow-sm flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl">person_check</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Allocated</p>
                                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats?.totalAllocated || 350 }}</p>
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-primary/10 shadow-sm flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl">meeting_room</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Venues Used</p>
                                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ venues?.length || 3 }}</p>
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-primary/10 shadow-sm flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl">seat</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Avg Occupancy</p>
                                    <p class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ stats?.avgOccupancy || 78 }}%</p>
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-primary/10 shadow-sm flex items-center gap-4">
                                <div
                                    class="h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl">check_circle</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Unallocated</p>
                                    <p class="text-2xl font-black text-emerald-600">{{ stats?.unallocated || 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Venue List Section -->
                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Venue Distribution Details</h2>
                            <span class="text-xs font-bold bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">check_circle</span> Completed
                            </span>
                        </div>

                        <!-- Venue Cards -->
                        <div class="space-y-4">
                            <div v-for="venue in venues" :key="venue.id"
                                class="bg-white dark:bg-slate-800 border border-primary/10 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                <div class="p-5 flex flex-col gap-4">
                                    <!-- Venue Header -->
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="bg-primary/10 h-14 w-14 rounded-lg flex items-center justify-center text-primary">
                                                <span class="material-symbols-outlined text-3xl">{{ getVenueIcon(venue.venue?.type) }}</span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ venue.venue?.name }}</h3>
                                                <p class="text-sm text-slate-500">{{ venue.venue?.location }}</p>
                                                <div class="flex gap-3 mt-2">
                                                    <span
                                                        class="text-xs font-semibold px-2.5 py-1 bg-slate-100 dark:bg-slate-700 rounded flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-sm">groups</span>
                                                        {{ venue.allocated_count }} Students
                                                    </span>
                                                    <span
                                                        class="text-xs font-semibold px-2.5 py-1 bg-slate-100 dark:bg-slate-700 rounded flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-sm">grid_on</span>
                                                        Capacity: {{ venue.venue?.capacity }}
                                                    </span>
                                                    <span
                                                        class="text-xs font-semibold px-2.5 py-1 bg-primary/10 text-primary rounded flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-sm">chair</span>
                                                        Seats: {{ venue.seat_range }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-2 min-w-[280px]">
                                            <Link
                                                :href="`/allocations/${exam?.id}/attendance?venue_id=${venue.venue?.id}`"
                                                class="flex items-center justify-center gap-2 w-full bg-primary text-white py-2.5 rounded-lg font-bold text-sm transition-transform active:scale-95 hover:shadow-lg">
                                                <span class="material-symbols-outlined text-lg">assignment</span>
                                                View Attendance Sheet
                                            </Link>
                                        </div>
                                    </div>

                                    <!-- Student List Toggle -->
                                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                                        <button
                                            @click="toggleStudentList(venue.venue?.id)"
                                            class="flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
                                            <span class="material-symbols-outlined text-lg">
                                                {{ showStudentList[venue.venue?.id] ? 'expand_less' : 'expand_more' }}
                                            </span>
                                            {{ showStudentList[venue.venue?.id] ? 'Hide' : 'Show' }} Student List ({{ venue.allocated_count }})
                                        </button>

                                        <!-- Student List -->
                                        <div v-if="showStudentList[venue.venue?.id]" class="mt-4 bg-slate-50 dark:bg-slate-700/30 rounded-lg overflow-hidden">
                                            <div class="overflow-x-auto">
                                                <table class="w-full text-sm">
                                                    <thead class="bg-slate-100 dark:bg-slate-700">
                                                        <tr>
                                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Seat No.</th>
                                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Registration No.</th>
                                                            <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase">Student Name</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                                        <tr v-for="student in venue.students" :key="student.id" class="hover:bg-slate-100 dark:hover:bg-slate-700/50">
                                                            <td class="px-4 py-3 font-bold text-primary">{{ student.seat_number }}</td>
                                                            <td class="px-4 py-3 font-medium">{{ student.student?.registration_number }}</td>
                                                            <td class="px-4 py-3">{{ student.student?.name }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div v-if="isAllocated"
                        class="mt-12 flex flex-col md:flex-row items-center justify-between bg-white dark:bg-slate-800 p-6 rounded-xl border border-primary/20">
                        <div class="mb-4 md:mb-0">
                            <h4 class="font-bold text-slate-900 dark:text-slate-100">Ready to finalize?</h4>
                            <p class="text-sm text-slate-500">All student documents are generated and ready for distribution to invigilators.</p>
                        </div>
                        <div class="flex gap-3 w-full md:w-auto">
                            <Link
                                href="/allocations"
                                class="flex-1 md:flex-none px-6 py-2.5 rounded-lg border border-primary text-primary font-bold hover:bg-primary/5 transition-all text-center">
                                Review List
                            </Link>
                            <button
                                class="flex-1 md:flex-none px-6 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                                Distribute Digitally
                            </button>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</template>