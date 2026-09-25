<template>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Taktička Tabla</h2>
            <p class="text-xs text-gray-400 mt-0.5">Klikni na poziciju da dodijeliš igrača ili ih prevlači po terenu</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 bg-gray-900 border border-gray-700 rounded-xl px-3 py-1.5">
                <span class="text-xs font-bold text-gray-400 uppercase">EKIPA:</span>
                <select v-model="selectedTeamFilter" @change="$emit('team-filter-change')" class="bg-transparent text-white text-xs font-bold outline-none cursor-pointer">
                    <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                </select>
            </div>

            <select v-model="selectedFormation" @change="$emit('apply-formation')" class="bg-gray-900 border border-gray-700 text-white text-xs font-bold rounded-xl px-3 py-2.5 outline-none cursor-pointer">
                <option value="4-3-3">Formacija 4-3-3</option>
                <option value="4-4-2">Formacija 4-4-2</option>
                <option value="4-2-3-1">Formacija 4-2-3-1</option>
                <option value="3-5-2">Formacija 3-5-2</option>
            </select>

            <button @click="$emit('save')" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer">
                <span>💾</span> Sačuvaj Taktiku
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    teams: {
        type: Array,
        default: () => []
    }
})

defineEmits(['team-filter-change', 'apply-formation', 'save'])

const selectedTeamFilter = defineModel('selectedTeamFilter')
const selectedFormation = defineModel('selectedFormation')
</script>
