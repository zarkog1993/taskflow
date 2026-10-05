<!-- Stranica utakmica: tabovi po statusu, kartice mečeva, zakazivanje, zapisnik i brisanje. -->
<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Zaglavlje -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">Utakmice & Zapisnici</h1>
                <p class="text-xs text-gray-600 dark:text-gray-400">Pregled zakazanih mečeva, sastava i statistike igrača</p>
            </div>
            <button @click="openCreateModal" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-3 sm:py-2.5 rounded-xl transition shadow-lg cursor-pointer">
                + Zakaži Utakmicu
            </button>
        </div>

        <!-- Tabovi + filter po mesecu -->
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <MatchTabs
                v-model:active-tab="activeTab"
                :upcoming-count="upcomingMatches.length"
                :completed-count="completedMatches.length"
                :canceled-count="canceledMatches.length"
            />

            <div class="flex w-full items-center justify-between gap-2 pb-2 sm:w-auto sm:justify-start">
                <label class="text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400 tracking-wider">Mesec</label>
                <AppSelect
                    v-model="selectedMonth"
                    class="min-h-[44px] w-44 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white outline-none focus:border-indigo-500 cursor-pointer"
                >
                    <option value="all">Svi meseci</option>
                    <option v-for="option in monthOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </AppSelect>
            </div>
        </div>

        <!-- Kartice -->
        <div v-if="filteredMatches.length" class="space-y-4">
            <MatchCard
                v-for="match in filteredMatches"
                :key="match.id"
                :match="match"
                @delete="handleDeleteMatch"
            />
        </div>
        <div v-else class="text-center py-12 bg-gray-100/40 dark:bg-gray-800/40 border border-gray-200/50 dark:border-gray-700/50 rounded-2xl space-y-2">
            <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                <template v-if="selectedMonth !== 'all'">
                    {{ emptyStateLabel }} za {{ selectedMonthLabel }}.
                </template>
                <template v-else>
                    {{ emptyStateLabel }}.
                </template>
            </p>
            <button
                v-if="selectedMonth !== 'all'"
                type="button"
                @click="resetMonthFilter"
                class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer"
            >
                Prikaži sve mesece
            </button>
        </div>

        <!-- Modali -->
        <CreateMatchModal
            v-if="showCreateModal"
            :form="newMatch"
            :teams="teams"
            @close="showCreateModal = false"
            @submit="createMatch"
        />
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
import DeleteMatchModal from '../../components/matches/DeleteMatchModal.vue'
import MatchTabs from './components/MatchTabs.vue'
import CreateMatchModal from './components/CreateMatchModal.vue'
import { useMatchesPage } from './composables/useMatchesPage'
import { computed } from 'vue'

const {
    teams,
    activeTab,
    showCreateModal,
    newMatch,
    openCreateModal,
    createMatch,
    upcomingMatches,
    completedMatches,
    canceledMatches,
    filteredMatches,
    selectedMonth,
    monthOptions,
    selectedMonthLabel,
    resetMonthFilter,
    matchToDelete,
    isDeleting,
    handleDeleteMatch,
    confirmDeleteMatch
} = useMatchesPage()

const emptyStateLabel = computed(() => ({
    upcoming: 'Nema predstojećih utakmica',
    completed: 'Nema odigranih utakmica',
    canceled: 'Nema otkazanih utakmica'
}[activeTab.value]))
</script>
