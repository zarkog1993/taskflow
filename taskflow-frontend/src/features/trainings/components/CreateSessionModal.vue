<!-- Modal za zakazivanje novog treninga. -->
<template>
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden">
            <div class="p-6 bg-gray-900 border-b border-gray-700/70 relative">
                <button @click="$emit('close')" class="absolute top-5 right-5 text-gray-400 hover:text-white">✕</button>
                <h3 class="text-2xl font-black text-white">Zakaži Novi Trening</h3>
            </div>

            <form @submit.prevent="$emit('submit')" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Ekipa</label>
                    <select v-model="form.team_id" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white">
                        <option disabled value="">Izaberite ekipu</option>
                        <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Naslov / Tema</label>
                    <input v-model="form.title" type="text" required placeholder="npr. Taktička priprema za meč" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Tip Događaja</label>
                        <select v-model="form.type" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white">
                            <option value="training">🏃‍♂️ Trening</option>
                            <option value="match">⚽ Utakmica</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Lokacija</label>
                        <input v-model="form.location" type="text" placeholder="Glavni Teren A" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Datum i Vreme</label>
                    <div class="grid grid-cols-3 gap-2">
                        <input v-model="formDate" type="date" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white" />
                        <select v-model="formHours" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-2 py-2 text-xs text-white">
                            <option v-for="h in 24" :key="h-1" :value="String(h-1).padStart(2,'0')">{{ String(h-1).padStart(2,'0') }}h</option>
                        </select>
                        <select v-model="formMinutes" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-2 py-2 text-xs text-white">
                            <option value="00">00 min</option>
                            <option value="15">15 min</option>
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Opis / Napomena</label>
                    <textarea v-model="form.description" rows="3" placeholder="Uputstva za igrače..." class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-xs text-white resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Odustani</button>
                    <button type="submit" class="px-5 py-2 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl">Sačuvaj</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
defineProps({
    form: {
        type: Object,
        required: true
    },
    teams: {
        type: Array,
        default: () => []
    }
})

defineEmits(['close', 'submit'])

// Datum/vreme se sastavljaju odvojeno (datum + sat + minuti) i spajaju u `scheduled_at` u roditeljskoj komponenti.
const formDate = defineModel('formDate', { default: '' })
const formHours = defineModel('formHours', { default: '18' })
const formMinutes = defineModel('formMinutes', { default: '00' })
</script>
