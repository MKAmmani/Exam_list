<script setup>
import { Link, Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import html2pdf from 'html2pdf.js';

const props = defineProps({
    exam: Object,
    venue: Object,
    allocations: Array,
    date: String,
});

const printContainer = ref(null);

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
};

const formatTime = (timeString) => {
    if (!timeString) return '';
    return timeString;
};

const getInitials = (name) => {
    if (!name) return '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const downloadPDF = () => {
    const element = printContainer.value;
    const courseCode = props.exam?.course?.code || 'COURSE';
    const venueName = props.venue?.name?.replace(/[^a-zA-Z0-9]/g, '_') || 'VENUE';
    const filename = `Attendance_${courseCode}_${venueName}_${new Date().toISOString().split('T')[0]}.pdf`;
    
    const opt = {
        margin: 0.5,
        filename: filename,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            letterRendering: true,
        },
        jsPDF: { 
            unit: 'in', 
            format: 'a4', 
            orientation: 'portrait',
            compress: true,
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };
    
    // Temporarily hide the no-print elements
    const noPrintElements = document.querySelectorAll('.no-print');
    noPrintElements.forEach(el => el.style.display = 'none');
    
    html2pdf().set(opt).from(element).save().then(() => {
        // Restore no-print elements
        noPrintElements.forEach(el => el.style.display = '');
    });
};
</script>

<template>
    <Head :title="`Attendance Sheet - ${exam?.course?.code || 'Course'}`" />
    <body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen">
        <!-- Top Navigation / Toolbar (Hidden on Print) -->
        <div class="no-print w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex justify-between items-center sticky top-0 z-50">
            <div class="flex items-center gap-3">
                <Link href="/exams" class="flex items-center gap-2 text-primary hover:text-primary/80">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="font-semibold">Back to Exams</span>
                </Link>
            </div>
            <div class="flex gap-3">
                <button @click="downloadPDF" class="flex items-center gap-2 bg-primary hover:bg-primary/90 px-4 py-2 rounded-lg font-semibold transition-colors text-slate-900">
                    <span class="material-symbols-outlined">download</span>
                    Download Sheet
                </button>
            </div>
        </div>

        <!-- Printable Area -->
        <div ref="printContainer" class="print-container max-w-[1000px] mx-auto my-8 bg-white dark:bg-slate-950 p-10 shadow-xl border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
            <!-- Header Section -->
            <header class="border-b-4 border-primary pb-6 mb-8">
                <div class="flex justify-between items-start">
                    <div class="flex gap-6 items-center">
                        <div class="w-24 h-24 bg-primary/10 flex items-center justify-center rounded-full border-2 border-primary">
                            <span class="material-symbols-outlined text-primary text-5xl">account_balance</span>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-primary uppercase leading-none">Bayero University Kano</h2>
                            <h3 class="text-xl font-semibold text-slate-700 dark:text-slate-300 mt-1 uppercase">Faculty of Computing</h3>
                            <div class="mt-4 flex flex-col gap-1 text-slate-600 dark:text-slate-400">
                                <p class="flex items-center gap-2 font-medium">
                                    <span class="material-symbols-outlined text-secondary text-sm">label</span>
                                    Course: <span class="text-slate-900 dark:text-slate-100 uppercase">{{ exam?.course?.code || 'CSC3301' }} - {{ exam?.course?.title || 'Course Title' }}</span>
                                </p>
                                <p class="flex items-center gap-2 font-medium">
                                    <span class="material-symbols-outlined text-secondary text-sm">location_on</span>
                                    Venue: <span class="text-slate-900 dark:text-slate-100 uppercase">{{ venue?.name || 'Venue Name' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <div class="bg-primary px-4 py-2 rounded-lg font-bold text-sm mb-4 uppercase tracking-widest text-slate-900">
                            Attendance Sheet
                        </div>
                        <p class="flex items-center gap-2 font-medium text-slate-600 dark:text-slate-400">
                            <span class="material-symbols-outlined text-secondary">calendar_today</span>
                            Date: <span class="text-slate-900 dark:text-slate-100 font-bold">{{ formatDate(exam?.date) }}</span>
                        </p>
                        <p class="flex items-center gap-2 font-medium text-slate-600 dark:text-slate-400">
                            <span class="material-symbols-outlined text-secondary">schedule</span>
                            Time: <span class="text-slate-900 dark:text-slate-100 font-bold">{{ formatTime(exam?.start_time) }} - {{ formatTime(exam?.end_time) }}</span>
                        </p>
                    </div>
                </div>
            </header>

            <!-- Attendance Table -->
            <div class="w-full border border-slate-300 dark:border-slate-700 rounded-lg overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-primary text-slate-900">
                            <th class="px-3 py-4 text-xs font-bold uppercase tracking-wider border-r border-white/20 w-12 text-center">S/N</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider border-r border-white/20">Student Name</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider border-r border-white/20 w-44">Reg. Number</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider border-r border-white/20 w-32">Seat No.</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider border-r border-white/20 w-36">Sign-In</th>
                            <th class="px-4 py-4 text-xs font-bold uppercase tracking-wider w-36">Sign-Out</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-300 dark:divide-slate-700">
                        <tr v-for="(allocation, index) in allocations" :key="allocation.id" :class="['table-row hover:bg-slate-50 dark:hover:bg-slate-900/50', index % 2 === 1 ? 'bg-slate-50/30 dark:bg-slate-900/20' : '']">
                            <td class="sig-cell px-3 text-center font-medium border-r border-slate-300 dark:border-slate-700">{{ index + 1 }}</td>
                            <td class="sig-cell px-4 border-r border-slate-300 dark:border-slate-700 font-semibold uppercase">{{ allocation.student?.name || 'Student Name' }}</td>
                            <td class="sig-cell px-4 border-r border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-400">{{ allocation.student?.registration_number || 'REG/NUMBER' }}</td>
                            <td class="sig-cell px-4 border-r border-slate-300 dark:border-slate-700 font-bold text-primary">{{ allocation.seat_number }}</td>
                            <td class="sig-cell px-4 border-r border-slate-300 dark:border-slate-700"></td>
                            <td class="sig-cell px-4"></td>
                        </tr>
                        <tr v-if="!allocations || allocations.length === 0">
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl mb-2">event_busy</span>
                                <p>No students allocated to this venue</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Section (Signatures) -->
            <footer class="mt-12 grid grid-cols-2 gap-12 pt-8 border-t-2 border-slate-100 dark:border-slate-800">
                <div class="space-y-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold uppercase text-slate-500 tracking-wider">Invigilator's Name</label>
                        <div class="border-b-2 border-slate-300 dark:border-slate-700 h-10 w-full"></div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold uppercase text-slate-500 tracking-wider">Invigilator's Signature</label>
                        <div class="border-b-2 border-slate-300 dark:border-slate-700 h-10 w-full flex items-end">
                            <span class="text-xs text-slate-300 mb-1 italic">Date: ____/____/2023</span>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold uppercase text-slate-500 tracking-wider">Departmental Exam Officer (DEO)</label>
                        <div class="border-b-2 border-slate-300 dark:border-slate-700 h-10 w-full"></div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-bold uppercase text-slate-500 tracking-wider">DEO Signature & Stamp</label>
                        <div class="border-b-2 border-slate-300 dark:border-slate-700 h-10 w-full"></div>
                    </div>
                </div>
            </footer>

            <!-- Printing Info -->
            <div class="mt-8 text-[10px] text-slate-400 dark:text-slate-600 flex justify-between uppercase font-medium tracking-tight border-t border-slate-50 pt-4">
                <p>Generated by BUK Faculty Exam System - Page 1 of 1</p>
                <p>Reference: ATT-{{ exam?.course?.code || 'COURSE' }}-{{ new Date().getFullYear() }}-{{ venue?.id || '01' }}</p>
                <p>Print Time: {{ new Date().toLocaleString() }}</p>
            </div>
        </div>
    </body>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
    .print-container {
        box-shadow: none !important;
        border: none !important;
        margin: 0 !important;
        padding: 20px !important;
    }
    body {
        background: white !important;
    }
}
</style>
