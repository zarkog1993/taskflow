<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
        <!-- Dobrodošlica i Zaglavlje -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-800/60 p-6 rounded-2xl border border-gray-700/60 backdrop-blur-md shadow-xl">
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">Dobrodošli nazad, {{ authStore.user?.name || 'Trener' }} 👋</h2>
                <p class="text-xs text-gray-400 mt-1">Pregled stanja u akademiji, predstojećih utakmica i statistike tima</p>
            </div>

            <div class="flex items-center space-x-3">
                <router-link
                    to="/trainings"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider px-4 py-3 rounded-xl shadow-lg transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Zakaži Događaj
                </router-link>
            </div>
        </div>

        <!-- KPI Statistika -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl shadow-xl flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Ukupno Ekipe</div>
                    <div class="text-3xl font-black text-white mt-1">{{ teamStore.teams.length }}</div>
                    <div class="text-[11px] text-indigo-400 mt-1">Aktivne selekcije</div>
                </div>
                <div class="p-3 bg-indigo-600/10 border border-indigo-500/20 text-indigo-400 rounded-2xl text-xl">
                    🛡️
                </div>
            </div>

            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl shadow-xl flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Registrovani Igrači</div>
                    <div class="text-3xl font-black text-emerald-400 mt-1">{{ userStore.users.length }}</div>
                    <div class="text-[11px] text-emerald-500 mt-1">Članovi akademije</div>
                </div>
                <div class="p-3 bg-emerald-600/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-xl">
                    🏃‍♂️
                </div>
            </div>

            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl shadow-xl flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Predstojeći Treninzi</div>
                    <div class="text-3xl font-black text-yellow-400 mt-1">{{ upcomingSessionsCount }}</div>
                    <div class="text-[11px] text-yellow-500 mt-1">Ove nedelje</div>
                </div>
                <div class="p-3 bg-yellow-600/10 border border-yellow-500/20 text-yellow-400 rounded-2xl text-xl">
                    📅
                </div>
            </div>

            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl shadow-xl flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Prosečni Golovi</div>
                    <div class="text-3xl font-black text-purple-400 mt-1">{{ totalGoalsCount }}</div>
                    <div class="text-[11px] text-purple-400 mt-1">Ukupno u sezoni</div>
                </div>
                <div class="p-3 bg-purple-600/10 border border-purple-500/20 text-purple-400 rounded-2xl text-xl">
                    ⚽
                </div>
            </div>
        </div>

        <!-- Glavni Sadržaj -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Leva Kolona: Današnji Fokalni Događaji & Najbolji Igrači -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Današnji / Sledeći Događaj -->
                <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <h3 class="text-lg font-bold text-white">Sledeća Aktivnost</h3>
                        </div>
                        <router-link to="/trainings" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                            Pogledaj sve →
                        </router-link>
                    </div>

                    <div v-if="nextSession" class="bg-gray-900/80 border border-gray-700/60 p-5 rounded-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
              <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded border bg-indigo-500/20 text-indigo-300 border-indigo-500/30">
                {{ nextSession.type === 'match' ? 'Utakmica' : 'Trening' }}
              </span>
                            <h4 class="text-xl font-bold text-white mt-2">{{ nextSession.title }}</h4>
                            <p class="text-xs text-gray-400 mt-1">📍 {{ nextSession.location || 'Glavni Teren' }} | ⏰ {{ formatDate(nextSession.scheduled_at) }}</p>
                        </div>

                        <router-link
                            to="/trainings"
                            class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg transition"
                        >
                            Evidencija Prisustva
                        </router-link>
                    </div>

                    <div v-else class="text-center py-8 text-gray-500 italic bg-gray-900/40 rounded-xl border border-dashed border-gray-700">
                        Nema zakazanih aktivacija za naredne dane.
                    </div>
                </div>

                <!-- Tabela Najboljih Strelaca / Igrača -->
                <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-white mb-4">Top Igrači po Golovima i Učinku</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-900/60 border-b border-gray-700 text-gray-400 uppercase text-[10px]">
                                <th class="py-3 px-4">Igrač</th>
                                <th class="py-3 px-4 text-center">Pozicija</th>
                                <th class="py-3 px-4 text-center">Utakmice</th>
                                <th class="py-3 px-4 text-center">Golovi</th>
                                <th class="py-3 px-4 text-center">Asistencije</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/50 text-xs">
                            <tr v-for="user in topPlayers" :key="user.id" class="hover:bg-gray-700/30 transition">
                                <td class="py-3 px-4 font-semibold text-white flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-[10px]">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <router-link :to="`/players/${user.id}`" class="hover:text-indigo-400">
                                        {{ user.name }}
                                    </router-link>
                                </td>
                                <td class="py-3 px-4 text-center text-gray-400 font-mono">
                                    {{ user.player_profile?.primary_position || 'CM' }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-emerald-400">
                                    {{ user.player_profile?.matches_played || 0 }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-yellow-400">
                                    {{ user.player_profile?.goals || 0 }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-purple-400">
                                    {{ user.player_profile?.assists || 0 }}
                                </td>
                            </tr>

                            <tr v-if="topPlayers.length === 0">
                                <td colspan="5" class="text-center py-6 text-gray-500 italic">Nema zabeleženih statistika igrača.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Desna Kolona: Brze Akcije & Pregled Ekipa -->
            <div class="space-y-8">
                <!-- Brze Akcije -->
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

                <!-- Pregled Selekcija -->
                <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Aktivne Selekcije</h3>

                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
                        <div
                            v-for="team in teamStore.teams"
                            :key="team.id"
                            class="flex justify-between items-center p-3 bg-gray-900/60 rounded-xl border border-gray-700/40"
                        >
                            <div>
                                <div class="text-xs font-bold text-white">{{ team.name }}</div>
                                <div class="text-[10px] text-gray-400">{{ team.members?.length || 0 }} igrača</div>
                            </div>

                            <span class="text-[10px] font-mono font-bold text-indigo-400 bg-indigo-950 px-2 py-0.5 rounded border border-indigo-800">
                {{ team.age_group }}
              </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useTeamStore } from '../stores/team'
import { useUserStore } from '../stores/user'
import { useTrainingStore } from '../stores/training'

const authStore = useAuthStore()
const teamStore = useTeamStore()
const userStore = useUserStore()
const trainingStore = useTrainingStore()

onMounted(() => {
    teamStore.fetchTeams()
    userStore.fetchUsers()
    trainingStore.fetchSessions()
})

const upcomingSessionsCount = computed(() => {
    return trainingStore.sessions.filter(s => s.status === 'planned').length
})

const totalGoalsCount = computed(() => {
    return userStore.users.reduce((sum, u) => sum + (u.player_profile?.goals || 0), 0)
})

const nextSession = computed(() => {
    return trainingStore.sessions.find(s => s.status === 'planned') || null
})

const topPlayers = computed(() => {
    return [...userStore.users]
        .sort((a, b) => (b.player_profile?.goals || 0) - (a.player_profile?.goals || 0))
        .slice(0, 5)
})

const formatDate = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleString('sr-RS', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}
</script>