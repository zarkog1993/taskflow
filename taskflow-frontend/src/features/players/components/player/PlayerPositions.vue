<template>
    <section v-if="positions.length" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 p-4">
        <h2 class="mb-3 text-sm font-bold text-gray-900 dark:text-white">Pozicija na terenu</h2>
        <PlayerPositionMap :position="player.primary_position" />
    </section>
</template>

<script setup>
import { computed } from 'vue'
import PlayerPositionMap from '../PlayerPositionMap.vue'

const props = defineProps({
    player: { type: Object, required: true }
})

const positions = computed(() => {
    const additional = props.player.secondary_positions ?? props.player.positions ?? []
    const list = Array.isArray(additional) ? additional : String(additional).split(/[,;]/)
    return [props.player.primary_position, ...list
        .map((position) => typeof position === 'object' ? position.code || position.name : position)
        .map((position) => String(position || '').trim())
        .filter((position) => position && position !== props.player.primary_position)]
        .filter(Boolean)
})
</script>
