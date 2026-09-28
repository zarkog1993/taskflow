<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-gray-900 border border-gray-700/60 rounded-2xl w-full max-w-4xl p-6 shadow-2xl relative text-white space-y-6">
            
            <!-- Zaglavlje Modal-a -->
            <div class="flex justify-between items-start pb-4 border-b border-gray-800">
                <div>
                    <h2 class="text-xl font-bold text-emerald-400 flex items-center gap-2">
                        <span>💳</span> Finansijski Karton - {{ teamName }}
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">
                        Evidencija članarina (Akademija) i honorara (Prvotimci)
                    </p>
                </div>
                
                <button @click="$emit('close')" class="text-gray-400 hover:text-white p-2 rounded-xl hover:bg-gray-800 transition">
                    ✕
                </button>
            </div>

            <!-- Kontrole: Prekidač (Članarine / Isplate) + Mjesec -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-800/40 p-3 rounded-xl border border-gray-700/60">
                <div class="flex bg-gray-900 p-1 rounded-xl border border-gray-700 text-xs w-full sm:w-auto">
                    <button 
                        @click="activeType = 'membership'"
                        :class="activeType === 'membership' ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-400 hover:text-white'"
                        class="px-4 py-2 rounded-lg transition flex-1 sm:flex-none text-center"
                    >
                        Mesečne Članarine (Akademija)
                    </button>
                    <button 
                        @click="activeType = 'stipend'"
                        :class="activeType === 'stipend' ? 'bg-indigo-600 text-white font-semibold' : 'text-gray-400 hover:text-white'"
                        class="px-4 py-2 rounded-lg transition flex-1 sm:flex-none text-center"
                    >
                        Isplate Prvotimcima (Seniori)
                    </button>
                </div>

                <div class="flex items-center gap-2 text-xs w-full sm:w-auto justify-end">
                    <span class="text-gray-400 font-medium">Mesec:</span>
                    <input 
                        type="month" 
                        v-model="selectedPeriod"
                        class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-1.5 text-white focus:outline-none focus:border-indigo-500 font-mono"
                    />
                </div>
            </div>

            <!-- Loading -->
            <div v-if="loading" class="py-12 text-center text-gray-400 text-sm font-medium">
                Učitavanje finansijskih podataka...
            </div>

            <!-- Tabela sa igračima -->
            <div v-else-if="players && players.length > 0" class="overflow-x-auto rounded-xl border border-gray-800">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead class="bg-gray-800/80 text-xs uppercase text-gray-400 border-b border-gray-700">
                        <tr>
                            <th class="py-3 px-4">Igrač</th>
                            <th class="py-3 px-4">Dres / Pozicija</th>
                            <th class="py-3 px-4">Iznos</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Akcija</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800/60 bg-gray-900/40">
                        <tr v-for="player in players" :key="player.id" class="hover:bg-gray-800/40 transition">
                            <td class="py-3 px-4 font-semibold text-white">
                                {{ player.name }}
                            </td>
                            <td class="py-3 px-4 text-xs text-gray-400">
                                #{{ player.jersey_number || '-' }} | {{ player.position || 'Igrač' }}
                            </td>
                            <td class="py-3 px-4">
                                <input 
                                    type="number" 
                                    v-model.number="getPayment(player.id).amount"
                                    @change="savePayment(player.id)"
                                    class="w-28 bg-gray-950 border border-gray-700 rounded-lg px-2.5 py-1 text-sm font-semibold text-emerald-400 focus:outline-none focus:border-indigo-500"
                                />
                            </td>
                            <td class="py-3 px-4">
                                <span 
                                    :class="{
                                        'bg-emerald-500/10 text-emerald-400 border-emerald-500/30': getPayment(player.id).status === 'paid',
                                        'bg-amber-500/10 text-amber-400 border-amber-500/30': getPayment(player.id).status === 'pending',
                                        'bg-rose-500/10 text-rose-400 border-rose-500/30': getPayment(player.id).status === 'overdue'
                                    }"
                                    class="px-2.5 py-1 rounded-full text-xs font-bold border uppercase tracking-wider inline-block"
                                >
                                    {{ getPayment(player.id).status === 'paid' ? 'Plaćeno' : getPayment(player.id).status === 'pending' ? 'Na čekanju' : 'Kasni' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button 
                                        @click="updateStatus(player.id, 'paid')" 
                                        :class="getPayment(player.id).status === 'paid' ? 'bg-emerald-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-emerald-600/30'"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-700 transition"
                                    >
                                        ✓ Plaćeno
                                    </button>
                                    <button 
                                        @click="updateStatus(player.id, 'pending')" 
                                        :class="getPayment(player.id).status === 'pending' ? 'bg-amber-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-amber-600/30'"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-700 transition"
                                    >
                                        ⏳ Čeka
                                    </button>
                                    <button 
                                        @click="updateStatus(player.id, 'overdue')" 
                                        :class="getPayment(player.id).status === 'overdue' ? 'bg-rose-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-rose-600/30'"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-700 transition"
                                    >
                                        ✕ Kasni
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="py-12 text-center text-gray-400 text-sm bg-gray-950/30 rounded-xl border border-gray-800">
                Nema igrača u ovoj ekipi.
            </div>

            <!-- Podnožje -->
            <div class="pt-4 border-t border-gray-800 flex justify-between items-center text-xs text-gray-400">
                <span>Ukupno igrača: <strong class="text-white">{{ players ? players.length : 0 }}</strong></span>
                <button @click="$emit('close')" class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-xl transition">
                    Zatvori
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useTeamFinances } from '../composables/useTeamFinances'

const props = defineProps({
    show: Boolean,
    teamId: Number,
    teamName: String,
    players: Array
})

defineEmits(['close'])

const teamIdRef = computed(() => props.teamId)

const {
    activeType,
    selectedPeriod,
    loading,
    getPayment,
    updateStatus,
    savePayment
} = useTeamFinances(teamIdRef)
</script>