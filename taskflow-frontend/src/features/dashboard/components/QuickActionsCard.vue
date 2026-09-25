<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Brze Akcije</h3>

        <div class="space-y-3">
            <router-link
                to="/teams"
                class="flex items-center justify-between p-3.5 bg-gray-900/80 hover:bg-gray-900 border border-gray-700/50 hover:border-indigo-500/40 rounded-xl transition text-xs font-semibold text-white group"
            >
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-indigo-600/20 text-indigo-400 rounded-lg">🛡️</span>
                    <span>Upravljaj Ekipama i Sastavom</span>
                </div>
                <span class="text-gray-500 group-hover:text-white">→</span>
            </router-link>

            <router-link
                to="/players"
                class="flex items-center justify-between p-3.5 bg-gray-900/80 hover:bg-gray-900 border border-gray-700/50 hover:border-indigo-500/40 rounded-xl transition text-xs font-semibold text-white group"
            >
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-emerald-600/20 text-emerald-400 rounded-lg">🏃</span>
                    <span>Registar Igrača Akademije</span>
                </div>
                <span class="text-gray-500 group-hover:text-white">→</span>
            </router-link>

            <router-link
                to="/trainings"
                class="flex items-center justify-between p-3.5 bg-gray-900/80 hover:bg-gray-900 border border-gray-700/50 hover:border-indigo-500/40 rounded-xl transition text-xs font-semibold text-white group"
            >
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-yellow-600/20 text-yellow-400 rounded-lg">📅</span>
                    <span>Mesečni Kalendar Treninga</span>
                </div>
                <span class="text-gray-500 group-hover:text-white">→</span>
            </router-link>
        </div>
    </div>
</template>
