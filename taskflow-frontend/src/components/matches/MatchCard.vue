<!-- src/components/matches/MatchCard.vue -->
<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-5 shadow-lg space-y-4">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
          <span class="text-[10px] font-bold uppercase text-indigo-400 bg-indigo-950/80 px-2.5 py-0.5 rounded border border-indigo-800/60">
            {{ match.team?.name || "Selekcija" }}
          </span>
                    <span
                        class="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                        :class="match.is_home ? 'bg-emerald-950 text-emerald-400 border border-emerald-800' : 'bg-amber-950 text-amber-400 border border-amber-800'"
                    >
            {{ match.is_home ? "Domaćin" : "Gost" }}
          </span>
                </div>

                <h3 class="text-xl font-black text-white">
                    {{ match.is_home ? "Rudar" : match.opponent }}
                    <span v-if="match.status === 'completed'" class="text-indigo-400 font-mono px-2">
            {{ match.home_score }} : {{ match.away_score }}
          </span>
                    <span v-else class="text-gray-400 font-normal px-1">vs</span>
                    {{ match.is_home ? match.opponent : "Rudar" }}
                </h3>

                <div class="flex items-center gap-4 text-xs text-gray-400 mt-2">
                    <span>📅 {{ formatDate(match.scheduled_at) }}</span>
                    <span>📍 {{ match.location || "Nije uneto" }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    @click="$emit('open-stats', match)"
                    class="bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 border border-indigo-500/30 text-xs font-bold px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2"
                >
                    📋 Detalji & Zapisnik
                </button>
                <button
                    @click="$emit('delete', match)"
                    title="Obriši meč"
                    class="p-2 bg-rose-950/60 hover:bg-rose-900 border border-rose-800/80 text-rose-300 hover:text-white rounded-xl transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Pregled Učinka -->
        <div v-if="match.status === 'completed'" class="pt-3 border-t border-gray-700/50 flex flex-wrap gap-6 text-xs">
            <div>
                <span class="text-gray-400 font-bold">⚽ Strelci:</span>
                <span class="text-white ml-1.5 font-medium">{{ getScorersText(match) || "Nema upisanih golova" }}</span>
            </div>
            <div>
                <span class="text-gray-400 font-bold">🎯 Asistenti:</span>
                <span class="text-white ml-1.5 font-medium">{{ getAssistersText(match) || "Nema upisanih asistencija" }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    match: { type: Object, required: true }
})
defineEmits(['open-stats', 'delete'])

const getScorersText = (match) => {
    if (!match.players) return ""
    return match.players.filter((p) => p.pivot?.goals > 0).map((p) => `${p.name} (${p.pivot.goals})`).join(", ")
}

const getAssistersText = (match) => {
    if (!match.players) return ""
    return match.players.filter((p) => p.pivot?.assists > 0).map((p) => `${p.name} (${p.pivot.assists})`).join(", ")
}

const formatDate = (dateStr) => {
    if (!dateStr) return ""
    return new Date(dateStr).toLocaleDateString("sr-RS", {
        day: "numeric", month: "short", hour: "2-digit", minute: "2-digit"
    })
}
</script>