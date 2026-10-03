<template>
    <section v-if="information.length" :class="compact ? 'mt-3 border-t border-slate-800 pt-3' : 'rounded-2xl border border-slate-800 bg-slate-900/90 p-4'">
        <h2 v-if="!compact" class="mb-2 text-sm font-bold text-white">Informacije o igraču</h2>
        <dl :class="compact ? 'grid grid-cols-2 gap-x-3 gap-y-2' : 'divide-y divide-slate-800/80'">
            <div v-for="item in information" :key="item.label" :class="compact ? 'min-w-0' : 'flex items-center justify-between gap-3 py-2 text-xs'">
                <dt class="text-slate-400">{{ item.label }}</dt>
                <dd :class="compact ? 'mt-0.5 truncate text-xs font-semibold text-slate-200' : 'truncate text-right font-medium text-slate-200'">{{ item.value }}</dd>
            </div>
        </dl>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    player: { type: Object, required: true },
    formatFoot: { type: Function, default: null },
    compact: { type: Boolean, default: false }
})

const age = computed(() => {
    if (!props.player.date_of_birth) return null
    const [year, month, day] = String(props.player.date_of_birth).slice(0, 10).split('-').map(Number)
    if (!year || !month || !day) return null
    const now = new Date()
    const years = now.getFullYear() - year -
        (now.getMonth() + 1 < month || (now.getMonth() + 1 === month && now.getDate() < day) ? 1 : 0)
    return years >= 0 ? years : null
})

const information = computed(() => [
    ...(props.compact ? [
        { label: 'Uzrast', value: age.value === null ? null : `${age.value} god.` },
        { label: 'Visina', value: props.player.height ? `${props.player.height} cm` : null },
        { label: 'Težina', value: props.player.weight ? `${props.player.weight} kg` : null },
        { label: 'Jača noga', value: props.player.preferred_foot && props.formatFoot
            ? props.formatFoot(props.player.preferred_foot)
            : null }
    ] : [
        { label: 'Ekipa', value: props.player.team?.name },
        { label: 'Datum rođenja', value: props.player.date_of_birth },
        { label: 'Ugovor / članarina', value: props.player.membership_fee ?? props.player.monthly_fee ?? props.player.contract?.membership_fee }
    ])
].filter((item) => item.value !== null && item.value !== undefined && item.value !== ''))
</script>
