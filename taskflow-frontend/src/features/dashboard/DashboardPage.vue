<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
        <!-- Dobrodošlica i Zaglavlje -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
            <div>
                <h2 class="wrap-break-word text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Dobrodošli nazad, {{ authStore.user?.name || 'Trener' }} 👋</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Pregled stanja u akademiji, predstojećih utakmica i statistike tima</p>
            </div>

            <div class="flex items-center space-x-3">
                <router-link
                    to="/trainings"
                    class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider px-4 py-3 rounded-xl shadow-lg transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Zakaži Događaj
                </router-link>
            </div>
        </div>

        <!-- KPI Statistika -->
        <DashboardKpiCards
            :teams-count="teamStore.teams.length"
            :players-count="players.length"
            :upcoming-sessions-count="upcomingSessionsCount"
            :featured-match="featuredMatch"
            :is-upcoming-match="isUpcomingMatch"
            :can-access-matches="canAccessMatches"
            :matches-loading="matchesLoading"
            :matches-load-failed="matchesLoadFailed"
        />

        <!-- Glavni Sadržaj -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Leva Kolona: Današnji Fokalni Događaji & Najbolji Igrači -->
            <div class="lg:col-span-2 space-y-8">
                <NextActivityCard :session="nextSession" :format-date="formatDate" />
                <TopPlayersTable :players="topPlayers" />
            </div>

            <!-- Desna Kolona: Brze Akcije & Pregled Ekipa -->
            <div class="space-y-8">
                <QuickActionsCard />
                <RecentActivitiesCard :sessions="trainingStore.sessions" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { useDashboardMetrics } from './composables/useDashboardMetrics'
import DashboardKpiCards from './components/DashboardKpiCards.vue'
import NextActivityCard from './components/NextActivityCard.vue'
import TopPlayersTable from './components/TopPlayersTable.vue'
import QuickActionsCard from './components/QuickActionsCard.vue'
import RecentActivitiesCard from './components/RecentActivitiesCard.vue'

const {
    authStore,
    teamStore,
    trainingStore,
    players,
    upcomingSessionsCount,
    featuredMatch,
    isUpcomingMatch,
    canAccessMatches,
    matchesLoading,
    matchesLoadFailed,
    nextSession,
    topPlayers,
    formatDate
} = useDashboardMetrics()
</script>
