<template>
    <section v-if="positions.length" class="rounded-2xl border border-slate-800 bg-slate-900/90 p-4">
        <h2 class="mb-3 text-sm font-bold text-white">Pozicija na terenu</h2>
        <PlayerPositionMap :position="player.primary_position" />
        <div class="mt-2 flex flex-wrap items-center gap-1.5">
            <span v-for="position in positions" :key="position" class="rounded-md bg-slate-800 px-2 py-1 text-xs font-semibold text-emerald-300">
                {{ position }}<span v-if="position === player.primary_position" class="ml-1 text-[9px] font-medium text-slate-400">Primarna</span>
            </span>
        </div>
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
