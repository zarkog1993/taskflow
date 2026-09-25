<!-- Modal sa detaljima događaja i pregledom odziva/prisustva pozvanih igrača. -->
<template>
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl space-y-6">
            <!-- Zaglavlje Modala -->
            <div class="flex justify-between items-start border-b border-gray-800 pb-4">
                <div>
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-indigo-400 bg-indigo-950 px-2.5 py-1 rounded-md border border-indigo-800">
                        {{ event.team?.name || 'Svi timovi' }}
                    </span>
                    <h3 class="text-2xl font-black text-white mt-2">{{ event.title }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        📍 {{ event.location || 'Glavni teren' }} | ⏰ {{ formatDate(event.scheduled_at) }}
                    </p>
                </div>
                <button @click="$emit('close')" class="text-gray-400 hover:text-white text-lg font-bold">✕</button>
            </div>

            <!-- Lista Odziva Igrača -->
            <div class="space-y-4">
                <h4 class="text-xs font-semibold uppercase text-gray-400 tracking-wider">
                    Status Prisustva ({{ event.users?.length || 0 }})
                </h4>

                <div v-if="event.users && event.users.length > 0" class="max-h-60 overflow-y-auto space-y-2 pr-1">
                    <div
                        v-for="player in event.users"
                        :key="player.id"
                        class="flex items-center justify-between p-3 bg-gray-800/60 rounded-xl border border-gray-700/50"
                    >
                        <div class="flex items-center space-x-3">
                            <img
                                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(player.name)}&background=312e81&color=c7d2fe&size=64`"
                                class="w-8 h-8 rounded-full border border-indigo-500/30"
                            />
                            <span class="text-sm font-semibold text-white">{{ player.name }}</span>
                        </div>

                        <!-- Status Bedž na osnovu pivot.attended -->
                        <span
                            v-if="player.pivot?.attended === 1 || player.pivot?.attended === true"
                            class="text-[11px] font-bold text-emerald-400 bg-emerald-950/80 px-2.5 py-1 rounded-lg border border-emerald-800 flex items-center gap-1"
                        >
                            ✅ Dolazi
                        </span>
                        <span
                            v-else-if="player.pivot?.attended === 0 || player.pivot?.attended === false"
                            class="text-[11px] font-bold text-rose-400 bg-rose-950/80 px-2.5 py-1 rounded-lg border border-rose-800 flex items-center gap-1"
                        >
                            ❌ Otkazao
                        </span>
                        <span
                            v-else
                            class="text-[11px] font-bold text-amber-400 bg-amber-950/80 px-2.5 py-1 rounded-lg border border-amber-800 flex items-center gap-1"
                        >
                            ⏳ Čeka se odgovor
                        </span>
                    </div>
                </div>

                <div v-else class="text-center py-6 bg-gray-800/30 rounded-xl border border-dashed border-gray-700 text-xs text-gray-500 italic">
                    Nema pozvanih igrača za ovaj događaj.
                </div>
            </div>

            <!-- Dugme za Zatvaranje -->
            <div class="pt-2 border-t border-gray-800 flex justify-end">
                <button
                    @click="$emit('close')"
                    class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl transition"
                >
                    Zatvori
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { formatDate } from '../utils/calendarFormatters'

defineProps({
    event: {
        type: Object,
        required: true
    }
})

defineEmits(['close'])
</script>
