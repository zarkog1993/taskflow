<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <router-link to="/teams" class="flex items-center justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:border-indigo-300 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 dark:border-slate-800 dark:bg-slate-900">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Ukupno Ekipe</div>
                <div class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ teamsCount }}</div>
                <div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-1">Aktivne selekcije</div>
            </div>
            <div class="p-3 bg-indigo-600/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-2xl text-xl">
                🛡️
            </div>
        </router-link>

        <router-link to="/players" class="flex items-center justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 dark:border-slate-800 dark:bg-slate-900">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Registrovani Igrači</div>
                <div class="text-3xl font-black text-emerald-700 dark:text-emerald-400 mt-1">{{ playersCount }}</div>
                <div class="text-[11px] text-emerald-700 dark:text-emerald-500 mt-1">Članovi akademije</div>
            </div>
            <div class="p-3 bg-emerald-600/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xl">
                🏃‍♂️
            </div>
        </router-link>

        <router-link to="/trainings" class="flex items-center justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:border-amber-300 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 dark:border-slate-800 dark:bg-slate-900">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Predstojeći Treninzi</div>
                <div class="text-3xl font-black text-yellow-700 dark:text-yellow-400 mt-1">{{ upcomingSessionsCount }}</div>
                <div class="text-[11px] text-yellow-700 dark:text-yellow-500 mt-1">Zakazani događaji</div>
            </div>
            <div class="p-3 bg-yellow-600/10 border border-yellow-500/20 text-yellow-700 dark:text-yellow-400 rounded-2xl text-xl">
                📅
            </div>
        </router-link>

        <router-link to="/matches" class="flex items-center justify-between rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:border-violet-300 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 dark:border-slate-800 dark:bg-slate-900">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">
                    {{ featuredMatch ? (isUpcomingMatch ? 'Predstojeća utakmica' : 'Poslednja utakmica') : 'Utakmice' }}
                </div>
                <div class="mt-1 truncate text-xl font-black text-violet-700 dark:text-violet-400">
                    {{ matchesLoading ? 'Učitavanje...' : featuredMatch?.opponent || (matchesLoadFailed ? 'Podaci nisu učitani' : canAccessMatches ? 'Nema utakmica' : 'Modul nije aktivan') }}
                </div>
                <div class="mt-1 truncate text-[11px] text-violet-700 dark:text-violet-400">
                    <template v-if="featuredMatch">
                        {{ isUpcomingMatch ? formatDate(featuredMatch.scheduled_at) : `${featuredMatch.home_score ?? 0} : ${featuredMatch.away_score ?? 0} · ${formatDate(featuredMatch.scheduled_at)}` }}
                    </template>
                    <template v-else-if="matchesLoadFailed">Proveri vezu i pokušaj ponovo</template>
                    <template v-else-if="!canAccessMatches">Paket ne uključuje utakmice</template>
                    <template v-else>Nema zakazanih ni odigranih utakmica</template>
                </div>
            </div>
            <div class="p-3 bg-purple-600/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 rounded-2xl text-xl">
                ⚽
            </div>
        </router-link>
    </div>
</template>

<script setup>
defineProps({
    teamsCount: {
        type: Number,
        default: 0
    },
    playersCount: {
        type: Number,
        default: 0
    },
    upcomingSessionsCount: {
        type: Number,
        default: 0
    },
    featuredMatch: { type: Object, default: null },
    isUpcomingMatch: { type: Boolean, default: false },
    canAccessMatches: { type: Boolean, default: false },
    matchesLoading: { type: Boolean, default: true },
    matchesLoadFailed: { type: Boolean, default: false }
})

const formatDate = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleString('sr-Latn-RS', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>
