<script setup>
import { Link, router, Head, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    sessions: Array,
});

const page = usePage();
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedSession = ref(null);
const createForm = ref({
    name: '',
    start_date: '',
    end_date: '',
    is_active: false,
});
const editForm = ref({
    name: '',
    start_date: '',
    end_date: '',
    is_active: false,
});

// Watch for flash messages from shared props
watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success) {
        alert(newFlash.success);
    }
    if (newFlash?.error) {
        alert(newFlash.error);
    }
}, { deep: true });

const handleCreate = () => {
    showCreateModal.value = true;
    createForm.value = {
        name: '',
        start_date: '',
        end_date: '',
        is_active: false,
    };
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    createForm.value = {
        name: '',
        start_date: '',
        end_date: '',
        is_active: false,
    };
};

const submitCreate = () => {
    router.post('/academic-sessions', createForm.value, {
        onSuccess: () => {
            closeCreateModal();
        },
        onError: (errors) => {
            alert(Object.values(errors).join('\n'));
        },
    });
};

const editSession = (session) => {
    selectedSession.value = session;
    editForm.value = {
        name: session.name,
        start_date: session.start_date,
        end_date: session.end_date,
        is_active: session.is_active,
    };
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    selectedSession.value = null;
    editForm.value = {
        name: '',
        start_date: '',
        end_date: '',
        is_active: false,
    };
};

const submitEdit = () => {
    router.put(`/academic-sessions/${selectedSession.value.id}`, editForm.value, {
        onSuccess: () => {
            closeEditModal();
        },
        onError: (errors) => {
            alert(Object.values(errors).join('\n'));
        },
    });
};

const deleteSession = (session) => {
    if (confirm(`Are you sure you want to delete the ${session.name} session? This cannot be undone.`)) {
        router.delete(`/academic-sessions/${session.id}`, {
            preserveScroll: true,
        });
    }
};

const setActiveSession = (session) => {
    if (confirm(`Set ${session.name} as the active academic session? This will deactivate any currently active session.`)) {
        router.post(`/academic-sessions/${session.id}/set-active`);
    }
};

const stats = computed(() => ({
    totalSessions: props.sessions?.length || 0,
    activeSession: props.sessions?.find(s => s.is_active),
    upcomingSessions: props.sessions?.filter(s => new Date(s.start_date) > new Date()).length || 0,
    archivedSessions: props.sessions?.filter(s => new Date(s.end_date) < new Date() && !s.is_active).length || 0,
}));

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const isUpcoming = (session) => {
    return new Date(session.start_date) > new Date();
};

const isArchived = (session) => {
    return new Date(session.end_date) < new Date() && !session.is_active;
};
</script>

<template>
    <Head title="Academic Sessions - Exam Officer" />
    <body class="bg-background text-on-background min-h-screen flex">
        <!-- SideNavBar Component -->
        <aside class="hidden md:flex flex-col h-screen w-64 bg-white dark:bg-zinc-950 border-r-0 rounded-r-2xl shadow-2xl shadow-zinc-200/50 dark:shadow-none p-4 space-y-2 z-50">
            <div class="px-2 py-6 mb-4">
                <div class="flex items-center gap-3">
                    <Link href="/exam-officer" class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-on-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">school</span>
                    </Link>
                    <div>
                        <h1 class="text-lg font-black text-zinc-900 dark:text-zinc-50 leading-tight">BUK Exam Portal</h1>
                        <p class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px] text-zinc-500">Exam Officer</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 space-y-1">
                <Link class="flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl hover:scale-[1.02] transition-transform duration-200" href="/exam-officer">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Dashboard</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 bg-primary text-on-primary rounded-xl shadow-lg shadow-green-500/20 translate-x-1 transition-all" href="/academic-sessions">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">event_repeat</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Sessions</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl hover:scale-[1.02] transition-transform duration-200" href="/courses">
                    <span class="material-symbols-outlined">school</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Courses</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl hover:scale-[1.02] transition-transform duration-200" href="/students">
                    <span class="material-symbols-outlined">group</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Students</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl hover:scale-[1.02] transition-transform duration-200" href="/exams">
                    <span class="material-symbols-outlined">event</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Exams</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl hover:scale-[1.02] transition-transform duration-200" href="/allocations">
                    <span class="material-symbols-outlined">assignment_turned_in</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Allocations</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl hover:scale-[1.02] transition-transform duration-200" href="/venues">
                    <span class="material-symbols-outlined">meeting_room</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Venues</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl hover:scale-[1.02] transition-transform duration-200" href="/reports">
                    <span class="material-symbols-outlined">assessment</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Reports</span>
                </Link>
            </nav>
            <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-1">
                <Link class="w-full flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-all" href="/help">
                    <span class="material-symbols-outlined">help_outline</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Support</span>
                </Link>
                <Link class="w-full flex items-center gap-3 px-4 py-3 text-error hover:bg-error/10 rounded-xl transition-all" href="/logout" method="post" as="button">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Sign Out</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0 overflow-auto">
            <!-- TopNavBar -->
            <header class="bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-lg shadow-green-500/5 dark:shadow-none sticky top-0 z-40 w-full px-6 py-3 flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h2 class="text-xl font-black tracking-tighter text-zinc-900 dark:text-zinc-50">Academic Sessions</h2>
                    <div class="hidden md:flex items-center bg-zinc-100/50 dark:bg-zinc-800/50 px-4 py-2 rounded-xl w-96">
                        <span class="material-symbols-outlined text-zinc-400 mr-2">search</span>
                        <input class="bg-transparent border-none focus:ring-0 text-sm w-full" placeholder="Search sessions..." type="text" />
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="p-2 text-zinc-600 dark:text-zinc-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200 rounded-full relative">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full ring-2 ring-white"></span>
                    </button>
                    <button class="p-2 text-zinc-600 dark:text-zinc-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200 rounded-full">
                        <span class="material-symbols-outlined">settings</span>
                    </button>
                    <div class="h-8 w-px bg-zinc-200 mx-2"></div>
                    <div class="flex items-center gap-3 cursor-pointer group">
                        <img alt="User profile" class="w-8 h-8 rounded-full border-2 border-primary/20 group-hover:border-primary transition-all" data-alt="User profile avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuANioJ70meAUEqPlwZEoHX_vv4FBaBHATBVmikru8yS3fRRZMCjOz3W7dsNysiUpp7BxFk4_OBymCPgwh8Zac6325ISRfcJaFnXnF3iN87fhWCoRlSVSVkzjKVKlKPLkxGj5nirAUPHrO5ehTWMSIJcDeaiwlzvPYhcbNqDWNSBlcGmoqVUp2rfn85DMOZU_pETwRgSPqdg2-jrX-guOIl4JW-ezREnVw-CJKLydtGcQ9SyC2YBePbU7qlPT90h4hRCVRvtaGRUUsg" />
                        <span class="text-sm font-bold text-on-surface hidden sm:block">Exam Officer</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-4 md:p-8 max-w-7xl mx-auto">
                <!-- Hero / Header Section -->
                <div class="flex flex-col gap-4 mb-8 md:mb-12">
                    <div>
                        <nav class="flex items-center gap-2 mb-4">
                            <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[9px] md:text-[10px] text-primary">Academic Management</span>
                            <span class="material-symbols-outlined text-[10px] md:text-[12px] text-zinc-400">chevron_right</span>
                            <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[9px] md:text-[10px] text-zinc-400">Sessions</span>
                        </nav>
                        <h2 class="text-3xl md:text-5xl font-black text-on-surface tracking-tighter leading-[1.1]">
                            Session<br /><span class="text-primary">Lifecycle.</span>
                        </h2>
                    </div>
                    <button @click="handleCreate" class="w-full md:w-auto flex items-center justify-center gap-3 bg-primary text-zinc-900 font-bold px-6 md:px-8 py-3 md:py-4 rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        <span class="material-symbols-outlined">add</span>
                        <span>Create New Session</span>
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
                    <div class="bg-surface p-3 md:p-5 rounded-2xl shadow-lg shadow-zinc-200/50 flex flex-col justify-between border border-outline-variant/10">
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[8px] md:text-[10px] text-zinc-500">Total Sessions</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-xl md:text-3xl font-black text-on-surface">{{ stats.totalSessions }}</span>
                            <span class="text-primary text-xs font-bold">All time</span>
                        </div>
                    </div>
                    <div class="bg-surface p-3 md:p-5 rounded-2xl shadow-lg shadow-zinc-200/50 flex flex-col justify-between border border-outline-variant/10">
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[8px] md:text-[10px] text-zinc-500">Active Session</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-xl md:text-3xl font-black text-on-surface">{{ stats.activeSession ? 1 : 0 }}</span>
                            <span v-if="stats.activeSession" class="text-emerald-600 text-xs font-bold truncate">● {{ stats.activeSession.name }}</span>
                            <span v-else class="text-zinc-400 text-xs font-bold">None</span>
                        </div>
                    </div>
                    <div class="bg-surface p-3 md:p-5 rounded-2xl shadow-lg shadow-zinc-200/50 flex flex-col justify-between border border-outline-variant/10">
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[8px] md:text-[10px] text-zinc-500">Upcoming</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-xl md:text-3xl font-black text-on-surface">{{ stats.upcomingSessions }}</span>
                            <span class="text-blue-600 text-xs font-bold">Future</span>
                        </div>
                    </div>
                    <div class="bg-surface p-3 md:p-5 rounded-2xl shadow-lg shadow-zinc-200/50 flex flex-col justify-between border border-outline-variant/10">
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[8px] md:text-[10px] text-zinc-500">Archived</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-xl md:text-3xl font-black text-on-surface">{{ stats.archivedSessions }}</span>
                            <span class="text-zinc-400 text-xs font-bold">Done</span>
                        </div>
                    </div>
                </div>

                <!-- Sessions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <!-- Active Session Card -->
                    <div v-if="stats.activeSession" class="md:col-span-2 bg-white rounded-3xl p-8 shadow-xl shadow-zinc-200/50 relative overflow-hidden group">
                        <div class="absolute -top-12 -right-12 w-48 h-48 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px] uppercase tracking-widest mb-4">
                                    <span class="w-1.5 h-1.5 bg-emerald-600 rounded-full mr-2 animate-pulse"></span>
                                    Currently Active
                                </span>
                                <h3 class="text-4xl font-black text-on-surface tracking-tight">{{ stats.activeSession.name }}</h3>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8 relative z-10">
                            <div>
                                <p class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px] text-zinc-400 mb-1">Start Date</p>
                                <p class="font-bold text-on-surface">{{ formatDate(stats.activeSession.start_date) }}</p>
                            </div>
                            <div>
                                <p class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px] text-zinc-400 mb-1">End Date</p>
                                <p class="font-bold text-on-surface">{{ formatDate(stats.activeSession.end_date) }}</p>
                            </div>
                            <div>
                                <p class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px] text-zinc-400 mb-1">Duration</p>
                                <p class="font-bold text-on-surface">{{ Math.ceil((new Date(stats.activeSession.end_date) - new Date(stats.activeSession.start_date)) / (1000 * 60 * 60 * 24)) }} days</p>
                            </div>
                        </div>
                        <div class="flex gap-4 relative z-10">
                            <button @click="editSession(stats.activeSession)" class="flex-1 bg-zinc-900 text-white font-bold py-3 rounded-xl hover:bg-zinc-800 transition-colors">
                                Manage Session
                            </button>
                            <button @click="deleteSession(stats.activeSession)" class="px-4 py-3 border border-zinc-200 rounded-xl hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-colors">
                                <span class="material-symbols-outlined align-middle">delete</span>
                            </button>
                        </div>
                    </div>

                    <!-- No Active Session -->
                    <div v-else class="md:col-span-2 bg-surface-container-low border-2 border-dashed border-zinc-300 rounded-3xl p-8 flex flex-col justify-center items-center text-center">
                        <span class="material-symbols-outlined text-6xl text-zinc-300 mb-4">event_busy</span>
                        <h3 class="text-2xl font-black text-zinc-500 mb-2">No Active Session</h3>
                        <p class="text-zinc-400 mb-6">Create and activate an academic session to get started</p>
                        <button @click="handleCreate" class="bg-primary text-zinc-900 font-bold px-6 py-3 rounded-xl hover:scale-105 transition-transform">
                            Create Session
                        </button>
                    </div>

                    <!-- Upcoming Sessions -->
                    <div class="bg-surface-container-low border border-outline-variant/20 rounded-3xl p-8 flex flex-col justify-between">
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] uppercase tracking-widest mb-6">
                                Upcoming
                            </span>
                            <div v-if="stats.upcomingSessions > 0">
                                <div v-for="session in sessions.filter(s => isUpcoming(s))" :key="session.id" class="mb-4 last:mb-0">
                                    <h3 class="text-xl font-black text-on-surface tracking-tight mb-2">{{ session.name }}</h3>
                                    <p class="text-sm text-zinc-500 mb-4">{{ formatDate(session.start_date) }} - {{ formatDate(session.end_date) }}</p>
                                    <div class="space-y-2">
                                        <button @click="setActiveSession(session)" class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-xl hover:bg-blue-700 transition-colors text-sm">
                                            Set Active
                                        </button>
                                        <button @click="editSession(session)" class="w-full border border-zinc-300 text-zinc-700 font-bold py-2.5 rounded-xl hover:bg-zinc-50 transition-colors text-sm">
                                            Edit Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <p class="text-sm text-zinc-500 mb-6">No upcoming sessions scheduled</p>
                                <button @click="handleCreate" class="w-full border-2 border-zinc-900 text-zinc-900 font-bold py-3 rounded-xl hover:bg-zinc-900 hover:text-white transition-all">
                                    Schedule Session
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Archived Sessions Table -->
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-zinc-200/50">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-black text-on-surface tracking-tight">Previous Archives</h3>
                        <span class="text-xs font-bold text-zinc-500">{{ stats.archivedSessions }} archived sessions</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-zinc-100">
                                    <th class="pb-4 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Session Name</th>
                                    <th class="pb-4 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Start Date</th>
                                    <th class="pb-4 text-[10px] font-bold uppercase tracking-widest text-zinc-500">End Date</th>
                                    <th class="pb-4 text-[10px] font-bold uppercase tracking-widest text-zinc-500">Status</th>
                                    <th class="pb-4 text-[10px] font-bold uppercase tracking-widest text-zinc-500 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-50">
                                <tr v-for="session in sessions.filter(s => isArchived(s))" :key="session.id" class="hover:bg-zinc-50 transition-colors">
                                    <td class="py-4">
                                        <p class="font-bold text-on-surface">{{ session.name }}</p>
                                    </td>
                                    <td class="py-4 text-sm text-zinc-600">{{ formatDate(session.start_date) }}</td>
                                    <td class="py-4 text-sm text-zinc-600">{{ formatDate(session.end_date) }}</td>
                                    <td class="py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-600 uppercase">Archived</span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button @click="editSession(session)" class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-400 hover:text-primary transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <button @click="deleteSession(session)" class="p-2 hover:bg-red-50 rounded-lg text-zinc-400 hover:text-red-600 transition-colors" title="Delete">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="stats.archivedSessions === 0">
                                    <td colspan="5" class="py-12 text-center">
                                        <span class="material-symbols-outlined text-zinc-300 text-4xl mb-2">archive</span>
                                        <p class="text-zinc-500 text-sm">No archived sessions yet</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Create Session Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="closeCreateModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-black text-on-surface">Create Academic Session</h3>
                        <p class="text-xs text-zinc-500 mt-1">Define a new academic session period</p>
                    </div>
                    <button @click="closeCreateModal" class="p-2 hover:bg-zinc-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Session Name</label>
                        <input v-model="createForm.name" type="text" placeholder="e.g., 2024/2025" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-zinc-500 mb-2 block">Start Date</label>
                            <input v-model="createForm.start_date" type="date" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-zinc-500 mb-2 block">End Date</label>
                            <input v-model="createForm.end_date" type="date" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-zinc-50 rounded-xl">
                        <input v-model="createForm.is_active" type="checkbox" id="active" class="w-4 h-4 text-primary rounded focus:ring-primary"/>
                        <label for="active" class="text-sm font-bold text-zinc-700">Set as active session immediately</label>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="closeCreateModal" class="flex-1 py-3 bg-zinc-100 text-zinc-700 rounded-xl font-bold text-sm">Cancel</button>
                    <button @click="submitCreate" class="flex-1 py-3 bg-primary text-zinc-900 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Create Session
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Session Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="closeEditModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-black text-on-surface">Edit Session</h3>
                        <p class="text-xs text-zinc-500 mt-1">{{ selectedSession?.name }}</p>
                    </div>
                    <button @click="closeEditModal" class="p-2 hover:bg-zinc-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Session Name</label>
                        <input v-model="editForm.name" type="text" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-zinc-500 mb-2 block">Start Date</label>
                            <input v-model="editForm.start_date" type="date" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-zinc-500 mb-2 block">End Date</label>
                            <input v-model="editForm.end_date" type="date" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-zinc-50 rounded-xl">
                        <input v-model="editForm.is_active" type="checkbox" id="edit-active" class="w-4 h-4 text-primary rounded focus:ring-primary"/>
                        <label for="edit-active" class="text-sm font-bold text-zinc-700">Set as active session</label>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="closeEditModal" class="flex-1 py-3 bg-zinc-100 text-zinc-700 rounded-xl font-bold text-sm">Cancel</button>
                    <button @click="submitEdit" class="flex-1 py-3 bg-primary text-zinc-900 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </body>
</template>
