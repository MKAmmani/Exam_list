<template >
<body
        class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex flex-col">
        <!-- Navigation Header -->
        <header
            class="w-full border-b border-primary/10 bg-white/50 dark:bg-background-dark/50 backdrop-blur-md sticky top-0 z-50">
            <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-2xl">school</span>
                    </div>
                    <div>
                        <h1 class="text-sm font-bold leading-none tracking-tight">Bayero University Kano</h1>
                        <p class="text-[10px] uppercase tracking-wider text-primary font-bold">Faculty of Computing</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="p-2 text-slate-500 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">help_outline</span>
                    </button>
                    <div
                        class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 overflow-hidden border border-primary/20">
                        <img class="w-full h-full object-cover"
                            data-alt="University campus academic building silhouette"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCsKQxmCVVsCF0lerDk_fo2NvqKsly8HqfGCqsyuAHLvKikWeH9Ia7xSqT0OFivbIbVE5lD3v1FyDqenqDLdUMncMG7a3bJQJvfURes6R8U4xsQjgW1hDzVnafXjabprFxEdXWWJ6QPN6qBQWYjEqiQOQOJwly6wbSa7hPqnl5k666KZbXwGApNfZAOSuSjiKum-75hJeo3c9o36Rbs6_Upc8W9GlPY8ZpVcdo7wO63WthtGOtQMbgvC3HmXVqbuCL1-3fZSINFkaY" />
                    </div>
                </div>
            </div>
        </header>
        <!-- Main Content Container -->
        <main class="flex-1 flex flex-col items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md space-y-8">
                <!-- Hero Section -->
                <div class="text-center space-y-4">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-primary/10 mb-2">
                        <span class="material-symbols-outlined text-4xl text-primary">event_seat</span>
                    </div>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                        Student Exam Seat <br />&amp; Venue Finder
                    </h2>
                    <p class="text-slate-600 dark:text-slate-400 text-base leading-relaxed">
                        Access your exam allocation details instantly. Please enter your full registration number to
                        proceed.
                    </p>
                </div>
                <!-- Search Form Card -->
                <div
                    class="bg-white dark:bg-slate-900/50 p-6 sm:p-8 rounded-2xl shadow-xl shadow-primary/5 border border-primary/5">
                    <form class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1"
                                for="reg-number">
                                Registration Number
                            </label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-xl">badge</span>
                                </div>
                                <input
                                    class="block w-full pl-11 pr-4 py-4 bg-background-light dark:bg-slate-800 border-2 border-transparent focus:border-primary focus:ring-0 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 transition-all font-medium"
                                    id="reg-number" name="reg-number" placeholder="e.g. CST/19/COM/0000" type="text" />
                            </div>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 ml-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">info</span>
                                Ensure the format is correct for accurate results
                            </p>
                        </div>
                        <button
                            class="w-full bg-primary hover:bg-primary/90 text-background-dark font-black py-4 rounded-xl shadow-lg shadow-primary/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-lg"
                            type="submit">
                            <span class="material-symbols-outlined">search</span>
                            Search Allocation
                        </button>
                    </form>
                </div>
                <!-- Quick Links / Notices -->
                <div class="grid grid-cols-2 gap-4">
                    <a class="flex flex-col items-center justify-center p-4 rounded-xl bg-white dark:bg-slate-900/50 border border-primary/5 hover:border-primary/20 transition-all group"
                        href="#">
                        <span
                            class="material-symbols-outlined text-primary mb-1 group-hover:scale-110 transition-transform">calendar_month</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Timetable</span>
                    </a>
                    <a class="flex flex-col items-center justify-center p-4 rounded-xl bg-white dark:bg-slate-900/50 border border-primary/5 hover:border-primary/20 transition-all group"
                        href="#">
                        <span
                            class="material-symbols-outlined text-primary mb-1 group-hover:scale-110 transition-transform">map</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Campus Map</span>
                    </a>
                </div>
            </div>
        </main>
        <!-- Footer -->
        <footer class="w-full py-8 px-4 mt-auto">
            <div class="max-w-4xl mx-auto flex flex-col items-center gap-4 text-center">
                <div class="flex flex-wrap justify-center gap-x-8 gap-y-2">
                    <a class="text-sm font-medium text-slate-500 hover:text-primary transition-colors" href="#">Student
                        Portal</a>
                    <a class="text-sm font-medium text-slate-500 hover:text-primary transition-colors" href="#">Exam
                        Regulations</a>
                    <a class="text-sm font-medium text-slate-500 hover:text-primary transition-colors" href="#">IT
                        Support</a>
                </div>
                <div class="w-12 h-px bg-primary/20"></div>
                <p class="text-xs text-slate-400 font-medium">
                    © 2024 Bayero University Kano. Faculty of Computing Exam System.
                </p>
            </div>
        </footer>
    </body>

</template>