<template>
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-gray-100/80 dark:bg-gray-800/80 p-4 sm:p-5 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xl">
        <div class="min-w-0">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Taktička Tabla</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Klikni na poziciju da dodijeliš igrača ili ih prevlači po terenu</p>
        </div>

        <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:flex-wrap sm:items-center sm:justify-end sm:gap-3">
            <div class="col-span-2 flex min-w-0 items-center gap-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-1.5 sm:col-span-1">
                <span class="shrink-0 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">EKIPA:</span>
                <AppSelect v-model="selectedTeamFilter" :disabled="isLoading || isSaving" @change="$emit('team-filter-change')" class="min-w-0 flex-1 bg-transparent text-gray-900 dark:text-white text-xs font-bold outline-none cursor-pointer">
                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                </AppSelect>
            </div>

            <AppSelect v-model="selectedFormation" :disabled="isLoading || isSaving" @change="$emit('apply-formation')" class="min-w-0 w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-xs font-bold rounded-xl px-3 py-2.5 outline-none cursor-pointer sm:w-auto">
                <option value="4-3-3">Formacija 4-3-3</option>
                <option value="4-4-2">Formacija 4-4-2</option>
                <option value="4-2-3-1">Formacija 4-2-3-1</option>
                <option value="3-5-2">Formacija 3-5-2</option>
            </AppSelect>

            <button
                @click="$emit('save')"
                :disabled="isLoading || isSaving || !selectedTeamFilter"
                class="col-span-2 w-full justify-center bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold px-5 py-3 sm:py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer sm:col-span-1 sm:w-auto"
            >
                <span>💾</span> {{ isSaving ? 'Čuvanje...' : 'Sačuvaj Taktiku' }}
            </button>
        </div>
        <p v-if="errorMessage || saveMessage" role="status" aria-live="polite" class="w-full text-xs font-semibold" :class="errorMessage ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-700 dark:text-emerald-400'">
            {{ errorMessage || saveMessage }}
        </p>
        <p v-else-if="!teams.length && !isLoading" role="status" class="w-full text-xs text-gray-600 dark:text-gray-400">
            Nema dostupnih ekipa za prikaz taktike.
        </p>
    </div>
</template>

<script setup>
defineProps({
    teams: {
        type: Array,
        default: () => []
    },
    isLoading: Boolean,
    isSaving: Boolean,
    errorMessage: String,
    saveMessage: String
})

defineEmits(['team-filter-change', 'apply-formation', 'save'])

const selectedTeamFilter = defineModel('selectedTeamFilter')
const selectedFormation = defineModel('selectedFormation')
</script>
