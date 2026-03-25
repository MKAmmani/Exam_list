<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome" />
    <body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display">
<div class="relative flex h-auto min-h-screen w-full flex-col overflow-x-hidden">
<div class="layout-container flex h-full grow flex-col">
<!-- Top Navigation Bar -->
<header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-primary/10 px-6 py-4 lg:px-20 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md sticky top-0 z-50">
<div class="flex items-center gap-3 text-slate-900 dark:text-slate-100">
<div class="text-primary">
<span class="material-symbols-outlined text-4xl">school</span>
</div>
<div class="flex flex-col">
<h2 class="text-lg font-bold leading-none tracking-tight">BUK Computing</h2>
<span class="text-[10px] uppercase tracking-widest font-semibold opacity-70">Exam Management System</span>
</div>
</div>
<div class="hidden md:flex flex-1 justify-end gap-8 items-center">
<nav class="flex items-center gap-8">
<Link href="/" class="text-sm font-medium hover:text-primary transition-colors">Home</Link>
<Link href="#features" class="text-sm font-medium hover:text-primary transition-colors">Features</Link>
<Link href="#" class="text-sm font-medium hover:text-primary transition-colors">Schedules</Link>
<Link href="#" class="text-sm font-medium hover:text-primary transition-colors">Support</Link>
</nav>
<div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>
<Link href="/login" class="flex min-w-[100px] cursor-pointer items-center justify-center rounded-lg h-10 px-5 bg-primary text-background-dark text-sm font-bold transition-transform active:scale-95 shadow-lg shadow-primary/20">
                        Login
                    </Link>
</div>
<button class="md:hidden text-slate-900 dark:text-slate-100">
<span class="material-symbols-outlined">menu</span>
</button>
</header>
<main class="flex-1">
<!-- Hero Section -->
<section class="px-6 lg:px-20 py-12 lg:py-24">
<div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-12 items-center">
<div class="flex flex-col gap-8 flex-1">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">
<span class="relative flex h-2 w-2">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
<span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
</span>
                                Official Faculty Portal
                            </div>
<div class="flex flex-col gap-4">
<h1 class="text-slate-900 dark:text-white text-5xl lg:text-7xl font-black leading-[1.1] tracking-tight">
                                    Seamless Exam <span class="text-primary">Management</span> &amp; Allocation
                                </h1>
<p class="text-slate-600 dark:text-slate-400 text-lg lg:text-xl leading-relaxed max-w-xl">
                                    The official digital infrastructure for Bayero University Kano, Faculty of Computing. Streamlining seating arrangements, venue lookups, and administrative workflows for a stress-free examination period.
                                </p>
</div>
<div class="flex flex-wrap gap-4">
<Link href="/students/search" class="flex min-w-[180px] items-center justify-center rounded-xl h-14 px-8 bg-primary text-background-dark text-base font-bold shadow-xl shadow-primary/20 hover:brightness-110 transition-all">
                                    Student Portal
                                </Link>
<Link href="/login" class="flex min-w-[180px] items-center justify-center rounded-xl h-14 px-8 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white text-base font-bold border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                    Exam Officer Portal
                                </Link>
</div>
<div class="flex items-center gap-6 pt-4">
<div class="flex -space-x-3">
<div class="h-10 w-10 rounded-full border-2 border-white dark:border-background-dark bg-slate-200 overflow-hidden" data-alt="Student profile portrait 1">
<img alt="Avatar" class="h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAmnqb-mjXi0fcZHciWRnNfHkNgJpmhw5DT6F2gw9omjWcb6os_uuJ9LRtcH7J79URlPQH39NvP4PBOqsATKbyhXVmq0CdVkHcLsBwfmCEbqDkxwUbaiMgqHYAAQF3b0VkVu9kQn-j8H_6BSI0z3sTlbsLtCarL4YhnesptBCeqQqKtUvKRob__J_pWKZcGcSsubZglvBhXO1yueQAqmCx2TFTmyFEFzgEem5jju1vR6xMkDY7YCZ6syxv3a0WtW8Oh1DeOj9w5P4Y"/>
</div>
<div class="h-10 w-10 rounded-full border-2 border-white dark:border-background-dark bg-slate-200 overflow-hidden" data-alt="Student profile portrait 2">
<img alt="Avatar" class="h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC-n4-n2AOBC3X9Isr5bTiJNTpTuN30fyGGc6Xb_CFwI_j2P5Or7fiu2muriiFv70o9-dY0qRPxbbzx6k4jGGXn_vXMFfpc5rgQQ0q3EbxPY5kXPNMJKOEaMqw8oVnu7SJKybfdxkWvtNANtMYRqscWD_JUR0XbmlGo9oPHGYr0zObuljD-PYaBwINs-C5N1pWLvh8D9LIaK4T-D4b-3Ev9bICL_-WIytO15KpswoUZPMsj0Zu3_UpR02mipFDYsk-klz4_MwIcybM"/>
</div>
<div class="h-10 w-10 rounded-full border-2 border-white dark:border-background-dark bg-slate-200 overflow-hidden" data-alt="Student profile portrait 3">
<img alt="Avatar" class="h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAd0feaKLdgveZMD8RkW6n63Fpi9Rx78N54r6MAeUIa9B87perkpAkaOYNPeLKPfJHKVVV_KvabBI01Dk22CFbh-XLhtW26B7t1xQ0IORQ0ZVV5A2LZXMeIor7W4vwciU8iGS-9p9bzz0At135RuIJb8e3NbZrA04q8lfDkowiZfABQAQpb7OUnBnU1mkzxnF_wmepw8sjHQdKpTTsgpNnNj2Ko4pS7Y2HWNFhGaghdTnEXPekLQ0H01UJcgYPvnEoUYO3N0Gyt9NI"/>
</div>
</div>
<p class="text-sm font-medium text-slate-500">Trusted by <span class="text-slate-900 dark:text-white font-bold">5,000+</span> students</p>
</div>
</div>
<div class="flex-1 w-full lg:w-auto relative">
<div class="absolute -top-10 -left-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl"></div>
<div class="absolute -bottom-10 -right-10 w-60 h-60 bg-primary/10 rounded-full blur-3xl"></div>
<div class="relative rounded-2xl overflow-hidden shadow-2xl border border-primary/20 bg-slate-200 aspect-[4/3]" data-alt="Modern university computing lab interior">
<img alt="University Campus" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGA8LuuHar4qQctYOSndKq08JdPUfBpwbBs26EDzdIOTDaRA_D9WZajhwv_WLqeFHUFetDQgWFHANvq_Qcn6MU3q7iUml4CxhNvr8w6KbQx5hmF3Y3tjxo_M4sppnCcwo8OmvfVbqGg7TOKWjeuJibNJF4eOc7gHoySOKm3tI2AE5wmgTDSt3vLKi5W4E4hmZsqDTlhSUQia0m0mE9jzvPVpLINmp8uj80iJUIZ9wgKr6gYlBxWD8OaCvwwcH1ryxIfXkMDt0GZjE"/>
<div class="absolute inset-0 bg-gradient-to-t from-background-dark/60 to-transparent flex items-end p-8">
<div class="bg-white/10 backdrop-blur-md rounded-lg p-4 border border-white/20">
<div class="flex items-center gap-3">
<div class="bg-primary rounded-full p-2">
<span class="material-symbols-outlined text-background-dark">check_circle</span>
</div>
<div class="text-white">
<p class="text-xs opacity-80">Next Exam Session</p>
<p class="font-bold">Monday, 8:30 AM</p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Features Section -->
<section class="px-6 lg:px-20 py-20 bg-slate-50 dark:bg-background-dark/50" id="features">
<div class="max-w-7xl mx-auto">
<div class="flex flex-col items-center text-center gap-4 mb-16">
<h2 class="text-primary font-bold tracking-widest uppercase text-sm">Capabilities</h2>
<h3 class="text-3xl lg:text-5xl font-black text-slate-900 dark:text-white">Built for Academic Excellence</h3>
<p class="text-slate-600 dark:text-slate-400 max-w-2xl">Our system leverages advanced automation to ensure every student and officer has the right information at the right time.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<!-- Feature 1 -->
<div class="group p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all hover:shadow-xl hover:shadow-primary/5">
<div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-3xl">search</span>
</div>
<h4 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Instant Seat Lookup</h4>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed">Students can instantly find their specific seat number and examination venue using their matriculation number.</p>
</div>
<!-- Feature 2 -->
<div class="group p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all hover:shadow-xl hover:shadow-primary/5">
<div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-3xl">hub</span>
</div>
<h4 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Automated Allocation</h4>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed">Complex algorithms distribute students across available venues based on capacity and course proximity, ensuring maximum efficiency.</p>
</div>
<!-- Feature 3 -->
<div class="group p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-primary/50 transition-all hover:shadow-xl hover:shadow-primary/5">
<div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-3xl">fact_check</span>
</div>
<h4 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Digital Attendance</h4>
<p class="text-slate-600 dark:text-slate-400 leading-relaxed">Replace manual paper sheets with real-time digital tracking. Officers can verify and sign off attendance with a single click.</p>
</div>
</div>
</div>
</section>
<!-- Statistics Banner -->
<section class="px-6 lg:px-20 py-16 bg-primary text-background-dark">
<div class="max-w-7xl mx-auto">
<div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
<div class="flex flex-col gap-1">
<span class="text-4xl lg:text-5xl font-black">15k+</span>
<span class="text-sm font-bold uppercase tracking-wider opacity-80">Students Active</span>
</div>
<div class="flex flex-col gap-1">
<span class="text-4xl lg:text-5xl font-black">45+</span>
<span class="text-sm font-bold uppercase tracking-wider opacity-80">Exam Venues</span>
</div>
<div class="flex flex-col gap-1">
<span class="text-4xl lg:text-5xl font-black">120+</span>
<span class="text-sm font-bold uppercase tracking-wider opacity-80">Exams Daily</span>
</div>
<div class="flex flex-col gap-1">
<span class="text-4xl lg:text-5xl font-black">100%</span>
<span class="text-sm font-bold uppercase tracking-wider opacity-80">Paperless</span>
</div>
</div>
</div>
</section>
<!-- Information Section -->
<section class="px-6 lg:px-20 py-20">
<div class="max-w-7xl mx-auto flex flex-col lg:flex-row-reverse gap-16 items-center">
<div class="flex-1 space-y-6">
<h3 class="text-3xl lg:text-4xl font-black text-slate-900 dark:text-white">Empowering Faculty Staff</h3>
<p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed">
                                Our administrative dashboard provides exam officers with a comprehensive view of the entire examination lifecycle. From venue setup to result submission tracking, everything is centralized.
                            </p>
<ul class="space-y-4">
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-primary mt-1">check_circle</span>
<div>
<h5 class="font-bold">Real-time Venue Monitoring</h5>
<p class="text-sm text-slate-500">Track student arrival and seating status across all centers simultaneously.</p>
</div>
</li>
<li class="flex items-start gap-3">
<span class="material-symbols-outlined text-primary mt-1">check_circle</span>
<div>
<h5 class="font-bold">Incident Reporting</h5>
<p class="text-sm text-slate-500">Quickly log and manage examination malpractice or technical issues.</p>
</div>
</li>
</ul>
<button class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold px-8 py-4 rounded-xl hover:opacity-90 transition-opacity">
                                View Admin Guide
                            </button>
</div>
<div class="flex-1 w-full bg-slate-100 dark:bg-slate-800 rounded-3xl p-4 lg:p-8 border border-slate-200 dark:border-slate-700">
<div class="bg-white dark:bg-slate-900 rounded-xl shadow-lg overflow-hidden border border-slate-200 dark:border-slate-700 aspect-video relative" data-alt="Screenshot of administrative exam dashboard">
<img alt="Dashboard" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC5mcM9X-Ln2qtrvpkDIMN4CE5f7qyBJolMqS9XUXGUbhskydL3cb_KhBnwNrf-rA4TpfVIifvgR3h8a9gFBzR1wGSpYL-18vEVE7lBezLZgDbnUnc9EQyTSgQ5GVavEVe6AnFDtVeze91Erne1zi-HGTsJNVTB4ZGDFy7-Wl9OdpZ7Xz1_DY38-BpJVDgWTwWUViRhgkAz7Tb7jJWC-PtmRRzNn55q1iSlzK0gNDT-isecikCZWnGNcSk-MrIdHoJ_7QC_FxBsX8E"/>
<div class="absolute inset-0 bg-primary/10"></div>
</div>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-slate-900 text-slate-300 px-6 lg:px-20 py-16">
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
<div class="col-span-1 md:col-span-1 space-y-6">
<div class="flex items-center gap-3 text-white">
<span class="material-symbols-outlined text-primary text-3xl">school</span>
<h2 class="text-xl font-bold tracking-tight">BUK Faculty of Computing</h2>
</div>
<p class="text-sm leading-relaxed opacity-70">
                            Bayero University Kano is committed to excellence in education and research. This management system is part of our digital transformation journey.
                        </p>
<div class="flex gap-4">
<a class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary/20 hover:text-primary transition-all" href="#">
<span class="material-symbols-outlined text-xl">language</span>
</a>
<a class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary/20 hover:text-primary transition-all" href="#">
<span class="material-symbols-outlined text-xl">mail</span>
</a>
<a class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary/20 hover:text-primary transition-all" href="#">
<span class="material-symbols-outlined text-xl">call</span>
</a>
</div>
</div>
<div>
<h4 class="text-white font-bold mb-6">Quick Links</h4>
<ul class="space-y-4 text-sm">
<li><a class="hover:text-primary transition-colors" href="#">University Home</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Student Portal</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Faculty Directory</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Academic Calendar</a></li>
</ul>
</div>
<div>
<h4 class="text-white font-bold mb-6">Support</h4>
<ul class="space-y-4 text-sm">
<li><a class="hover:text-primary transition-colors" href="#">Help Center</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Technical Support</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Security Information</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Privacy Policy</a></li>
</ul>
</div>
<div>
<h4 class="text-white font-bold mb-6">Contact Us</h4>
<ul class="space-y-4 text-sm">
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary text-sm">location_on</span>
<span>Gwarzo Road, Kano State, Nigeria</span>
</li>
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary text-sm">mail</span>
<span>exams.fcs@buk.edu.ng</span>
</li>
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary text-sm">schedule</span>
<span>Mon - Fri: 8:00 AM - 4:00 PM</span>
</li>
</ul>
</div>
</div>
<div class="max-w-7xl mx-auto pt-16 mt-16 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-xs opacity-50">
<p>© 2024 Bayero University Kano - Faculty of Computing. All rights reserved.</p>
<div class="flex gap-8">
<span>Designed for Excellence</span>
<span>Terms of Service</span>
</div>
</div>
</footer>
</div>
</div>
</body>
</template>
