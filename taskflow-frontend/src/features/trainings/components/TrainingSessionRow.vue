<!-- Red u listi treninga: prikazuje datum, vreme, tip, naziv i prisustvo za jednu sesiju. -->
<template>
    <div
        class="bg-gray-800/90 border border-gray-700/80 hover:border-indigo-500/50 rounded-2xl p-4 transition shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
        <!-- Datum i Vreme -->
        <div class="flex items-center gap-4 min-w-[200px]">
            <div class="bg-indigo-950/80 border border-indigo-800/80 rounded-xl px-3 py-2 text-center min-w-[65px]">
                <div class="text-[10px] font-bold text-indigo-400 uppercase">{{ getDayName(session.scheduled_at) }}</div>
                <div class="text-lg font-black text-white">{{ getDayNumber(session.scheduled_at) }}</div>
            </div>
            <div>
                <div class="text-xs font-mono font-bold text-indigo-300">⏰ {{ formatTime(session.scheduled_at) }}</div>
                <div class="text-[11px] text-gray-400">📍 {{ session.location || 'Glavni Teren' }}</div>
            </div>
        </div>

        <!-- Naziv i Opis -->
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
        <span
            :class="[
            'text-[10px] font-bold uppercase px-2 py-0.5 rounded-md border',
            session.type === 'match' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30'
          ]"
        >
          {{ session.type === 'match' ? '⚽ Utakmica' : '🏃‍♂️ Trening' }}
        </span>
                <h3 class="text-base font-bold text-white">{{ session.title }}</h3>
            </div>
            <p class="text-xs text-gray-400 line-clamp-1">
                {{ session.description || 'Nema unetog opisa za ovaj trening.' }}
            </p>
        </div>

        <!-- Prisustvo i Dugme -->
        <div class="flex items-center justify-between md:justify-end gap-4 border-t md:border-t-0 border-gray-700/60 pt-3 md:pt-0">
            <div class="text-left md:text-right">
                <div class="text-[10px] font-bold uppercase text-gray-400">Prisustvo</div>
                <div class="text-xs font-black text-emerald-400">
                    {{ getAttendedCount(session) }} / {{ session.users?.length || session.attendees?.length || 0 }} igrača
                </div>
            </div>

            <button
                @click="$emit('open-attendance', session)"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow-md flex items-center gap-1.5 cursor-pointer"
            >
                <span>👥</span> Evidencija
            </button>
            <!-- Dugme za Brisanje Treninga -->
            <button
                @click="$emit('delete', session)"
                title="Obriši trening"
                class="p-2 bg-rose-950/60 hover:bg-rose-900 border border-rose-800/80 text-rose-300 hover:text-white rounded-xl transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { getDayName, getDayNumber, formatTime, getAttendedCount } from '../utils/trainingFormatters'

defineProps({
    session: {
        type: Object,
        required: true
    }
})

defineEmits(['open-attendance', 'delete'])
</script>
