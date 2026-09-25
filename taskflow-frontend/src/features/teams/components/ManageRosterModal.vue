<template>
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-5 max-h-[85vh] flex flex-col">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3 shrink-0">
                <div>
                    <h3 class="text-lg font-black text-white">Sastav Ekipe: {{ team.name }}</h3>
                    <p class="text-xs text-gray-400">Dodajte ili izbacite igrače iz ove selekcije</p>
                </div>
                <button @click="$emit('close')" class="text-gray-400 hover:text-white font-bold">✕</button>
            </div>

            <div class="overflow-y-auto space-y-4 pr-1 flex-1">
                <!-- Dodavanje slobodnih igrača -->
                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1.5">Dodaj Slobodnog Igrača u Ekipu:</label>
                    <div class="flex gap-2">
                        <select v-model="selectedPlayerId" class="flex-1 bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
                            <option :value="null" disabled>Izaberite igratča bez ekipe...</option>
                            <option v-for="p in unassignedPlayers" :key="p.id" :value="p.id">
                                {{ p.name }} (#{{ p.jersey_number || '-' }} - {{ p.primary_position }})
                            </option>
                        </select>
                        <button @click="$emit('assign')" :disabled="!selectedPlayerId" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition disabled:opacity-50 cursor-pointer">
                            + Dodaj
                        </button>
                    </div>
                </div>

                <!-- Lista Igrača u Ekipi sa opcijom za uklanjanje -->
                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1.5">Trenutni Igrači u Ekipi:</label>
                    <div v-if="teamPlayers.length" class="space-y-2">
                        <div v-for="player in teamPlayers" :key="player.id" class="flex items-center justify-between p-3 bg-gray-900/80 border border-gray-700/60 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-950 border border-indigo-800 flex items-center justify-center font-bold text-indigo-300 text-xs">
                                    #{{ player.jersey_number || '-' }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">{{ player.name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ player.primary_position }}</p>
                                </div>
                            </div>
                            <button @click="$emit('remove', player.id)" class="text-rose-400 hover:text-rose-300 text-xs font-bold px-2 py-1 rounded bg-rose-950/60 border border-rose-800/80 cursor-pointer">
                                Ukloni
                            </button>
                        </div>
                    </div>
                    <p v-else class="text-xs text-gray-500 italic py-4 text-center">Nema igrača u ovoj ekipi.</p>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-gray-700 shrink-0">
                <button @click="$emit('close')" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2 rounded-xl transition cursor-pointer">
                    Završi
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    team: {
        type: Object,
        required: true
    },
    teamPlayers: {
        type: Array,
        default: () => []
    },
    unassignedPlayers: {
        type: Array,
        default: () => []
    }
})

defineEmits(['close', 'assign', 'remove'])

// Dvosmerno povezan izbor igrača za dodavanje — vlasništvo ostaje kod roditeljske komponente
// da bi se tačno očuvalo ponašanje resetovanja samo nakon uspešnog dodavanja.
const selectedPlayerId = defineModel('selectedPlayerId', { default: null })
</script>
