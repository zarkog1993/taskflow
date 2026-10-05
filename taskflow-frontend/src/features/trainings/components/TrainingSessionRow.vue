<!-- Red u listi treninga: prikazuje datum, vreme, tip, naziv i prisustvo za jednu sesiju. -->
<template>
    <div
        class="bg-gray-100/90 dark:bg-gray-800/90 border border-gray-200/80 dark:border-gray-700/80 hover:border-indigo-500/50 rounded-2xl p-4 transition shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4"
        :class="canEdit && 'cursor-pointer'"
        @click="openEdit"
    >
        <!-- Datum i Vreme -->
        <div class="flex min-w-0 items-center gap-4 sm:min-w-[200px]">
            <div class="bg-indigo-50/80 dark:bg-indigo-950/80 border border-indigo-200/80 dark:border-indigo-800/80 rounded-xl px-3 py-2 text-center min-w-[65px]">
                <div class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase">{{ getDayName(session.scheduled_at) }}</div>
                <div class="text-lg font-black text-gray-900 dark:text-white">{{ getDayNumber(session.scheduled_at) }}</div>
            </div>
            <div>
                <div class="text-xs font-mono font-bold text-indigo-700 dark:text-indigo-300">⏰ {{ formatTime(session.scheduled_at) }}</div>
                <div class="text-[11px] text-gray-600 dark:text-gray-400">📍 {{ session.location || 'Glavni Teren' }}</div>
            </div>
        </div>

        <!-- Naziv i Opis -->
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2 mb-1">
        <span
            :class="[
            'text-[10px] font-bold uppercase px-2 py-0.5 rounded-md border',
            session.type === 'match' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border-emerald-500/30' : 'bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border-indigo-500/30'
          ]"
        >
          {{ session.type === 'match' ? '⚽ Utakmica' : '🏃‍♂️ Trening' }}
        </span>
                <h3 class="break-words text-base font-bold text-gray-900 dark:text-white">{{ session.title }}</h3>
            </div>
            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-1">
                {{ session.description || 'Nema unetog opisa za ovaj trening.' }}
            </p>
        </div>

        <!-- Prisustvo i Dugme -->
        <div class="flex items-center justify-between md:justify-end gap-4 border-t md:border-t-0 border-gray-200/60 dark:border-gray-700/60 pt-3 md:pt-0">
            <!-- Odgovori na pozivnice (RSVP) -->
            <div class="text-left md:text-right">
                <div class="text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400">Potvrdili dolazak</div>
                <div class="text-xs font-black text-emerald-700 dark:text-emerald-400">
                    {{ rsvp.accepted }} / {{ rsvp.total }} pozvanih
                </div>
                <div v-if="rsvp.total > 0" class="flex items-center gap-1 mt-1 md:justify-end">
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md border bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-500/30">
                        ✅ {{ rsvp.accepted }}
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md border bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30">
                        ❌ {{ rsvp.declined }}
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md border bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-500/30">
                        ⏳ {{ rsvp.pending }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button
                    v-if="canEdit"
                    @click.stop="$emit('edit', session)"
                    :aria-label="`Izmeni trening ${session.title}`"
                    title="Izmeni trening"
                    class="p-2 bg-indigo-50/60 dark:bg-indigo-950/60 hover:bg-indigo-100 dark:hover:bg-indigo-900 border border-indigo-200/80 dark:border-indigo-800/80 text-indigo-700 dark:text-indigo-300 rounded-xl transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.67a4.5 4.5 0 0 1-1.897 1.13L6 18.5l.7-2.685a4.5 4.5 0 0 1 1.13-1.897l9.032-9.431ZM19.5 7.125 16.875 4.5" />
                    </svg>
                </button>
                <button
                    @click.stop="$emit('delete', session)"
                    title="Obriši trening"
                    class="p-2 bg-rose-50/60 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900 border border-rose-200/80 dark:border-rose-800/80 text-rose-700 dark:text-rose-300 hover:text-gray-900 dark:hover:text-white rounded-xl transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { getDayName, getDayNumber, formatTime, getRsvpCounts } from '../utils/trainingFormatters'

const props = defineProps({
    session: {
        type: Object,
        required: true
    },
    canEdit: { type: Boolean, default: false }
})

const emit = defineEmits(['delete', 'edit'])

const rsvp = computed(() => getRsvpCounts(props.session))

const openEdit = () => {
    if (props.canEdit) emit('edit', props.session)
}

</script>
