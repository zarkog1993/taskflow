<!-- Modal za zakazivanje novog treninga. -->
<template>
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div class="max-h-[calc(100dvh-2rem)] w-full max-w-xl overflow-y-auto rounded-2xl bg-gray-100 dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 shadow-2xl">
            <div class="p-6 bg-white dark:bg-gray-900 border-b border-gray-200/70 dark:border-gray-700/70 relative">
                <button @click="$emit('close')" class="absolute top-5 right-5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">✕</button>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white">{{ isEditing ? 'Izmeni Trening' : 'Zakaži Novi Trening' }}</h3>
            </div>

            <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
                <div v-if="!isEditing">
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">Ekipa</label>
                    <AppSelect v-model="form.team_id" required class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2.5 text-xs text-gray-900 dark:text-white">
                        <option disabled value="">Izaberite ekipu</option>
                        <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </AppSelect>
                </div>
                <div v-else>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">Ekipa</label>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-xs text-gray-900 dark:text-white">{{ session?.team?.name || 'Ekipa' }}</div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">Naslov / Tema</label>
                    <input v-model="form.title" type="text" required placeholder="npr. Taktička priprema za meč" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-white" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">Tip Događaja</label>
                        <AppSelect v-model="form.type" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2.5 text-xs text-gray-900 dark:text-white">
                            <option value="training">🏃‍♂️ Trening</option>
                            <option value="match">⚽ Utakmica</option>
                        </AppSelect>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">Lokacija</label>
                        <input v-model="form.location" type="text" placeholder="Glavni Teren A" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-xs text-gray-900 dark:text-white" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">Datum i Vreme</label>
                    <div class="grid grid-cols-3 gap-2">
                        <CustomDatePicker v-model="formDate" />
                        <AppSelect v-model="formHours" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-2 py-2 text-xs text-gray-900 dark:text-white">
                            <option v-for="h in 24" :key="h-1" :value="String(h-1).padStart(2,'0')">{{ String(h-1).padStart(2,'0') }}h</option>
                        </AppSelect>
                        <AppSelect v-model="formMinutes" class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-2 py-2 text-xs text-gray-900 dark:text-white">
                            <option value="00">00 min</option>
                            <option value="15">15 min</option>
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                        </AppSelect>
                    </div>
                    <p v-if="dateMissing && !formDate" class="mt-1 text-xs text-rose-600 dark:text-rose-400">Izaberite datum.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-600 dark:text-gray-400 mb-1">Opis / Napomena</label>
                    <textarea v-model="form.description" rows="3" placeholder="Uputstva za igrače..." class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-3 text-xs text-gray-900 dark:text-white resize-none"></textarea>
                </div>

                <section v-if="isEditing" class="space-y-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div>
                        <h4 class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300">Zapažanja po igraču</h4>
                        <p class="mt-1 text-[11px] text-gray-600 dark:text-gray-400">Beleške su dostupne samo za igrače koji su potvrdili dolazak.</p>
                    </div>
                    <div v-if="attendees.length" class="space-y-3">
                        <label v-for="attendee in attendees" :key="attendee.id" class="block">
                            <span class="mb-1 block text-xs font-semibold text-gray-900 dark:text-white">{{ attendee.name }}</span>
                            <textarea
                                v-model="form.player_observations[attendee.id]"
                                rows="2"
                                :placeholder="`Zapažanje za ${attendee.name}...`"
                                class="w-full resize-y rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3 text-xs text-gray-900 dark:text-white"
                            ></textarea>
                        </label>
                    </div>
                    <p v-else class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-4 text-center text-xs text-gray-600 dark:text-gray-400">Još nijedan igrač nije potvrdio dolazak na ovaj trening.</p>
                </section>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Odustani</button>
                    <button type="submit" :disabled="isSaving" class="px-5 py-2 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl disabled:opacity-50">{{ isSaving ? 'Čuvanje...' : isEditing ? 'Sačuvaj izmene' : 'Sačuvaj' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import CustomDatePicker from '../../../components/CustomDatePicker.vue'

defineProps({
    form: {
        type: Object,
        required: true
    },
    teams: {
        type: Array,
        default: () => []
    },
    isEditing: { type: Boolean, default: false },
    session: { type: Object, default: null },
    attendees: { type: Array, default: () => [] },
    isSaving: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'submit'])

// Datum/vreme se sastavljaju odvojeno (datum + sat + minuti) i spajaju u `scheduled_at` u roditeljskoj komponenti.
const formDate = defineModel('formDate', { default: '' })
const formHours = defineModel('formHours', { default: '18' })
const formMinutes = defineModel('formMinutes', { default: '00' })
const dateMissing = ref(false)

const handleSubmit = () => {
    dateMissing.value = !formDate.value
    if (!dateMissing.value) emit('submit')
}
</script>
