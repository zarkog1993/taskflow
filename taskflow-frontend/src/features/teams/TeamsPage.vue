<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- ZAGLAVLJE I DUGME -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Upravljanje Ekipama</h1>
                <p class="text-xs text-gray-400 mt-0.5">Pregled svih timova, starosnih kategorija i sastava ekipa</p>
            </div>

            <button
                @click="showCreateTeamModal = true"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 border border-indigo-500/30 cursor-pointer"
            >
                <span>➕</span> NOVA EKIPA
            </button>
        </div>

        <!-- STATISTIČKE KARTICE NA VRHU -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Ukupno Ekipa -->
            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">UKUPNO EKIPA</span>
                    <div class="text-2xl font-black text-white mt-1 font-mono">{{ teams.length }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-950 border border-indigo-800 text-indigo-400 flex items-center justify-center text-lg">🛡️</div>
            </div>

            <!-- Registrovani Igrači -->
            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">REGISTROVANI IGRAČI</span>
                    <div class="text-2xl font-black text-emerald-400 mt-1 font-mono">{{ totalPlayersCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-950 border border-emerald-800 text-emerald-400 flex items-center justify-center text-lg">🏃</div>
            </div>

            <!-- Slobodni Igrači -->
            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">SLOBODNI IGRAČI</span>
                    <div class="text-2xl font-black text-amber-400 mt-1 font-mono">{{ freePlayersCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-950 border border-amber-800 text-amber-400 flex items-center justify-center text-lg">📋</div>
            </div>
        </div>

        <!-- KARTICE EKIPA -->
        <div v-if="teams.length" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <TeamCard
                v-for="team in teams"
                :key="team.id"
                :team="team"
                :players="getTeamPlayers(team)"
                @manage="openManageModal"
            />
        </div>

        <div v-else class="text-center py-16 bg-gray-800/40 border border-gray-700/50 rounded-2xl">
            <p class="text-sm text-gray-400 italic">Nema definisanih ekipa. Kliknite na "NOVA EKIPA" da biste je kreirali.</p>
        </div>

        <!-- MODAL 1: Nova Ekipa -->
        <CreateTeamModal
            v-if="showCreateTeamModal"
            :form="newTeam"
            :is-submitting="isSubmitting"
            @close="showCreateTeamModal = false"
            @submit="handleCreateTeam"
        />

        <!-- MODAL 2: Upravljanje Sastavom Ekipe -->
        <ManageRosterModal
            v-if="selectedTeamForManage"
            v-model:selected-player-id="selectedPlayerToAssign"
            :team="selectedTeamForManage"
            :team-players="getTeamPlayers(selectedTeamForManage)"
            :unassigned-players="unassignedPlayers"
            @close="selectedTeamForManage = null"
            @assign="handleAssignPlayer"
            @remove="removePlayerFromTeam"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CreateTeamModal from './components/CreateTeamModal.vue'
import ManageRosterModal from './components/ManageRosterModal.vue'
import TeamCard from './components/TeamCard.vue'
import { useTeamsRoster } from './composables/useTeamsRoster'

const {
    teams,
    isSubmitting,
    newTeam,
    fetchData,
    getTeamPlayers,
    unassignedPlayers,
    totalPlayersCount,
    freePlayersCount,
    handleCreateTeam: createTeam,
    assignPlayerToTeam,
    removePlayerFromTeam
} = useTeamsRoster()

const showCreateTeamModal = ref(false)
const selectedTeamForManage = ref(null)
const selectedPlayerToAssign = ref(null)

onMounted(fetchData)

const handleCreateTeam = () => {
    return createTeam({
        onSuccess: () => { showCreateTeamModal.value = false }
    })
}

// Otvaranje modala za upravljanje sastavom
const openManageModal = (team) => {
    selectedTeamForManage.value = team
    selectedPlayerToAssign.value = null
}

const handleAssignPlayer = () => {
    if (!selectedPlayerToAssign.value || !selectedTeamForManage.value) return
    return assignPlayerToTeam(selectedPlayerToAssign.value, selectedTeamForManage.value.id, {
        onSuccess: () => { selectedPlayerToAssign.value = null }
    })
}
</script>
