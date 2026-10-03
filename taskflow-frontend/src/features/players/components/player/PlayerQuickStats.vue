<template>
    <section v-if="stats.length" class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div v-for="stat in stats" :key="stat.key" class="flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/90 p-3">
                <span aria-hidden="true" class="text-xl">{{ stat.icon }}</span>
                <dl class="min-w-0">
                    <dt class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">{{ stat.label }}</dt>
                    <dd class="mt-0.5 font-mono text-2xl font-bold leading-none tabular-nums text-white">{{ stat.value }}{{ stat.suffix || '' }}</dd>
                </dl>
            </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    player: { type: Object, required: true }
})

const definitions = [
    { key: 'matches_played', label: 'Utakmice', icon: '⚽' },
    { key: 'goals', label: 'Golovi', icon: '🎯' },
    { key: 'assists', label: 'Asistencije', icon: '👟' },
    { key: 'minutes', label: 'Minuti', icon: '⏱️' }
]

const stats = computed(() => {
    const source = props.player.stats || {}
    const available = definitions.filter(({ key }) =>
        (Object.hasOwn(source, key) && source[key] != null) ||
        (Object.hasOwn(props.player, key) && props.player[key] != null)
    ).map(({ key, ...stat }) => ({
        key,
        ...stat,
        value: source[key] ?? props.player[key]
    }))

    if (available.length < 4) {
        const attendance = source.trainings_attended ?? props.player.trainings_attended
        if (attendance != null) {
            available.push({ key: 'trainings_attended', label: 'Treninzi', icon: '🏃', value: attendance })
        }
    }

    return available.slice(0, 4)
})
</script>
