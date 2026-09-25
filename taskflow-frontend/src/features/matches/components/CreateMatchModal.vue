<!-- Modal za zakazivanje nove utakmice. -->
<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-md">
        <form @submit.prevent="$emit('submit')" class="w-full max-w-lg space-y-4 rounded-2xl border border-gray-700 bg-gray-800 p-6">
            <div class="flex items-center justify-between border-b border-gray-700 pb-3">
                <h2 class="text-lg font-black text-white">Zakaži Utakmicu</h2>
                <button type="button" @click="$emit('close')" class="text-gray-400 hover:text-white">✕</button>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Ekipa</label>
                <select v-model="form.team_id" required class="w-full rounded-xl border border-gray-700 bg-gray-900 px-3 py-2.5 text-xs text-white">
                    <option disabled value="">Izaberite ekipu</option>
                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Protivnik</label>
                <input v-model="form.opponent" required class="w-full rounded-xl border border-gray-700 bg-gray-900 px-3 py-2.5 text-xs text-white" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Datum i vreme</label>
                    <input v-model="form.scheduled_at" type="datetime-local" required class="w-full rounded-xl border border-gray-700 bg-gray-900 px-3 py-2.5 text-xs text-white" />
                </div>
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase text-gray-400">Lokacija</label>
                    <input v-model="form.location" class="w-full rounded-xl border border-gray-700 bg-gray-900 px-3 py-2.5 text-xs text-white" />
                </div>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-300">
                <input v-model="form.is_home" type="checkbox" />
                Domaća utakmica
            </label>
            <div class="flex justify-end gap-3 border-t border-gray-700 pt-3">
                <button type="button" @click="$emit('close')" class="px-4 py-2 text-xs font-bold text-gray-400">Odustani</button>
                <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white">Sačuvaj</button>
            </div>
        </form>
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
</script>
