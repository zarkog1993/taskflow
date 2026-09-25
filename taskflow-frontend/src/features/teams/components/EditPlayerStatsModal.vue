<template>
    <div class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-1">Ažuriraj Učinak i Status</h3>
            <p class="text-xs text-indigo-400 font-semibold mb-4">{{ player?.name }}</p>

            <form @submit.prevent="$emit('save')" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Senioritet</label>
                        <select v-model="form.seniority" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white text-xs outline-none">
                            <option value="Seniori">Seniori</option>
                            <option value="U19">U19 (Omladinci)</option>
                            <option value="U17">U17 (Kadeti)</option>
                            <option value="U15">U15 (Pioniri)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Jača Noga</label>
                        <select v-model="form.preferred_foot" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white text-xs outline-none">
                            <option value="right">Desna</option>
                            <option value="left">Leva</option>
                            <option value="both">Obe</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Odrađeni Treninzi</label>
                        <input
                            v-model.number="form.trainings_attended"
                            type="number"
                            min="0"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white outline-none focus:border-indigo-500 text-xs"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Odigrane Utakmice</label>
                        <input
                            v-model.number="form.matches_played"
                            type="number"
                            min="0"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white outline-none focus:border-indigo-500 text-xs"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Postignuti Golovi</label>
                        <input
                            v-model.number="form.goals"
                            type="number"
                            min="0"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white outline-none focus:border-indigo-500 text-xs"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Asistencije</label>
                        <input
                            v-model.number="form.assists"
                            type="number"
                            min="0"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white outline-none focus:border-indigo-500 text-xs"
                        />
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-xs text-gray-400 hover:text-white font-semibold">Odustani</button>
                    <button type="submit" class="px-5 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold transition shadow-lg cursor-pointer">Sačuvaj Učinak</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
defineProps({
    player: {
        type: Object,
        default: null
    },
    form: {
        type: Object,
        required: true
    }
})

defineEmits(['close', 'save'])
</script>
