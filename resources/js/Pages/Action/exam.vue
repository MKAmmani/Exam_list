<script setup>
import { Link, router, Head, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    exams: Array,
    courses: Array,
    sessions: Array,
    venues: Array,
    filters: Object,
});

const page = usePage();
const showCreateModal = ref(false);
const showAllocateModal = ref(false);
const selectedExam = ref(null);
const createForm = ref({
    course_id: '',
    academic_session_id: '',
    date: '',
    start_time: '',
    end_time: '',
    duration_minutes: 120,
});

// Watch for flash messages
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
    const activeSession = props.sessions?.find(s => s.is_active);
    if (activeSession) {
        createForm.value.academic_session_id = activeSession.id;
    }
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    createForm.value = {
        course_id: '',
        academic_session_id: '',
        date: '',
        start_time: '',
        end_time: '',
        duration_minutes: 120,
    };
};

const submitCreate = () => {
    router.post('/exams', createForm.value, {
        onSuccess: () => {
            closeCreateModal();
        },
        onError: (errors) => {
            alert(Object.values(errors).join('\n'));
        },
    });
};

const runAllocation = (exam) => {
    selectedExam.value = exam;
    showAllocateModal.value = true;
};

const closeAllocateModal = () => {
    showAllocateModal.value = false;
    selectedExam.value = null;
};

const submitAllocation = () => {
    if (!selectedExam.value) return;
    
    router.post(`/allocations/run`, { 
        exam_id: selectedExam.value.id 
    }, {
        preserveScroll: true,
        onSuccess: () => {
            closeAllocateModal();
        },
        onError: (errors) => {
            alert(errors.exam || errors.allocation || errors.error || 'Allocation failed');
        },
    });
};

const deleteExam = (exam) => {
    if (confirm(`Are you sure you want to delete ${exam.course?.code}?`)) {
        router.delete(`/exams/${exam.id}`, {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatTime = (timeString) => {
    if (!timeString) return 'N/A';
    return timeString;
};

const getStatusColor = (isAllocated) => {
    return isAllocated 
        ? 'bg-emerald-100 text-emerald-700' 
        : 'bg-red-100 text-red-700';
};

const stats = computed(() => ({
    totalExams: props.exams?.length || 0,
    allocated: props.exams?.filter(e => e.is_allocated).length || 0,
    pending: props.exams?.filter(e => !e.is_allocated).length || 0,
    today: props.exams?.filter(e => {
        const today = new Date().toISOString().split('T')[0];
        return e.date === today;
    }).length || 0,
}));
</script>

<template>
    <Head title="Exams - Exam Officer" />
    <body class="bg-background text-on-background selection:bg-primary-container selection:text-on-primary-container">
        <!-- TopNavBar -->
        <header class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-lg flex justify-between items-center px-4 md:px-6 h-16 w-full font-public-sans text-on-surface">
            <div class="flex items-center gap-4 md:gap-8">
                <Link href="/exam-officer" class="text-lg md:text-xl font-black tracking-tighter text-zinc-900 dark:text-zinc-50">BUK Exam Portal</Link>
                <div class="hidden md:flex items-center bg-zinc-100/50 dark:bg-zinc-800/50 rounded-full px-4 py-1.5 gap-2 border border-transparent focus-within:border-primary/30 transition-all">
                    <span class="material-symbols-outlined text-zinc-400 text-sm">search</span>
                    <input class="bg-transparent border-none focus:ring-0 text-sm w-64 placeholder:text-zinc-400" placeholder="Search exams, courses..." type="text" />
                </div>
            </div>
            <div class="flex items-center gap-2 md:gap-4">
                <button class="p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors active:scale-95 duration-200 text-zinc-500">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
                <button class="p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors active:scale-95 duration-200 text-zinc-500">
                    <span class="material-symbols-outlined">help</span>
                </button>
                <div class="h-8 w-8 rounded-full overflow-hidden border-2 border-primary/20">
                    <img alt="User profile avatar" class="h-full w-full object-cover" data-alt="Exam officer profile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB0Boo9bB8JOc6J5bDvQ_AYBuJNGQ1ASrQQ9zhzdSKcu_8zXS4UehYRGT4tSU0t3U9k9MEaBwSTIFYP7bQWnYv2WPVCfUHwpPtkvd8VCN36wnkqBSIBwqGy4ozS2pfT_UI8uusQUlErs8KXNCeGAckQxg33fg1DVxV6ys4bdA0brPeqUCI0ZMrWeKfvtIIXsdJEQxfnHK-0oaEQH2cxaOD9n2HZ_cqk2nE7qCSJ5Vu0szuHn6l51iIVbGBSoRy8wgHDf93YqkwP5Ds" />
                </div>
            </div>
        </header>

        <!-- SideNavBar -->
        <aside class="fixed left-0 top-0 h-full w-64 z-40 bg-white dark:bg-zinc-950 border-r border-zinc-100 dark:border-zinc-800 flex flex-col pt-20 pb-6 hidden md:flex">
            <div class="px-6 mb-8 flex items-center gap-3">
                <div class="h-10 w-10 bg-primary-container rounded-xl flex items-center justify-center text-on-primary-container shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">school</span>
                </div>
                <div>
                    <h2 class="text-lg font-black text-zinc-900 dark:text-zinc-50 leading-tight">BUK Portal</h2>
                    <p class="text-[10px] font-bold tracking-widest uppercase text-zinc-400">Exam Officer</p>
                </div>
            </div>
            <nav class="flex-1 px-4 space-y-1">
                <Link class="flex items-center gap-3 px-4 py-3 rounded-xl font-public-sans text-sm font-semibold tracking-wide uppercase text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-200 ease-in-out" href="/exam-officer">
                    <span class="material-symbols-outlined">dashboard</span>
                    Dashboard
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 rounded-xl font-public-sans text-sm font-semibold tracking-wide uppercase text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-200 ease-in-out" href="/academic-sessions">
                    <span class="material-symbols-outlined">calendar_month</span>
                    Sessions
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 rounded-xl font-public-sans text-sm font-semibold tracking-wide uppercase text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-200 ease-in-out" href="/courses">
                    <span class="material-symbols-outlined">book</span>
                    Courses
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 rounded-xl font-public-sans text-sm font-semibold tracking-wide uppercase text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-200 ease-in-out" href="/students">
                    <span class="material-symbols-outlined">group</span>
                    Students
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 rounded-xl font-public-sans text-sm font-semibold tracking-wide uppercase text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-200 ease-in-out" href="/venues">
                    <span class="material-symbols-outlined">location_on</span>
                    Venues
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 rounded-xl font-public-sans text-sm font-semibold tracking-wide uppercase text-green-600 dark:text-green-400 border-r-4 border-green-500 bg-green-50/50 dark:bg-green-900/10 transition-all duration-200 ease-in-out" href="/exams">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assignment</span>
                    Exams
                </Link>
            </nav>
            <div class="mt-auto px-4 space-y-1">
                <Link class="flex items-center gap-3 px-4 py-3 rounded-xl font-public-sans text-sm font-semibold tracking-wide uppercase text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-200 ease-in-out" href="/allocations">
                    <span class="material-symbols-outlined">assignment_turned_in</span>
                    Allocations
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 rounded-xl font-public-sans text-sm font-semibold tracking-wide uppercase text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all duration-200 ease-in-out" href="/logout" method="post" as="button">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="md:ml-64 pt-20 pb-12 px-4 md:px-6 lg:px-10 min-h-screen">
            <!-- Header & Title -->
            <div class="mb-6 md:mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4 md:gap-6">
                <div class="space-y-1 md:space-y-2">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2.5 py-1 rounded-full bg-primary/10 text-primary text-[8px] md:text-[10px] font-bold tracking-widest uppercase">Admin Portal</span>
                        <span class="text-outline-variant">•</span>
                        <span class="text-zinc-400 text-[8px] md:text-[10px] font-bold tracking-widest uppercase">BUK Faculty of Computing</span>
                    </div>
                    <h1 class="text-2xl md:text-4xl lg:text-5xl font-black tracking-tight text-on-surface leading-[1.1]">Exam Timetable</h1>
                    <p class="text-on-surface-variant text-xs md:text-sm max-w-2xl font-body">Manage, allocate, and monitor the examination schedule for the current academic session.</p>
                </div>
                <div class="flex items-center gap-2 md:gap-3">
                    <button @click="handleCreate" class="px-4 md:px-6 py-2.5 md:py-3 bg-primary text-on-primary rounded-xl font-bold text-xs md:text-sm shadow-lg shadow-primary/30 hover:shadow-xl transition-all flex items-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined text-base md:text-lg">add</span>
                        <span class="hidden sm:inline">New Exam Entry</span>
                        <span class="sm:hidden">Add Exam</span>
                    </button>
                </div>
            </div>

            <!-- Bento Stats Section -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg border border-transparent hover:border-primary/20 transition-all group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 md:p-3 rounded-xl bg-zinc-100 group-hover:bg-primary-container/20 transition-colors">
                            <span class="material-symbols-outlined text-zinc-500 group-hover:text-primary transition-colors text-base md:text-lg">assignment</span>
                        </div>
                        <span class="text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-zinc-400">Total Exams</span>
                    </div>
                    <div class="text-2xl md:text-3xl font-black text-on-surface">{{ stats.totalExams }}</div>
                    <div class="mt-2 text-[10px] md:text-xs text-zinc-500 flex items-center gap-1">
                        <span class="text-primary font-bold">All sessions</span>
                    </div>
                </div>
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg border border-transparent hover:border-red-500/20 transition-all group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 md:p-3 rounded-xl bg-red-100 group-hover:bg-red-100/60 transition-colors">
                            <span class="material-symbols-outlined text-red-600" style="font-variation-settings: 'FILL' 1;">pending_actions</span>
                        </div>
                        <span class="text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-red-600">Pending Allocation</span>
                    </div>
                    <div class="text-2xl md:text-3xl font-black text-on-surface">{{ stats.pending }}</div>
                    <div class="mt-2 text-[10px] md:text-xs text-zinc-500 flex items-center gap-1">
                        Requires immediate action
                    </div>
                </div>
                <div class="bg-white p-4 md:p-6 rounded-2xl shadow-lg border border-transparent hover:border-emerald-500/20 transition-all group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2 md:p-3 rounded-xl bg-emerald-100 group-hover:bg-emerald-100/60 transition-colors">
                            <span class="material-symbols-outlined text-emerald-600">verified</span>
                        </div>
                        <span class="text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-zinc-400">Allocated</span>
                    </div>
                    <div class="text-2xl md:text-3xl font-black text-on-surface">{{ stats.allocated }}</div>
                    <div class="mt-2 text-[10px] md:text-xs text-zinc-500 flex items-center gap-1">
                        <span class="text-emerald-600 font-bold">{{ stats.totalExams > 0 ? Math.round((stats.allocated / stats.totalExams) * 100) : 0 }}%</span> completion rate
                    </div>
                </div>
                <div class="relative overflow-hidden bg-primary p-4 md:p-6 rounded-2xl shadow-lg group">
                    <div class="absolute -right-2 -bottom-2 md:-right-4 md:-bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <span class="material-symbols-outlined text-6xl md:text-9xl">today</span>
                    </div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div class="p-2 md:p-3 rounded-xl bg-white/20">
                            <span class="material-symbols-outlined text-white text-base md:text-lg">bolt</span>
                        </div>
                        <span class="text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-white/80">Today</span>
                    </div>
                    <div class="text-2xl md:text-3xl font-black text-white relative z-10">{{ stats.today }}</div>
                    <div class="mt-2 text-[10px] md:text-xs text-white/90 relative z-10 font-medium">
                        Exams scheduled
                    </div>
                </div>
            </div>

            <!-- Exams Table -->
            <div class="bg-white rounded-2xl md:rounded-3xl shadow-xl overflow-hidden border border-zinc-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-zinc-50/50">
                                <th class="px-4 md:px-6 py-3 md:py-5 text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-zinc-400 border-b border-zinc-100 whitespace-nowrap">Course Code</th>
                                <th class="px-4 md:px-6 py-3 md:py-5 text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-zinc-400 border-b border-zinc-100">Course Title</th>
                                <th class="px-4 md:px-6 py-3 md:py-5 text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-zinc-400 border-b border-zinc-100 whitespace-nowrap">Date & Time</th>
                                <th class="px-4 md:px-6 py-3 md:py-5 text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-zinc-400 border-b border-zinc-100 text-center whitespace-nowrap">Students</th>
                                <th class="px-4 md:px-6 py-3 md:py-5 text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-zinc-400 border-b border-zinc-100 whitespace-nowrap">Status</th>
                                <th class="px-4 md:px-6 py-3 md:py-5 text-[8px] md:text-[10px] font-bold tracking-widest uppercase text-zinc-400 border-b border-zinc-100 text-right whitespace-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-50">
                            <tr v-for="exam in exams" :key="exam.id" class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-4 md:px-6 py-3 md:py-5">
                                    <span class="font-bold text-zinc-900 text-xs md:text-sm whitespace-nowrap">{{ exam.course?.code }}</span>
                                </td>
                                <td class="px-4 md:px-6 py-3 md:py-5">
                                    <span class="text-xs md:text-sm font-medium text-zinc-700">{{ exam.course?.title }}</span>
                                </td>
                                <td class="px-4 md:px-6 py-3 md:py-5">
                                    <div class="text-xs md:text-sm font-semibold text-zinc-900 whitespace-nowrap">{{ formatDate(exam.date) }}</div>
                                    <div class="text-[10px] md:text-xs text-zinc-500 whitespace-nowrap">{{ formatTime(exam.start_time) }} - {{ formatTime(exam.end_time) }}</div>
                                </td>
                                <td class="px-4 md:px-6 py-3 md:py-5 text-center">
                                    <div class="flex items-center gap-1 md:gap-2 justify-center">
                                        <span class="text-xs md:text-sm font-bold text-zinc-900">{{ exam.students_count || 0 }}</span>
                                        <span class="text-[8px] md:text-[10px] text-zinc-400 hidden md:inline">Students</span>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-3 md:py-5">
                                    <span :class="['inline-flex items-center gap-1 md:gap-1.5 px-2 md:px-3 py-1 rounded-full text-[8px] md:text-[10px] font-bold tracking-wide uppercase whitespace-nowrap', getStatusColor(exam.is_allocated)]">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="exam.is_allocated ? 'bg-emerald-600' : 'bg-red-600 animate-pulse'"></span>
                                        {{ exam.is_allocated ? 'Allocated' : 'Not Allocated' }}
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 py-3 md:py-5 text-right">
                                    <div class="flex justify-end gap-1 md:gap-2">
                                        <Link v-if="exam.is_allocated" :href="`/allocations/${exam.id}`" class="p-1.5 md:p-2 text-zinc-400 hover:text-primary transition-colors hover:bg-primary/10 rounded-lg" title="View Allocation">
                                            <span class="material-symbols-outlined text-base md:text-lg">visibility</span>
                                        </Link>
                                        <button v-if="exam.is_allocated" class="p-1.5 md:p-2 text-zinc-400 hover:text-primary transition-colors hover:bg-primary/10 rounded-lg" title="Print">
                                            <span class="material-symbols-outlined text-base md:text-lg">print</span>
                                        </button>
                                        <button v-if="!exam.is_allocated" @click="runAllocation(exam)" class="px-3 md:px-4 py-1.5 md:py-2 bg-primary text-on-primary rounded-lg font-bold text-[10px] md:text-xs shadow-md shadow-primary/20 hover:shadow-lg transition-all active:scale-95 flex items-center gap-1 md:gap-2">
                                            <span class="material-symbols-outlined text-sm">auto_awesome</span>
                                            <span class="hidden md:inline">Run Allocation</span>
                                            <span class="md:hidden">Allocate</span>
                                        </button>
                                        <button @click="deleteExam(exam)" class="p-1.5 md:p-2 text-zinc-400 hover:text-red-600 transition-colors hover:bg-red-50 rounded-lg" title="Delete">
                                            <span class="material-symbols-outlined text-base md:text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!exams || exams.length === 0">
                                <td colspan="6" class="px-4 md:px-6 py-12 text-center">
                                    <span class="material-symbols-outlined text-zinc-300 text-3xl md:text-4xl mb-2">event_busy</span>
                                    <p class="text-zinc-500 text-xs md:text-sm">No exams scheduled. Create your first exam.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <!-- Create Exam Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="closeCreateModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-black text-on-surface">Create Exam</h3>
                        <p class="text-xs text-zinc-500 mt-1">Schedule a new examination</p>
                    </div>
                    <button @click="closeCreateModal" class="p-2 hover:bg-zinc-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Course *</label>
                        <select v-model="createForm.course_id" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                            <option value="">Select Course</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.code }} - {{ course.title }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Academic Session *</label>
                        <select v-model="createForm.academic_session_id" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                            <option value="">Select Session</option>
                            <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Exam Date *</label>
                        <input v-model="createForm.date" type="date" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-zinc-500 mb-2 block">Start Time *</label>
                            <input v-model="createForm.start_time" type="time" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-zinc-500 mb-2 block">End Time *</label>
                            <input v-model="createForm.end_time" type="time" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Duration (minutes)</label>
                        <input v-model.number="createForm.duration_minutes" type="number" min="30" step="30" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="closeCreateModal" class="flex-1 py-3 bg-zinc-100 text-zinc-700 rounded-xl font-bold text-sm">Cancel</button>
                    <button @click="submitCreate" class="flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Create Exam
                    </button>
                </div>
            </div>
        </div>

        <!-- Run Allocation Modal -->
        <div v-if="showAllocateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="closeAllocateModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-black text-on-surface">Run Allocation</h3>
                        <p class="text-xs text-zinc-500 mt-1">{{ selectedExam?.course?.code }} - {{ formatDate(selectedExam?.date) }}</p>
                    </div>
                    <button @click="closeAllocateModal" class="p-2 hover:bg-zinc-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="space-y-4 mb-6">
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-amber-600 text-lg">info</span>
                            <div>
                                <p class="text-sm font-bold text-amber-800 mb-1">Allocation Rules</p>
                                <ul class="text-xs text-amber-700 space-y-1">
                                    <li>• Venues will be filled to 50% capacity only</li>
                                    <li>• Students are distributed across multiple venues if needed</li>
                                    <li>• Sequential seat numbers generated (A-001, A-002, etc.)</li>
                                    <li>• {{ selectedExam?.students_count || 0 }} students will be allocated</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-zinc-50 rounded-xl">
                        <p class="text-xs font-bold text-zinc-500 mb-2">Exam Details</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-zinc-400">Course:</span>
                                <p class="font-bold">{{ selectedExam?.course?.code }}</p>
                            </div>
                            <div>
                                <span class="text-zinc-400">Time:</span>
                                <p class="font-bold">{{ formatTime(selectedExam?.start_time) }} - {{ formatTime(selectedExam?.end_time) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button @click="closeAllocateModal" class="flex-1 py-3 bg-zinc-100 text-zinc-700 rounded-xl font-bold text-sm">Cancel</button>
                    <button @click="submitAllocation" class="flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">auto_awesome</span>
                        Run Allocation
                    </button>
                </div>
            </div>
        </div>
    </body>
</template>
