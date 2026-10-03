<!-- Modal za zakazivanje nove utakmice. -->
<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-md">
        <form @submit.prevent="handleSubmit" class="w-full max-w-lg space-y-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 p-6">
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                <h2 class="text-lg font-black text-gray-900 dark:text-white">Zakaži Utakmicu</h2>
                <button type="button" @click="$emit('close')" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">✕</button>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold uppercase text-gray-600 dark:text-gray-400">Ekipa</label>
                <AppSelect v-model="form.team_id" required class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-xs text-gray-900 dark:text-white">
                    <option disabled value="">Izaberite ekipu</option>
                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                </AppSelect>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold uppercase text-gray-600 dark:text-gray-400">Protivnik</label>
                <input v-model="form.opponent" required class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-xs text-gray-900 dark:text-white" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase text-gray-600 dark:text-gray-400">Datum i vreme</label>
                    <div class="flex flex-col gap-2">
                        <CustomDatePicker v-model="matchDate" />
                        <AppSelect v-model="matchTime" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-xs text-gray-900 dark:text-white">
                            <option v-if="!timeSlots.includes(matchTime)" :value="matchTime">{{ matchTime }}</option>
                            <option v-for="time in timeSlots" :key="time" :value="time">{{ time }}</option>
                        </AppSelect>
                    </div>
                    <p v-if="dateMissing && !matchDate" class="mt-1 text-xs text-rose-600 dark:text-rose-400">Izaberite datum.</p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase text-gray-600 dark:text-gray-400">Lokacija</label>
                    <input v-model="form.location" class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-xs text-gray-900 dark:text-white" />
                </div>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300">
                <input v-model="form.is_home" type="checkbox" />
                Domaća utakmica
            </label>
            <div class="flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-3">
                <button type="button" @click="$emit('close')" class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-400">Odustani</button>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white">Sačuvaj</button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import CustomDatePicker from '../../../components/CustomDatePicker.vue'

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    teams: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['close', 'submit'])
const selectedTime = ref('18:00')
const dateMissing = ref(false)
const matchDate = computed({
    get: () => props.form.scheduled_at?.slice(0, 10) || '',
    set: (date) => { props.form.scheduled_at = date ? `${date}T${matchTime.value}` : '' }
})
const matchTime = computed({
    get: () => props.form.scheduled_at?.slice(11, 16) || selectedTime.value,
    set: (time) => {
        selectedTime.value = time
        if (matchDate.value) props.form.scheduled_at = `${matchDate.value}T${time}`
    }
})
const timeSlots = Array.from({ length: 96 }, (_, index) => {
    const hour = String(Math.floor(index / 4)).padStart(2, '0')
    const minute = String((index % 4) * 15).padStart(2, '0')
    return `${hour}:${minute}`
})

const handleSubmit = () => {
    dateMissing.value = !matchDate.value
    if (!dateMissing.value) emit('submit')
}
</script>
