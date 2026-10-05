<template>
    <section v-if="matches.length || error" class="min-w-0 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 p-5">
        <h2 class="mb-3 border-b border-slate-200 dark:border-slate-800 pb-3 text-sm font-bold text-gray-900 dark:text-white">Nedavne utakmice</h2>
        <p v-if="error" class="text-xs text-amber-700 dark:text-amber-300">{{ error }}</p>
        <div v-else-if="matches.length" class="overflow-x-auto">
            <table class="min-w-[560px] w-full text-left text-xs">
                <thead class="text-[10px] uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="py-2 pr-3 font-semibold">Protivnik</th>
                        <th class="px-3 py-2 font-semibold">Datum</th>
                        <th v-if="hasMinutes" class="px-3 py-2 text-center font-semibold">Min.</th>
                        <th v-if="hasCards" class="px-3 py-2 text-center font-semibold">Kartoni</th>
                        <th class="px-3 py-2 text-center font-semibold">Golovi</th>
                        <th class="px-3 py-2 text-center font-semibold">Asist.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    <tr v-for="match in matches" :key="match.id" class="text-slate-700 dark:text-slate-300 transition hover:bg-slate-100/50 dark:hover:bg-slate-800/50">
                        <td class="max-w-40 truncate py-2.5 pr-3 font-medium text-gray-900 dark:text-white">{{ match.opponent }}</td>
                        <td class="whitespace-nowrap px-3 py-2.5 text-slate-600 dark:text-slate-400">{{ formatDate(match.scheduled_at) }}</td>
                        <td v-if="hasMinutes" class="px-3 py-2.5 text-center font-mono">{{ match.player_stats.minutes ?? match.player_stats.minutes_played ?? '—' }}</td>
                        <td v-if="hasCards" class="px-3 py-2.5 text-center font-mono">{{ cards(match.player_stats) }}</td>
                        <td class="px-3 py-2.5 text-center font-mono">{{ match.player_stats.goals ?? '—' }}</td>
                        <td class="px-3 py-2.5 text-center font-mono">{{ match.player_stats.assists ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    matches: { type: Array, default: () => [] },
    error: { type: String, default: '' }
})

const hasMinutes = computed(() => props.matches.some((match) =>
    match.player_stats?.minutes != null || match.player_stats?.minutes_played != null
))
const hasCards = computed(() => props.matches.some((match) =>
    match.player_stats?.yellow_cards != null || match.player_stats?.red_cards != null
))
const cards = (stats) => [stats.yellow_cards, stats.red_cards]
    .filter((card) => card != null)
    .join(' / ') || '—'

const formatDate = (value) => {
    if (!value) return '—'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return '—'
    return date.toLocaleDateString('sr-Latn-RS', { day: '2-digit', month: 'short' })
}
</script>
