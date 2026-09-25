<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
        <!-- Navigacija i Zaglavlje Ekipe -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/60 p-6 rounded-2xl border border-gray-700/60 backdrop-blur-md shadow-xl">
            <div class="flex items-center space-x-4">
                <router-link
                    to="/teams"
                    class="p-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white rounded-xl transition text-xs font-semibold flex items-center gap-2 border border-gray-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Nazad na Ekipe
                </router-link>

                <div v-if="team">
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-black text-white tracking-tight">{{ team.name }}</h2>
                        <span class="text-xs font-mono font-bold uppercase text-indigo-400 bg-indigo-600/20 px-3 py-1 rounded-lg border border-indigo-500/30">
                          {{ team.category || team.age_group || 'Seniori' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Ukupno {{ teamPlayers.length }} igrača u sastavu</p>
                </div>
            </div>
        </div>

        <!-- Status Učitavanja -->
        <div v-if="loading" class="text-center py-20 text-gray-400 font-medium flex justify-center items-center gap-3">
            <svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Učitavanje igrača ekipe...
        </div>

        <!-- Grid Kartica Igrača -->
        <div v-else-if="team" class="space-y-6">
            <div v-if="teamPlayers.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <PlayerStatCard
                    v-for="player in teamPlayers"
                    :key="player.id"
                    :player="player"
                    :get-stat="getStat"
                    @edit="openEditModal"
                />
            </div>

            <!-- Prazan Sastav -->
            <div v-else class="text-center py-16 bg-gray-800/40 rounded-2xl border border-dashed border-gray-700/60">
                <p class="text-sm text-gray-400 italic">Ova ekipa trenutno nema dodeljenih igrača.</p>
            </div>
        </div>

        <!-- Modal za Izmenu Učinka i Statusa Igrača -->
        <EditPlayerStatsModal
            v-if="showEditModal"
            :player="selectedPlayer"
            :form="statsForm"
            @close="showEditModal = false"
            @save="handleSaveStats"
        />
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import PlayerStatCard from './components/PlayerStatCard.vue'
import EditPlayerStatsModal from './components/EditPlayerStatsModal.vue'
import { useTeamDetail } from './composables/useTeamDetail'

const route = useRoute()

const {
    team,
    loading,
    showEditModal,
    selectedPlayer,
    statsForm,
    teamPlayers,
    getStat,
    loadTeamData,
    openEditModal,
    handleSaveStats
} = useTeamDetail(route.params.id)

onMounted(() => {
    loadTeamData()
})
</script>
