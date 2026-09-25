<!-- Stranica utakmica: tabovi predstojeće/odigrane, kartice mečeva, zakazivanje, zapisnik i brisanje. -->
<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Zaglavlje -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-white">Utakmice & Zapisnici</h1>
                <p class="text-xs text-gray-400">Pregled zakazanih mečeva, sastava i statistike igrača</p>
            </div>
            <button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-lg cursor-pointer">
                + Zakaži Utakmicu
            </button>
        </div>

        <!-- Tabovi -->
        <MatchTabs v-model:active-tab="activeTab" :upcoming-count="upcomingMatches.length" :completed-count="completedMatches.length" />

        <!-- Kartice -->
        <div v-if="filteredMatches.length" class="space-y-4">
            <MatchCard
                v-for="match in filteredMatches"
                :key="match.id"
                :match="match"
                @open-stats="openStatsModal"
                @delete="handleDeleteMatch"
            />
        </div>
        <div v-else class="text-center py-12 bg-gray-800/40 border border-gray-700/50 rounded-2xl">
            <p class="text-sm text-gray-400 italic">Nema utakmica u ovoj kategoriji.</p>
        </div>

        <!-- Modali -->
        <CreateMatchModal
            v-if="showCreateModal"
            :form="newMatch"
            :teams="teams"
            @close="showCreateModal = false"
            @submit="createMatch"
        />
        <MatchStatsModal v-if="selectedMatch" :match="selectedMatch" :stats-form="statsForm" :modal-tab="modalTab" @close="selectedMatch = null" @save="saveMatchStats" />
        <DeleteMatchModal
            v-if="matchToDelete"
            :match-title="matchToDelete.opponent || 'Utakmica'"
            :is-deleting="isDeleting"
            @close="matchToDelete = null"
            @confirm="confirmDeleteMatch"
        />
    </div>
</template>

<script setup>
import MatchCard from '../../components/matches/MatchCard.vue'
import MatchStatsModal from '../../components/matches/MatchStatsModal.vue'
import DeleteMatchModal from '../../components/matches/DeleteMatchModal.vue'
import MatchTabs from './components/MatchTabs.vue'
import CreateMatchModal from './components/CreateMatchModal.vue'
import { useMatchesPage } from './composables/useMatchesPage'

const {
    teams,
    activeTab,
    showCreateModal,
    newMatch,
    openCreateModal,
    createMatch,
    selectedMatch,
    modalTab,
    statsForm,
    openStatsModal,
    saveMatchStats,
    upcomingMatches,
    completedMatches,
    filteredMatches,
    matchToDelete,
    isDeleting,
    handleDeleteMatch,
    confirmDeleteMatch
} = useMatchesPage()
</script>
