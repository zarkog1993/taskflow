<template>
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Brze akcije</h3>

        <div class="space-y-3">
            <router-link
                to="/teams"
                class="group flex items-center justify-between rounded-xl border border-slate-200/80 bg-white p-3.5 text-xs font-semibold text-slate-900 transition hover:border-indigo-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950/40 dark:text-white dark:hover:border-indigo-800 dark:hover:bg-slate-950"
            >
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 rounded-lg">🛡️</span>
                    <span>Upravljaj Ekipama i Sastavom</span>
                </div>
                <span class="text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white">→</span>
            </router-link>

            <router-link
                to="/players"
                class="group flex items-center justify-between rounded-xl border border-slate-200/80 bg-white p-3.5 text-xs font-semibold text-slate-900 transition hover:border-indigo-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950/40 dark:text-white dark:hover:border-indigo-800 dark:hover:bg-slate-950"
            >
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-emerald-600/20 text-emerald-700 dark:text-emerald-400 rounded-lg">🏃</span>
                    <span>Registar Igrača Akademije</span>
                </div>
                <span class="text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white">→</span>
            </router-link>

            <router-link
                to="/trainings"
                class="group flex items-center justify-between rounded-xl border border-slate-200/80 bg-white p-3.5 text-xs font-semibold text-slate-900 transition hover:border-indigo-300 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950/40 dark:text-white dark:hover:border-indigo-800 dark:hover:bg-slate-950"
            >
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-yellow-600/20 text-yellow-700 dark:text-yellow-400 rounded-lg">📅</span>
                    <span>Mesečni Kalendar Treninga</span>
                </div>
                <span class="text-gray-500 group-hover:text-gray-900 dark:group-hover:text-white">→</span>
            </router-link>
        </div>
    </section>
</template>
