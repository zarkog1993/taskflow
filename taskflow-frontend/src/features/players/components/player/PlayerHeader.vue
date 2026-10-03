<template>
    <section class="rounded-2xl border border-slate-800 bg-slate-900/90 p-4 sm:p-5">
        <div class="grid grid-cols-1 items-center gap-4 sm:grid-cols-[5rem_minmax(0,1fr)_19rem]">
            <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-slate-800 sm:h-24 sm:w-24">
                <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="h-full w-full object-cover" />
                <div v-else class="flex h-full items-center justify-center text-2xl font-bold text-slate-500">{{ initials }}</div>
                <span v-if="hasValue(player.jersey_number)" class="absolute bottom-1.5 left-1.5 rounded bg-slate-950/90 px-1.5 py-0.5 font-mono text-xs font-bold text-white">
                    #{{ player.jersey_number }}
                </span>
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <h1 class="text-xl font-bold tracking-tight text-white sm:text-2xl">{{ player.name }}</h1>
                    <span v-if="statusLabel" class="rounded-full bg-emerald-950 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-emerald-300">
                        {{ statusLabel }}
                    </span>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span v-for="position in positions" :key="position" class="rounded-md bg-emerald-950/80 px-2.5 py-1 text-xs font-semibold text-emerald-300">
                        {{ position }}
                    </span>
                    <span v-if="category" class="text-xs text-slate-400">{{ category }}</span>
                </div>
            </div>

            <div class="flex min-w-0 flex-col items-start gap-1 sm:items-stretch">
                <p v-if="player.email" class="break-all text-xs text-slate-400">
                    {{ player.email }}
                </p>
                <button
                    type="button"
                    class="mt-1 self-start rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-500"
                    @click="$emit('edit')"
                >
                    Izmeni profil
                </button>
                <p v-if="player.seniority || player.team?.age_group" class="mt-1 text-xs text-slate-400">
                    {{ category }}
                </p>
                <PlayerInformation :player="player" :format-foot="formatFoot" compact />
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { getPlayerCategory } from '../../utils/playerFormatters'
import PlayerInformation from './PlayerInformation.vue'

defineEmits(['edit'])

const props = defineProps({
    player: { type: Object, required: true },
    formatFoot: { type: Function, required: true }
})

const hasValue = (value) => value !== null && value !== undefined && value !== ''
const initials = computed(() => String(props.player.name || '')
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0).toUpperCase())
    .join(''))
const category = computed(() =>
    props.player.team?.age_group || props.player.seniority
        ? getPlayerCategory(props.player)
        : ''
)
const positions = computed(() => {
    const additional = props.player.secondary_positions ?? props.player.positions ?? []
    const list = Array.isArray(additional) ? additional : String(additional).split(/[,;]/)
    return [props.player.primary_position, ...list
        .map((position) => typeof position === 'object' ? position.code || position.name : position)
        .map((position) => String(position || '').trim())
        .filter((position) => position && position !== props.player.primary_position)]
        .filter(Boolean)
})
const statusLabel = computed(() => {
    if (!props.player.physical_status) return ''
    return String(props.player.physical_status).toLowerCase() === 'fit'
        ? 'Spreman'
        : props.player.physical_status
})
</script>
