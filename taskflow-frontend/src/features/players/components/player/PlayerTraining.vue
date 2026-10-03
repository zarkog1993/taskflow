<template>
    <section v-if="attendance != null" :class="compact ? 'rounded-xl border border-slate-800 bg-slate-950/50 p-3' : 'rounded-2xl border border-slate-800 bg-slate-900/90 p-5'">
        <h3 class="mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Trening i prisustvo</h3>
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-slate-400">Evidentirani treninzi</p>
            </div>
            <span class="font-mono text-lg font-semibold text-emerald-300">{{ attendance }}</span>
        </div>
        <div v-if="percentage != null" class="mt-3">
            <div class="mb-1 flex justify-between text-[10px] text-slate-400">
                <span>Dolaznost</span><span>{{ percentage }}%</span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-slate-800">
                <div class="h-full rounded-full bg-emerald-500" :style="{ width: `${Math.min(100, Math.max(0, percentage))}%` }"></div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    player: { type: Object, required: true },
    compact: { type: Boolean, default: false }
})

const attendance = computed(() => props.player.stats?.trainings_attended ?? props.player.trainings_attended ?? null)
const percentage = computed(() =>
    props.player.stats?.training_attendance_percentage ??
    props.player.stats?.attendance_percentage ??
    props.player.training_attendance_percentage ??
    null
)
</script>
