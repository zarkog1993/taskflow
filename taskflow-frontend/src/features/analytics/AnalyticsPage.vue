<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6 text-gray-900 dark:text-white">
        <!-- ZAGLAVLJE -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-100/80 dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 shadow-xl">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-emerald-700 dark:text-emerald-400">Napredna Analitika & Statistika</h1>
                <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Pregled forme, efikasnosti i redovnosti igrača na treninzima</p>
            </div>

            <!-- FILTERI -->
            <div class="flex w-full flex-col items-stretch gap-2 sm:w-auto sm:flex-row sm:items-center sm:gap-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 pointer-events-none">🔍</span>
                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Pretraži igrače..."
                        class="min-h-11 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl pl-8 pr-8 py-2 text-xs text-gray-900 dark:text-white outline-none focus:border-emerald-500 sm:w-52 placeholder:text-slate-500"
                    />
                    <button
                        v-if="searchQuery"
                        @click="searchQuery = ''"
                        type="button"
                        title="Poništi pretragu"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-slate-500 hover:text-gray-900 dark:hover:text-white cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <AppSelect
                    v-model="selectedTeamId"
                    class="min-h-11 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white outline-none focus:border-emerald-500 cursor-pointer sm:w-auto"
                >
                    <option value="all">Sve selekcije</option>
                    <option v-for="team in teams" :key="team.id" :value="team.id">
                        {{ team.name }}
                    </option>
                </AppSelect>
            </div>
        </div>

        <!-- UČITAVANJE -->
        <div v-if="loading" class="text-center py-12 text-slate-600 dark:text-slate-400 italic">
            Učitavanje analitičkih podataka...
        </div>

        <template v-else>
            <!-- METRIKE NA OSNOVU REALNIH PODATAKA (KPI) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <button
                    type="button"
                    @click="toggleStatFilter('goals')"
                    :aria-pressed="activeStatFilter === 'goals'"
                    class="w-full text-left bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 p-4 rounded-2xl flex items-center justify-between shadow-lg transition hover:border-emerald-400 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer"
                >
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Ukupno Golova</span>
                        <div class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1 font-mono">{{ summary.total_goals || 0 }}</div>
                        <span class="text-[10px] text-slate-600 dark:text-slate-400">Na {{ summary.total_matches || 0 }} utakmica</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-lg">⚽</div>
                </button>

                <button
                    type="button"
                    @click="toggleStatFilter('assists')"
                    :aria-pressed="activeStatFilter === 'assists'"
                    class="w-full text-left bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 p-4 rounded-2xl flex items-center justify-between shadow-lg transition hover:border-indigo-400 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 cursor-pointer"
                >
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Ukupno Asistencija</span>
                        <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1 font-mono">{{ summary.total_assists || 0 }}</div>
                        <span class="text-[10px] text-slate-600 dark:text-slate-400">Ključna dodavanja</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">👟</div>
                </button>

                <div class="bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 p-4 rounded-2xl flex items-center justify-between shadow-lg">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Prosek Golova po Meču</span>
                        <div class="text-2xl font-black text-amber-700 dark:text-amber-400 mt-1 font-mono">{{ summary.goals_per_match || 0 }}</div>
                        <span class="text-[10px] text-slate-600 dark:text-slate-400">Efikasnost tima</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 flex items-center justify-center text-lg">📊</div>
                </div>

                <div class="bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 p-4 rounded-2xl flex items-center justify-between shadow-lg">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-600 dark:text-slate-400 tracking-wider">Prosečan Odaziv na Treninge</span>
                        <div class="text-2xl font-black text-cyan-700 dark:text-cyan-400 mt-1 font-mono">{{ summary.avg_attendance_rate || 0 }}%</div>
                        <span class="text-[10px] text-slate-600 dark:text-slate-400">Redovnost tima</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950 border border-cyan-200 dark:border-cyan-800 text-cyan-700 dark:text-cyan-400 flex items-center justify-center text-lg">🏃</div>
                </div>
            </div>

            <!-- TABELA RANG-LISTE IGRAČA -->
            <div class="bg-slate-100/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-5 shadow-xl space-y-4">
                <div class="flex justify-between items-center border-b border-slate-200/60 dark:border-slate-700/60 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ activeStatFilter === 'goals' ? 'Lista strelaca' : activeStatFilter === 'assists' ? 'Lista asistenata' : 'Rang Lista Igrača po Indeksu Korisnosti' }}
                        </h3>
                        <p class="text-[10px] text-slate-600 dark:text-slate-400 mt-0.5">
                            {{ activeStatFilter === 'goals' ? 'Igrači koji su postigli najmanje jedan gol' : activeStatFilter === 'assists' ? 'Igrači koji su zabeležili najmanje jednu asistenciju' : 'Indeks = (Golovi + Asistencije) / Odigrane Utakmice' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-[10px] text-slate-600 dark:text-slate-400 font-mono">
                            Prikazano: {{ filteredPlayers.length }} / {{ players.length }}
                        </span>
                        <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-mono font-bold uppercase bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 px-2.5 py-1 rounded-lg">
                            G = Gol • A = Asistencija
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <div
                        v-if="!filteredPlayers.length"
                        class="text-center py-10 bg-white/40 dark:bg-slate-900/40 rounded-xl border border-dashed border-slate-200 dark:border-slate-700 space-y-2"
                    >
                        <div class="text-2xl">🔍</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            {{ activeStatFilter === 'goals' ? 'Nema strelaca koji odgovaraju filterima' : activeStatFilter === 'assists' ? 'Nema asistenata koji odgovaraju filterima' : 'Nema igrača koji odgovaraju pretrazi' }}
                            <span v-if="searchQuery" class="text-gray-900 dark:text-white font-bold">"{{ searchQuery }}"</span>.
                        </p>
                        <button
                            type="button"
                            @click="resetFilters"
                            class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 hover:underline cursor-pointer"
                        >
                            Poništi filtere
                        </button>
                    </div>

                    <table v-else class="min-w-[780px] w-full text-left border-collapse text-xs">
                        <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold uppercase text-[10px]">
                            <th class="p-3">#</th>
                            <th class="p-3">Igrač</th>
                            <th class="p-3">Tim</th>
                            <th class="p-3 text-center">Utakmice</th>
                            <th class="p-3 text-center">Golovi (G)</th>
                            <th class="p-3 text-center">Asistencije (A)</th>
                            <th class="p-3 text-center">Prisustvo Treningu</th>
                            <th class="p-3 text-right">Indeks Korisnosti</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/50 dark:divide-slate-700/50">
                        <tr
                            v-for="(player, index) in filteredPlayers"
                            :key="player.id"
                            class="hover:bg-white/40 dark:hover:bg-slate-900/40 transition"
                        >
                            <td class="p-3 font-mono font-bold text-slate-600 dark:text-slate-400">{{ index + 1 }}</td>
                            <td class="p-3 font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                  <span class="w-7 h-7 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-[10px] text-emerald-700 dark:text-emerald-400 font-mono font-bold">
                    #{{ player.jersey_number || '-' }}
                  </span>
                                <div>
                                    <div>{{ player.name }}</div>
                                    <div class="text-[10px] text-slate-500">{{ player.primary_position || 'Igrač' }}</div>
                                </div>
                            </td>
                            <td class="p-3 text-slate-700 dark:text-slate-300 font-medium">{{ player.team_name }}</td>
                            <td class="p-3 text-center font-mono text-slate-700 dark:text-slate-300">{{ player.matches_count }}</td>
                            <td class="p-3 text-center font-mono text-emerald-700 dark:text-emerald-400 font-bold">{{ player.goals }}</td>
                            <td class="p-3 text-center font-mono text-indigo-600 dark:text-indigo-400 font-bold">{{ player.assists }}</td>
                            <td class="p-3 text-center font-mono">
                  <span
                      :class="[
                      'px-2 py-0.5 rounded text-[10px] font-bold border',
                      player.attendance_rate >= 80
                        ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'
                        : 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800'
                    ]"
                  >
                    {{ player.attendance_rate }}%
                  </span>
                            </td>
                            <td class="p-3 text-right font-mono font-black text-emerald-700 dark:text-emerald-400 text-sm">
                                {{ player.performance_index }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import {useAnalytics} from "./composables/useAnalytics.js";

const {
    loading,
    selectedTeamId,
    searchQuery,
    teams,
    players,
    summary,
    filteredPlayers,
    activeStatFilter,
    toggleStatFilter,
    resetFilters,
    fetchAnalytics
} = useAnalytics()
</script>