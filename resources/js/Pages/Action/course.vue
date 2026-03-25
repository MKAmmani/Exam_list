<script setup>
import { Link, router, Head, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    courses: Array,
    sessions: Array,
    filters: Object,
});

const page = usePage();
const search = ref(props.filters?.search || '');
const semester = ref(props.filters?.semester || '');
const academicSessionId = ref(props.filters?.academic_session_id || '');
const showEditDrawer = ref(false);
const showImportModal = ref(false);
const showCreateModal = ref(false);
const showStudentsModal = ref(false);
const selectedCourse = ref(null);
const importFile = ref(null);
const importSessionId = ref('');
const studentImportFile = ref(null);
const showManualEntry = ref(false);
const manualStudents = ref([]);
const manualStudentForm = ref({
    registration_number: '',
    name: '',
    department: '',
    level: '',
});
const editForm = ref({
    code: '',
    title: '',
    credit_hours: 3,
    semester: 1,
    academic_session_id: '',
});
const createForm = ref({
    code: '',
    title: '',
    credit_hours: 3,
    semester: 1,
    academic_session_id: '',
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

const handleSearch = () => {
    router.get('/courses', { 
        search: search.value, 
        semester: semester.value, 
        academic_session_id: academicSessionId.value 
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    search.value = '';
    semester.value = '';
    academicSessionId.value = '';
    router.get('/courses');
};

const editCourse = (course) => {
    selectedCourse.value = course;
    editForm.value = {
        code: course.code,
        title: course.title,
        credit_hours: course.credit_hours,
        semester: course.semester,
        academic_session_id: course.academic_session_id,
    };
    showEditDrawer.value = true;
};

const closeDrawer = () => {
    showEditDrawer.value = false;
    selectedCourse.value = null;
    editForm.value = {
        code: '',
        title: '',
        credit_hours: 3,
        semester: 1,
        academic_session_id: '',
    };
};

const submitEdit = () => {
    if (!selectedCourse.value) return;
    
    router.put(`/courses/${selectedCourse.value.id}`, editForm.value, {
        onSuccess: () => {
            closeDrawer();
        },
        onError: (errors) => {
            alert(Object.values(errors).join('\n'));
        },
    });
};

const handleImport = () => {
    showImportModal.value = true;
    const activeSession = props.sessions?.find(s => s.is_active);
    if (activeSession) {
        importSessionId.value = activeSession.id;
    }
};

const closeImportModal = () => {
    showImportModal.value = false;
    importFile.value = null;
    importSessionId.value = '';
};

const submitImport = () => {
    if (!importFile.value) {
        alert('Please select a file');
        return;
    }
    if (!importSessionId.value) {
        alert('Please select an academic session');
        return;
    }

    const formData = new FormData();
    formData.append('file', importFile.value);
    formData.append('academic_session_id', importSessionId.value);

    router.post('/courses/import', formData, {
        onSuccess: () => {
            closeImportModal();
        },
        onError: (errors) => {
            alert(errors.file || errors.academic_session_id || 'Import failed');
        },
    });
};

const handleExport = () => {
    if (!academicSessionId.value) {
        alert('Please select an academic session from the filters above first');
        return;
    }
    // Open export in new window to trigger download
    const downloadUrl = `/courses/export?academic_session_id=${academicSessionId.value}`;
    window.open(downloadUrl, '_blank');
};

const handleDownloadTemplate = () => {
    // Download template without requiring session selection
    window.open('/courses/template', '_blank');
};

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
        code: '',
        title: '',
        credit_hours: 3,
        semester: 1,
        academic_session_id: '',
    };
};

const submitCreate = () => {
    router.post('/courses', createForm.value, {
        onSuccess: () => {
            closeCreateModal();
        },
        onError: (errors) => {
            alert(Object.values(errors).join('\n'));
        },
    });
};

const viewStudents = (course, courseName) => {
    selectedCourse.value = course;
    showStudentsModal.value = true;
};

const closeStudentsModal = () => {
    showStudentsModal.value = false;
    showManualEntry.value = false;
    selectedCourse.value = null;
    studentImportFile.value = null;
    manualStudents.value = [];
    manualStudentForm.value = {
        registration_number: '',
        name: '',
        department: '',
        level: '',
    };
};

const addManualStudent = () => {
    if (!manualStudentForm.value.registration_number || !manualStudentForm.value.name) {
        alert('Please fill in at least Registration Number and Name');
        return;
    }
    
    manualStudents.value.push({ ...manualStudentForm.value });
    manualStudentForm.value = {
        registration_number: '',
        name: '',
        department: '',
        level: '',
    };
};

const removeManualStudent = (index) => {
    manualStudents.value.splice(index, 1);
};

const submitManualStudents = () => {
    if (manualStudents.value.length === 0) {
        alert('Please add at least one student');
        return;
    }
    
    router.post(`/courses/${selectedCourse.value.id}/students`, {
        students: manualStudents.value,
        academic_session_id: importSessionId.value || props.sessions?.find(s => s.is_active)?.id,
    }, {
        preserveState: false,
        onSuccess: () => {
            closeStudentsModal();
        },
        onError: (errors) => {
            alert(Object.values(errors).join('\n'));
        },
    });
};

const submitStudentImport = () => {
    if (!studentImportFile.value || !selectedCourse.value) {
        alert('Please select a file');
        return;
    }
    
    const formData = new FormData();
    formData.append('file', studentImportFile.value);
    formData.append('academic_session_id', importSessionId.value || props.sessions?.find(s => s.is_active)?.id);
    
    router.post(`/courses/${selectedCourse.value.id}/enroll-students`, formData, {
        onSuccess: () => {
            closeStudentsModal();
        },
        onError: (errors) => {
            alert(errors.file || errors.academic_session_id || 'Import failed');
        },
    });
};

const stats = computed(() => ({
    totalCourses: props.courses?.length || 0,
    totalStudents: 2840,
    avgEnrollment: 45,
    pendingReviews: 8,
}));
</script>

<template>
    <Head title="Courses - Exam Officer" />
    <body class="bg-background text-on-background flex h-screen overflow-hidden">
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
                <Link class="flex items-center gap-3 px-4 py-3 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl hover:scale-[1.02] transition-transform duration-200" href="/academic-sessions">
                    <span class="material-symbols-outlined">event_repeat</span>
                    <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px]">Sessions</span>
                </Link>
                <Link class="flex items-center gap-3 px-4 py-3 bg-primary text-on-primary rounded-xl shadow-lg shadow-green-500/20 translate-x-1 transition-all" href="/courses">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">school</span>
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
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
            <!-- TopAppBar Component -->
            <header class="bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md sticky top-0 z-40 w-full px-6 py-3 flex justify-between items-center shadow-lg shadow-green-500/5 dark:shadow-none">
                <div class="flex items-center gap-4 flex-1">
                    <span class="md:hidden material-symbols-outlined text-zinc-600">menu</span>
                    <h2 class="text-xl font-black tracking-tighter text-zinc-900 dark:text-zinc-50">Course Management</h2>
                    <div class="hidden lg:flex items-center bg-zinc-100 dark:bg-zinc-900 px-4 py-2 rounded-xl w-96 ml-8">
                        <span class="material-symbols-outlined text-zinc-400 text-sm mr-2">search</span>
                        <input v-model="search" @keyup.enter="handleSearch" class="bg-transparent border-none focus:ring-0 text-sm w-full font-['Public_Sans']" placeholder="Search courses, codes..." type="text"/>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button class="p-2 text-zinc-600 dark:text-zinc-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined">notifications</span>
                    </button>
                    <button class="p-2 text-zinc-600 dark:text-zinc-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors duration-200">
                        <span class="material-symbols-outlined">settings</span>
                    </button>
                    <div class="h-8 w-8 rounded-full overflow-hidden ml-2 ring-2 ring-primary/20">
                        <img alt="User profile" class="w-full h-full object-cover" data-alt="Exam officer profile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuChK0sJ_uSm6D0PTw8x-sjL3O3W9kQ5TlacoXTSuckgbU1SVD4d5lo4xTM5WiUNIJJ2WjLhGD7-7DOh22HaM3f7Qua2-xbFeYQYkVX9TnSqfj1PS_rLL40j0239QBEOdnuNh5qEsCRDGQ854nhqhTzvbT1Hfgl_Cjv-NUQ1hoSeMHvKelw7IUz1eQiyVbUGv8tdkqbGM4uYe1eTS91CN0yx9aMEkUAt6RTP5pHLM6tkIfABp3Uh1GE3FwHHmc429SHywibtzS8LQ5k"/>
                    </div>
                </div>
            </header>

            <!-- Page Canvas -->
            <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 md:space-y-6">
                <!-- Page Header & Actions -->
                <div class="flex flex-col gap-4">
                    <div>
                        <h3 class="text-2xl md:text-3xl font-black text-on-surface tracking-tighter leading-none">Course Management</h3>
                        <p class="text-on-surface-variant mt-1 text-sm">BUK Faculty of Computing - Manage courses and enrollments</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="handleImport" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-surface-container-high text-on-surface rounded-xl font-bold text-sm hover:bg-surface-container-highest transition-all">
                            <span class="material-symbols-outlined text-sm">upload</span>
                            <span class="hidden sm:inline">Import CSV</span>
                            <span class="sm:hidden">Import</span>
                        </button>
                        <button @click="handleExport" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-surface-container-high text-on-surface rounded-xl font-bold text-sm hover:bg-surface-container-highest transition-all">
                            <span class="material-symbols-outlined text-sm">download</span>
                            <span class="hidden sm:inline">Export</span>
                            <span class="sm:hidden">Export</span>
                        </button>
                        <button @click="handleCreate" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            <span class="material-symbols-outlined text-sm">add_circle</span>
                            <span class="hidden sm:inline">Add New Course</span>
                            <span class="sm:hidden">Add Course</span>
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                    <div class="bg-surface p-4 md:p-5 rounded-2xl shadow-lg shadow-zinc-200/50 flex flex-col justify-between border border-outline-variant/10">
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[9px] md:text-[10px] text-zinc-500">Total Courses</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl md:text-3xl font-black text-on-surface">{{ stats.totalCourses }}</span>
                            <span class="text-primary text-xs font-bold">Current session</span>
                        </div>
                    </div>
                    <div class="bg-surface p-4 md:p-5 rounded-2xl shadow-lg shadow-zinc-200/50 flex flex-col justify-between border border-outline-variant/10">
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[9px] md:text-[10px] text-zinc-500">Active Students</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl md:text-3xl font-black text-on-surface">{{ stats.totalStudents.toLocaleString() }}</span>
                            <span class="text-secondary text-xs font-bold">↑ 12%</span>
                        </div>
                    </div>
                    <div class="bg-surface p-4 md:p-5 rounded-2xl shadow-lg shadow-zinc-200/50 flex flex-col justify-between border border-outline-variant/10">
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[9px] md:text-[10px] text-zinc-500">Avg. Enrollment</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl md:text-3xl font-black text-on-surface">{{ stats.avgEnrollment }}</span>
                            <span class="text-zinc-400 text-xs font-bold">Per course</span>
                        </div>
                    </div>
                    <div class="bg-surface p-4 md:p-5 rounded-2xl shadow-lg shadow-zinc-200/50 flex flex-col justify-between border border-outline-variant/10 bg-primary/5">
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[9px] md:text-[10px] text-primary">Pending Reviews</span>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-2xl md:text-3xl font-black text-primary">{{ stats.pendingReviews }}</span>
                            <span class="text-primary/60 text-xs font-bold">Syllabus updates</span>
                        </div>
                    </div>
                </div>

                <!-- Filter Bar -->
                <div class="bg-white rounded-2xl p-3 md:p-4 flex flex-wrap gap-2 md:gap-4 items-center shadow-lg shadow-zinc-200/50 border border-outline-variant/10">
                    <div class="flex-1 min-w-full md:min-w-[200px]">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-zinc-400">filter_list</span>
                            <input v-model="search" @keyup.enter="handleSearch" class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium" placeholder="Filter by course name or code..." type="text"/>
                        </div>
                    </div>
                    <select v-model="semester" @change="handleSearch" class="w-full md:w-auto bg-surface-container-low border-none rounded-xl text-sm font-bold text-on-surface px-4 py-2 focus:ring-2 focus:ring-primary/20">
                        <option value="">All Semesters</option>
                        <option value="1">First Semester</option>
                        <option value="2">Second Semester</option>
                    </select>
                    <select v-model="academicSessionId" @change="handleSearch" class="w-full md:w-auto bg-surface-container-low border-none rounded-xl text-sm font-bold text-on-surface px-4 py-2 focus:ring-2 focus:ring-primary/20">
                        <option value="">All Sessions</option>
                        <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.name }}</option>
                    </select>
                    <button @click="resetFilters" class="w-full md:w-auto px-4 py-2 text-sm font-bold text-primary hover:bg-primary/5 rounded-xl transition-all">
                        Reset Filters
                    </button>
                </div>

                <!-- Course Data Table -->
                <div class="bg-white rounded-2xl shadow-xl shadow-zinc-200/50 border border-outline-variant/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-[600px]">
                            <thead>
                                <tr class="bg-surface-container-low border-b border-outline-variant/20">
                                    <th class="px-4 md:px-6 py-3 md:py-4 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-500 whitespace-nowrap">Course Code</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-500">Title</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-500 text-center whitespace-nowrap">Credits</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-500 whitespace-nowrap">Semester</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-500 whitespace-nowrap">Session</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4 text-[9px] md:text-[10px] font-bold uppercase tracking-widest text-zinc-500 text-right whitespace-nowrap">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                                <tr v-for="course in courses" :key="course.id" class="hover:bg-primary/5 transition-colors cursor-pointer group">
                                    <td class="px-4 md:px-6 py-3 md:py-4">
                                        <span class="px-2 py-1 bg-primary/10 text-primary font-bold text-[10px] md:text-xs rounded-lg whitespace-nowrap">{{ course.code }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4">
                                        <p class="text-xs md:text-sm font-bold text-on-surface">{{ course.title }}</p>
                                        <p class="text-[10px] text-zinc-400 mt-0.5">{{ course.academic_session?.name || 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4 text-center">
                                        <span class="text-xs md:text-sm font-bold text-on-surface">{{ course.credit_hours }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4">
                                        <span class="text-[10px] md:text-xs font-bold px-2 py-1 rounded whitespace-nowrap" :class="course.semester === 1 ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'">
                                            {{ course.semester === 1 ? 'First' : 'Second' }} Sem
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4">
                                        <span class="text-[10px] md:text-xs font-bold text-zinc-600 whitespace-nowrap">{{ course.academic_session?.name || 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-3 md:py-4 text-right">
                                        <div class="flex justify-end gap-1 md:gap-2">
                                            <button @click.stop="editCourse(course)" class="p-1.5 md:p-2 hover:bg-zinc-100 rounded-lg text-zinc-400 group-hover:text-primary transition-colors" title="Edit Course">
                                                <span class="material-symbols-outlined text-base md:text-lg">edit</span>
                                            </button>
                                            <button @click.stop="viewStudents(course, course.code)" class="p-1.5 md:p-2 hover:bg-zinc-100 rounded-lg text-zinc-400 group-hover:text-zinc-600 transition-colors" title="Manage Students">
                                                <span class="material-symbols-outlined text-base md:text-lg">group</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!courses || courses.length === 0">
                                    <td colspan="6" class="px-4 md:px-6 py-12 text-center">
                                        <span class="material-symbols-outlined text-zinc-300 text-3xl md:text-4xl mb-2">inbox</span>
                                        <p class="text-zinc-500 text-xs md:text-sm">No courses found. Add your first course or import from CSV.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 md:px-6 py-3 md:py-4 bg-surface-container-low flex flex-col md:flex-row justify-between items-center gap-3 border-t border-outline-variant/20">
                        <p class="text-[10px] md:text-xs font-medium text-zinc-500">Showing {{ courses?.length || 0 }} courses</p>
                        <div class="flex gap-2">
                            <button class="p-1.5 md:p-2 rounded-lg hover:bg-zinc-200 disabled:opacity-30">
                                <span class="material-symbols-outlined text-sm">chevron_left</span>
                            </button>
                            <button class="w-7 h-7 md:w-8 md:h-8 rounded-lg bg-primary text-on-primary text-xs font-bold">1</button>
                            <button class="w-7 h-7 md:w-8 md:h-8 rounded-lg hover:bg-zinc-200 text-xs font-bold">2</button>
                            <button class="w-7 h-7 md:w-8 md:h-8 rounded-lg hover:bg-zinc-200 text-xs font-bold">3</button>
                            <button class="p-1.5 md:p-2 rounded-lg hover:bg-zinc-200">
                                <span class="material-symbols-outlined text-sm">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Course Side Drawer -->
            <div v-if="showEditDrawer" class="absolute inset-y-0 right-0 w-[450px] bg-white shadow-2xl z-50 transform transition-transform duration-300 flex flex-col border-l border-outline-variant/20">
                <div class="p-6 border-b border-outline-variant/20 flex justify-between items-center bg-white sticky top-0">
                    <div>
                        <h4 class="text-xl font-black text-on-surface tracking-tight">Course Details</h4>
                        <span class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[10px] text-primary">Editing {{ editForm.code }}</span>
                    </div>
                    <button @click="closeDrawer" class="p-2 hover:bg-zinc-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <div class="space-y-4">
                        <h5 class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[11px] text-zinc-500 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">info</span> Basic Information
                        </h5>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-on-surface-variant">Course Code</label>
                                <input v-model="editForm.code" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4" type="text"/>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-on-surface-variant">Credits</label>
                                <input v-model.number="editForm.credit_hours" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4" type="number" min="1" max="6"/>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-on-surface-variant">Course Title</label>
                            <input v-model="editForm.title" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4" type="text"/>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h5 class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[11px] text-zinc-500 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">groups</span> Enrollment Management
                        </h5>
                        <div class="bg-surface-container-low rounded-xl p-4 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-on-surface-variant">Enrolled Students</span>
                                <span class="text-sm font-black text-primary">{{ selectedCourse?.students_count || 0 }} Students</span>
                            </div>
                            <button @click="viewStudents(selectedCourse, selectedCourse?.code)" class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">group_add</span>
                                {{ selectedCourse?.students_count > 0 ? 'Add More Students' : 'Upload Student List' }}
                            </button>
                            <div v-if="selectedCourse?.students_count > 0" class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                                <span class="text-xs font-bold text-emerald-700">{{ selectedCourse.students_count }} student(s) already enrolled</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h5 class="font-['Public_Sans'] font-bold uppercase tracking-widest text-[11px] text-zinc-500 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">settings</span> Curricular Settings
                        </h5>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-on-surface-variant">Semester</label>
                                <select v-model="editForm.semester" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                                    <option :value="1">First Semester</option>
                                    <option :value="2">Second Semester</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-on-surface-variant">Academic Session</label>
                                <select v-model="editForm.academic_session_id" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                                    <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.name }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-outline-variant/20 bg-white sticky bottom-0 flex gap-3">
                    <button @click="submitEdit" class="flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Save Changes
                    </button>
                    <button @click="closeDrawer" class="px-6 py-3 bg-surface-container-high text-on-surface rounded-xl font-bold text-sm">
                        Discard
                    </button>
                </div>
            </div>
        </main>

        <!-- Import CSV Modal -->
        <div v-if="showImportModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="closeImportModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-black text-on-surface">Import Courses</h3>
                        <p class="text-xs text-slate-500 mt-1">Upload CSV/Excel file with course data</p>
                    </div>
                    <button @click="closeImportModal" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant mb-2 block">Academic Session</label>
                        <select v-model="importSessionId" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                            <option value="">Select Session</option>
                            <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant mb-2 block">Upload File</label>
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-primary transition-colors">
                            <input type="file" @change="(e) => importFile = e.target.files[0]" accept=".csv,.xlsx,.xls" class="hidden" id="fileUpload"/>
                            <label for="fileUpload" class="cursor-pointer">
                                <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">upload_file</span>
                                <p class="text-sm font-bold text-slate-600">{{ importFile ? importFile.name : 'Click to upload or drag and drop' }}</p>
                                <p class="text-xs text-slate-400 mt-1">CSV or Excel (max 10MB)</p>
                            </label>
                        </div>
                    </div>
                    <div class="bg-primary/5 rounded-xl p-4">
                        <p class="text-xs font-bold text-primary mb-2">File Format:</p>
                        <p class="text-[11px] text-slate-500">Columns: code, title, credit_hours, semester</p>
                        <p class="text-[11px] text-slate-500 mt-1">Example: COM401, Software Engineering, 3, 1</p>
                        <button @click="handleDownloadTemplate" class="mt-3 text-xs font-bold text-primary hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">download</span>
                            Download Template
                        </button>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="closeImportModal" class="flex-1 py-3 bg-surface-container-high text-on-surface rounded-xl font-bold text-sm">Cancel</button>
                    <button @click="submitImport" class="flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Import Courses
                    </button>
                </div>
            </div>
        </div>

        <!-- Create Course Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="closeCreateModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-black text-on-surface">Add New Course</h3>
                        <p class="text-xs text-slate-500 mt-1">Create a new course for the session</p>
                    </div>
                    <button @click="closeCreateModal" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-on-surface-variant mb-2 block">Course Code</label>
                            <input v-model="createForm.code" type="text" placeholder="e.g., COM401" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-on-surface-variant mb-2 block">Credit Hours</label>
                            <input v-model.number="createForm.credit_hours" type="number" min="1" max="6" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-on-surface-variant mb-2 block">Course Title</label>
                        <input v-model="createForm.title" type="text" placeholder="e.g., Software Engineering" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-on-surface-variant mb-2 block">Semester</label>
                            <select v-model="createForm.semester" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                                <option :value="1">First Semester</option>
                                <option :value="2">Second Semester</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-on-surface-variant mb-2 block">Academic Session</label>
                            <select v-model="createForm.academic_session_id" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                                <option value="">Select Session</option>
                                <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="closeCreateModal" class="flex-1 py-3 bg-surface-container-high text-on-surface rounded-xl font-bold text-sm">Cancel</button>
                    <button @click="submitCreate" class="flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Create Course
                    </button>
                </div>
            </div>
        </div>

        <!-- Manage Students Modal -->
        <div v-if="showStudentsModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="closeStudentsModal">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-black text-on-surface">Manage Students</h3>
                        <p class="text-xs text-zinc-500 mt-1">{{ selectedCourse?.code }} - Add or upload students</p>
                    </div>
                    <button @click="closeStudentsModal" class="p-2 hover:bg-zinc-100 rounded-full transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <!-- If students already uploaded -->
                <div v-if="selectedCourse?.students_count > 0" class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                        <span class="text-sm font-bold text-emerald-800">Students Already Enrolled</span>
                    </div>
                    <p class="text-xs text-emerald-700">This course already has students enrolled. Adding more will append to existing enrollments.</p>
                </div>
                
                <!-- Tabs for Upload vs Manual Entry -->
                <div class="flex gap-2 mb-6 border-b border-zinc-200">
                    <button @click="showManualEntry = false" :class="['px-4 py-2 text-sm font-bold border-b-2 transition-colors', !showManualEntry ? 'border-primary text-primary' : 'border-transparent text-zinc-500 hover:text-zinc-700']">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">upload_file</span>
                            Upload File
                        </span>
                    </button>
                    <button @click="showManualEntry = true" :class="['px-4 py-2 text-sm font-bold border-b-2 transition-colors', showManualEntry ? 'border-primary text-primary' : 'border-transparent text-zinc-500 hover:text-zinc-700']">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">person_add</span>
                            Manual Entry
                        </span>
                    </button>
                </div>
                
                <!-- Upload File Section -->
                <div v-if="!showManualEntry" class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Academic Session</label>
                        <select v-model="importSessionId" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                            <option value="">Select Session</option>
                            <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Upload Student List</label>
                        <div class="border-2 border-dashed border-zinc-300 rounded-xl p-6 text-center hover:border-primary transition-colors">
                            <input type="file" @change="(e) => studentImportFile = e.target.files[0]" accept=".csv,.xlsx,.xls" class="hidden" id="studentFileUpload"/>
                            <label for="studentFileUpload" class="cursor-pointer">
                                <span class="material-symbols-outlined text-4xl text-zinc-400 mb-2">upload_file</span>
                                <p class="text-sm font-bold text-zinc-600">{{ studentImportFile ? studentImportFile.name : 'Click to upload student list' }}</p>
                                <p class="text-xs text-zinc-400 mt-1">CSV or Excel (max 10MB)</p>
                            </label>
                        </div>
                    </div>
                    <div class="bg-primary/5 rounded-xl p-4">
                        <p class="text-xs font-bold text-primary mb-2">Required Columns:</p>
                        <p class="text-[11px] text-zinc-500">registration_number, name, department, level</p>
                        <p class="text-[11px] text-zinc-400 mt-1">Example: COM/4001/2021, Ahmad Muhammad, Computer Science, 400</p>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button @click="closeStudentsModal" class="flex-1 py-3 bg-zinc-100 text-zinc-700 rounded-xl font-bold text-sm">Cancel</button>
                        <button @click="submitStudentImport" class="flex-1 py-3 bg-primary text-zinc-900 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            Upload Students
                        </button>
                    </div>
                </div>
                
                <!-- Manual Entry Section -->
                <div v-else class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-zinc-500 mb-2 block">Academic Session</label>
                        <select v-model="importSessionId" class="w-full bg-zinc-50 border-none rounded-xl focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2.5 px-4">
                            <option value="">Select Session</option>
                            <option v-for="session in sessions" :key="session.id" :value="session.id">{{ session.name }}</option>
                        </select>
                    </div>
                    
                    <!-- Manual Entry Form -->
                    <div class="bg-zinc-50 rounded-xl p-4 space-y-3">
                        <h4 class="text-sm font-bold text-zinc-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-sm">person_add</span>
                            Add Student
                        </h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="text-[10px] font-bold text-zinc-500 mb-1 block">Registration Number *</label>
                                <input v-model="manualStudentForm.registration_number" type="text" placeholder="e.g., COM/4001/2021" class="w-full bg-white border border-zinc-200 rounded-lg focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2 px-3"/>
                            </div>
                            <div class="col-span-2">
                                <label class="text-[10px] font-bold text-zinc-500 mb-1 block">Full Name *</label>
                                <input v-model="manualStudentForm.name" type="text" placeholder="e.g., Ahmad Muhammad" class="w-full bg-white border border-zinc-200 rounded-lg focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2 px-3"/>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 mb-1 block">Department</label>
                                <input v-model="manualStudentForm.department" type="text" placeholder="e.g., Computer Science" class="w-full bg-white border border-zinc-200 rounded-lg focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2 px-3"/>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 mb-1 block">Level</label>
                                <input v-model="manualStudentForm.level" type="text" placeholder="e.g., 400" class="w-full bg-white border border-zinc-200 rounded-lg focus:ring-2 focus:ring-primary/20 text-sm font-medium py-2 px-3"/>
                            </div>
                        </div>
                        <button @click="addManualStudent" class="w-full py-2.5 bg-primary text-zinc-900 rounded-lg font-bold text-sm shadow-md hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-sm">add</span>
                            Add to List
                        </button>
                    </div>
                    
                    <!-- Added Students List -->
                    <div v-if="manualStudents.length > 0" class="border border-zinc-200 rounded-xl overflow-hidden">
                        <div class="bg-zinc-100 px-4 py-2 border-b border-zinc-200">
                            <p class="text-xs font-bold text-zinc-600">Students to Add ({{ manualStudents.length }})</p>
                        </div>
                        <div class="max-h-48 overflow-y-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-zinc-50">
                                    <tr>
                                        <th class="px-4 py-2 text-[10px] font-bold text-zinc-500 uppercase">Reg. Number</th>
                                        <th class="px-4 py-2 text-[10px] font-bold text-zinc-500 uppercase">Name</th>
                                        <th class="px-4 py-2 text-[10px] font-bold text-zinc-500 uppercase">Dept</th>
                                        <th class="px-4 py-2 text-[10px] font-bold text-zinc-500 uppercase text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100">
                                    <tr v-for="(student, index) in manualStudents" :key="index" class="hover:bg-zinc-50">
                                        <td class="px-4 py-2 text-xs font-medium">{{ student.registration_number }}</td>
                                        <td class="px-4 py-2 text-xs">{{ student.name }}</td>
                                        <td class="px-4 py-2 text-xs text-zinc-500">{{ student.department || '-' }}</td>
                                        <td class="px-4 py-2 text-xs text-right">
                                            <button @click="removeManualStudent(index)" class="text-red-500 hover:text-red-700">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button @click="closeStudentsModal" class="flex-1 py-3 bg-zinc-100 text-zinc-700 rounded-xl font-bold text-sm">Cancel</button>
                        <button @click="submitManualStudents" :disabled="manualStudents.length === 0" class="flex-1 py-3 bg-primary text-zinc-900 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            Save {{ manualStudents.length > 0 ? `(${manualStudents.length})` : '' }} Students
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- BottomNavBar (Mobile Only) -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-md flex justify-around items-center py-3 px-6 shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.1)] z-50">
            <Link class="flex flex-col items-center gap-1 text-zinc-400" href="/exam-officer">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Dash</span>
            </Link>
            <Link class="flex flex-col items-center gap-1 text-primary" href="/courses">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">school</span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Courses</span>
            </Link>
            <Link class="flex flex-col items-center gap-1 text-zinc-400" href="/students">
                <span class="material-symbols-outlined">group</span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Users</span>
            </Link>
            <Link class="flex flex-col items-center gap-1 text-zinc-400" href="/exams">
                <span class="material-symbols-outlined">event</span>
                <span class="text-[10px] font-bold uppercase tracking-widest">Exams</span>
            </Link>
        </nav>
    </body>
</template>
