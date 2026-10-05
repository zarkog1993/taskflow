<!-- src/components/matches/MatchCard.vue -->
<template>
    <div :class="['relative rounded-2xl border border-gray-200/80 dark:border-gray-700/80 bg-gray-100/80 dark:bg-gray-800/80 p-5 shadow-lg transition', canViewStats ? 'cursor-pointer hover:border-indigo-400/60 hover:shadow-xl' : '']">
        <router-link
            v-if="canViewStats"
            :to="{ name: 'match-stats', params: { id: match.id } }"
            :aria-label="`Otvori detalje i zapisnik utakmice protiv ${match.opponent}`"
            class="absolute inset-0 z-0 rounded-2xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
        />
        <div :class="['relative z-10 space-y-4', canViewStats ? 'pointer-events-none' : '']">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
          <span class="text-[10px] font-bold uppercase text-indigo-600 dark:text-indigo-400 bg-indigo-50/80 dark:bg-indigo-950/80 px-2.5 py-0.5 rounded border border-indigo-200/60 dark:border-indigo-800/60">
            {{ match.team?.name || "Selekcija" }}
          </span>
                    <span
                        class="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                        :class="match.is_home ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-950 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800'"
                    >
            {{ match.is_home ? "Domaćin" : "Gost" }}
          </span>
                    <span
                        v-if="match.status === 'canceled'"
                        class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-rose-50 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800"
                    >
                        Otkazana
                    </span>
                </div>

                <h3 class="text-xl font-black text-gray-900 dark:text-white">
                    {{ match.is_home ? "Rudar" : match.opponent }}
                    <span v-if="match.status === 'completed'" class="text-indigo-600 dark:text-indigo-400 font-mono px-2">
            {{ match.home_score }} : {{ match.away_score }}
          </span>
                    <span v-else class="text-gray-600 dark:text-gray-400 font-normal px-1">vs</span>
                    {{ match.is_home ? match.opponent : "Rudar" }}
                </h3>

                <div class="flex items-center gap-4 text-xs text-gray-600 dark:text-gray-400 mt-2">
                    <span>📅 {{ formatDate(match.scheduled_at) }}</span>
                    <span>📍 {{ match.location || "Nije uneto" }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <router-link
                    v-if="canPlanLineup && match.status === 'scheduled'"
                    :to="{ name: 'match-lineup', params: { id: match.id } }"
                    class="pointer-events-auto bg-emerald-600/15 hover:bg-emerald-600/30 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 text-xs font-bold px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2"
                >
                    ⚽ Planer sastava
                </router-link>
                <button
                    @click="$emit('delete', match)"
                    title="Obriši meč"
                    class="pointer-events-auto p-2 bg-rose-50/60 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900 border border-rose-200/80 dark:border-rose-800/80 text-rose-700 dark:text-rose-300 hover:text-gray-900 dark:hover:text-white rounded-xl transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Pregled Učinka -->
        <div v-if="match.status === 'completed' && hasContributions" class="pt-3 border-t border-gray-200/50 dark:border-gray-700/50 flex flex-wrap gap-6 text-xs">
            <div v-if="scorersText">
                <span class="text-gray-600 dark:text-gray-400 font-bold">⚽ Strelci:</span>
                <span class="text-gray-900 dark:text-white ml-1.5 font-medium">{{ scorersText }}</span>
            </div>
            <div v-if="assistersText">
                <span class="text-gray-600 dark:text-gray-400 font-bold">🎯 Asistenti:</span>
                <span class="text-gray-900 dark:text-white ml-1.5 font-medium">{{ assistersText }}</span>
            </div>
        </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue"
import { useAuthStore } from "../../stores/auth"

defineEmits(['delete'])

const props = defineProps({
    match: { type: Object, required: true }
})

const authStore = useAuthStore()
const canViewStats = computed(() => {
    const user = authStore.user
    return Boolean(
        user?.is_admin
        || user?.roles?.some(role => ['admin', 'super-admin'].includes(role.slug))
        || user?.subscription_features?.includes('advanced_stats')
    )
})
const canPlanLineup = computed(() => {
    const user = authStore.user
    return Boolean(
        user?.is_admin
        || user?.roles?.some(role => ['admin', 'super-admin'].includes(role.slug))
        || user?.subscription_features?.includes('tactics')
    )
})

const scorersText = computed(() =>
    (props.match.players || [])
        .filter((player) => player.pivot?.goals > 0)
        .map((player) => `${player.name} (${player.pivot.goals})`)
        .join(", "),
)

const assistersText = computed(() =>
    (props.match.players || [])
        .filter((player) => player.pivot?.assists > 0)
        .map((player) => `${player.name} (${player.pivot.assists})`)
        .join(", "),
)

const hasContributions = computed(() => Boolean(scorersText.value || assistersText.value))

const formatDate = (dateStr) => {
    if (!dateStr) return ""
    return new Date(dateStr).toLocaleDateString("sr-RS", {
        day: "numeric", month: "short", hour: "2-digit", minute: "2-digit"
    })
}
</script>