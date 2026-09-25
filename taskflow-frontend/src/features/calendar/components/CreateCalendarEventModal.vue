<!-- Modal za zakazivanje novog događaja (trening ili utakmica) sa selekcijom igrača. -->
<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                <h3 class="text-sm font-bold text-white">Zakaži Novi Događaj</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-white">✕</button>
            </div>

            <form @submit.prevent="$emit('submit')" class="space-y-4">
                <!-- Tip Događaja -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Tip Događaja</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            @click="form.eventType = 'training'"
                            :class="form.eventType === 'training' ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-gray-900 text-gray-400 border-gray-700'"
                            class="py-2 text-xs font-bold rounded-xl border transition cursor-pointer"
                        >
                            ⚽ Trening
                        </button>
                        <button
                            type="button"
                            @click="form.eventType = 'match'"
                            :class="form.eventType === 'match' ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-gray-900 text-gray-400 border-gray-700'"
                            class="py-2 text-xs font-bold rounded-xl border transition cursor-pointer"
                        >
                            🏆 Utakmica
                        </button>
                    </div>
                </div>

                <!-- Ekipa -->
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Ekipa</label>
                    <select
                        v-model="form.team_id"
                        @change="$emit('team-change')"
                        required
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer"
                    >
                        <option value="" disabled>Izaberite ekipu...</option>
                        <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </select>
                </div>

                <!-- Polja za Utakmicu -->
                <template v-if="form.eventType === 'match'">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Protivnik</label>
                        <input
                            v-model="form.opponent"
                            type="text"
                            placeholder="npr. FK Napredak"
                            required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Teren</label>
                        <select
                            v-model="form.is_home"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                        >
                            <option :value="true">Domaćin</option>
                            <option :value="false">Gost</option>
                        </select>
                    </div>
                </template>

                <!-- Polja za Trening -->
                <template v-else>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Naziv / Trenažni Cilj</label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="npr. Taktička priprema i šut"
                            required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Datum i Vreme</label>
                    <input
                        v-model="form.scheduled_at"
                        type="datetime-local"
                        required
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Lokacija</label>
                    <input
                        v-model="form.location"
                        type="text"
                        placeholder="npr. Glavni teren"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                    />
                </div>

                <!-- Lista Igrača za Sastav -->
                <PlayerSelector
                    :players="players"
                    :selected-count="selectedPlayersCount"
                    :all-selected="allSelected"
                    :team-selected="form.team_id"
                    @toggle-select-all="$emit('toggle-select-all')"
                />

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">
                        Odustani
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                        Sačuvaj
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import PlayerSelector from './PlayerSelector.vue'

defineProps({
    form: {
        type: Object,
        required: true
    },
    teams: {
        type: Array,
        default: () => []
    },
    players: {
        type: Array,
        default: () => []
    },
    selectedPlayersCount: {
        type: Number,
        required: true
    },
    allSelected: {
        type: Boolean,
        required: true
    }
})

defineEmits(['close', 'submit', 'team-change', 'toggle-select-all'])
</script>
