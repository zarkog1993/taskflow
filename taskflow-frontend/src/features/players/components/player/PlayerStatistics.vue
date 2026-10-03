<template>
    <section v-if="groups.length" class="rounded-2xl border border-slate-800 bg-slate-900/90 p-5">
        <h2 class="mb-3 text-sm font-bold text-white">Detaljna statistika</h2>
        <div class="grid gap-2 sm:grid-cols-2">
            <section v-for="group in groups" :key="group.label" class="rounded-lg bg-slate-950/45 px-3 py-2.5">
                <h3 class="mb-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400">{{ group.label }}</h3>
                <dl>
                    <div v-for="stat in group.stats" :key="stat.key" class="flex items-center justify-between gap-2 border-t border-slate-800/70 py-1.5 text-xs first:border-0">
                        <dt class="text-slate-400"><span class="mr-1.5">{{ stat.icon }}</span>{{ stat.label }}</dt>
                        <dd class="font-mono font-semibold tabular-nums text-slate-100">{{ stat.value }}{{ stat.suffix || '' }}</dd>
                    </div>
                </dl>
            </section>
            <PlayerTraining :player="player" compact />
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import PlayerTraining from './PlayerTraining.vue'

const props = defineProps({
    player: { type: Object, required: true }
})

const definitions = [
    {
        label: 'Napad i učinak',
        stats: [
            { key: 'goals', label: 'Golovi', icon: '⚽' },
            { key: 'assists', label: 'Asistencije', icon: '🎯' },
            { key: 'shots', label: 'Šutevi', icon: '↗' }
        ]
    },
    {
        label: 'Nastupi',
        stats: [
            { key: 'matches_played', label: 'Utakmice', icon: '⚽' },
            { key: 'starts', label: 'Startovi', icon: '↗' },
            { key: 'minutes', label: 'Minuti', icon: '⏱' }
        ]
    },
    {
        label: 'Dodavanja',
        stats: [
            { key: 'passes_completed', label: 'Uspešna dodavanja', icon: '⇢' },
            { key: 'passing_accuracy', label: 'Preciznost', icon: '✓', suffix: '%' }
        ]
    },
    {
        label: 'Odbrana i disciplina',
        stats: [
            { key: 'tackles', label: 'Uklizavanja', icon: '↘' },
            { key: 'interceptions', label: 'Intercepcije', icon: '↔' },
            { key: 'yellow_cards', label: 'Žuti kartoni', icon: '🟨' },
            { key: 'red_cards', label: 'Crveni kartoni', icon: '🟥' }
        ]
    }
]

const groups = computed(() => {
    const source = props.player.stats || {}
    return definitions.map((group) => ({
        ...group,
        stats: group.stats
            .filter(({ key }) =>
                (Object.hasOwn(source, key) && source[key] != null) ||
                (Object.hasOwn(props.player, key) && props.player[key] != null)
            )
            .map((stat) => ({
                ...stat,
                value: source[stat.key] ?? props.player[stat.key]
            }))
    })).filter((group) => group.stats.length)
})
</script>
