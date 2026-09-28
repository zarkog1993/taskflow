<!-- Modal za evidenciju prisustva igrača na treningu. -->
<template>
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-6 bg-gray-900 border-b border-gray-700 relative">
                <button @click="$emit('close')" class="absolute top-5 right-5 text-gray-400 hover:text-white">✕</button>
                <h3 class="text-2xl font-black text-white">{{ session.title }}</h3>
                <p class="text-xs text-gray-400 mt-1">📍 {{ session.location || 'Glavni Teren' }} | ⏰ {{ formatTime(session.scheduled_at) }}</p>
            </div>

            <div class="p-4 bg-gray-900/60 border-b border-gray-700 flex justify-between items-center">
                <div class="text-xs font-bold text-emerald-400">Prisutni: {{ selectedIds.length }}</div>
                <div class="flex gap-2">
                    <button @click="$emit('select-all')" class="text-xs font-bold text-indigo-400 bg-indigo-950/60 px-2.5 py-1 rounded-lg border border-indigo-800">✓ Označi sve</button>
                    <button @click="$emit('clear-all')" class="text-xs font-bold text-gray-400 bg-gray-800 px-2.5 py-1 rounded-lg border border-gray-700">✕ Poništi</button>
                </div>
            </div>

            <div class="p-4 overflow-y-auto space-y-4 flex-1">
                <!-- Odgovori na email pozivnice (samo za pregled) -->
                <div v-if="invitedPlayers.length > 0" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-[11px] font-black uppercase tracking-wider text-gray-400">
                            Odgovori na pozivnice
                        </h4>
                        <span class="text-[11px] font-bold text-emerald-400">
                            ✅ {{ rsvp.accepted }} · ❌ {{ rsvp.declined }} · ⏳ {{ rsvp.pending }}
                        </span>
                    </div>

                    <div
                        v-for="player in invitedPlayers"
                        :key="`rsvp-${player.id}`"
                        class="flex items-center justify-between p-2.5 rounded-xl border bg-gray-900/50 border-gray-700/50"
                    >
                        <div>
                            <span class="text-xs font-bold text-white">{{ player.name }}</span>
                            <span v-if="player.pivot?.responded_at" class="text-[10px] text-gray-500 ml-2">
                                {{ formatRespondedAt(player.pivot.responded_at) }}
                            </span>
                        </div>
                        <span
                            :class="['text-[10px] font-bold px-2.5 py-1 rounded-lg border', RSVP_BADGES[getRsvpStatus(player)].class]"
                        >
                            {{ RSVP_BADGES[getRsvpStatus(player)].label }}
                        </span>
                    </div>
                </div>

                <!-- Ručna evidencija prisustva -->
                <div class="space-y-2">
                    <h4 v-if="invitedPlayers.length > 0" class="text-[11px] font-black uppercase tracking-wider text-gray-400 pt-2 border-t border-gray-700/60">
                        Evidencija prisustva
                    </h4>
                    <div
                        v-for="user in users"
                        :key="user.id"
                        @click="$emit('toggle', user.id)"
                        class="flex items-center justify-between p-3 rounded-xl border cursor-pointer select-none"
                        :class="selectedIds.includes(user.id) ? 'bg-emerald-950/20 border-emerald-500/50' : 'bg-gray-900/50 border-gray-700/50'"
                    >
                        <span class="text-xs font-bold text-white">{{ user.name }}</span>
                        <span :class="['text-xs font-bold px-3 py-1 rounded-xl border', selectedIds.includes(user.id) ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40' : 'bg-gray-800 text-gray-500 border-gray-700']">
                            {{ selectedIds.includes(user.id) ? 'Prisutan' : 'Odsutan' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-900 border-t border-gray-700 flex justify-end gap-3">
                <button @click="$emit('close')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Odustani</button>
                <button @click="$emit('save')" class="px-5 py-2 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl">Sačuvaj Prisustvo</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import {
    formatTime,
    formatRespondedAt,
    getInvitedPlayers,
    getRsvpCounts,
    getRsvpStatus
} from '../utils/trainingFormatters'

const RSVP_BADGES = {
    accepted: { label: '✅ Dolazi', class: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' },
    declined: { label: '❌ Ne dolazi', class: 'bg-rose-500/10 text-rose-400 border-rose-500/30' },
    pending: { label: '⏳ Bez odgovora', class: 'bg-amber-500/10 text-amber-400 border-amber-500/30' }
}

const props = defineProps({
    session: {
        type: Object,
        required: true
    },
    users: {
        type: Array,
        default: () => []
    },
    selectedIds: {
        type: Array,
        default: () => []
    }
})

defineEmits(['close', 'toggle', 'select-all', 'clear-all', 'save'])

const invitedPlayers = computed(() => getInvitedPlayers(props.session))
const rsvp = computed(() => getRsvpCounts(props.session))
</script>
