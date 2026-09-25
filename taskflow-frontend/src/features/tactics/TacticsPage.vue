<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <TacticsToolbar
            :teams="teams"
            v-model:selected-team-filter="selectedTeamFilter"
            v-model:selected-formation="selectedFormation"
            @team-filter-change="onTeamFilterChange"
            @apply-formation="applyFormation"
            @save="saveTactics"
        />

        <!-- Teren i Sastav Ekipe -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <TacticsPitch
                :field-spots="fieldSpots"
                @drop="onDropOnPitch"
                @drag-start="onDragStart"
                @spot-click="openPlayerPicker"
            />

            <TeamRosterPanel :players="teamPlayers" />
        </div>

        <PlayerPickerModal
            v-if="activeSpotForSelection"
            :spot="activeSpotForSelection"
            :filtered-users="filteredUsers"
            v-model:search-query="searchQuery"
            @close="closePlayerPicker"
            @assign="assignPlayerToSpot"
            @remove="removePlayerFromSpot"
        />
    </div>
</template>

<script setup>
import { useTacticsBoard } from './composables/useTacticsBoard'
import TacticsToolbar from './components/TacticsToolbar.vue'
import TacticsPitch from './components/TacticsPitch.vue'
import TeamRosterPanel from './components/TeamRosterPanel.vue'
import PlayerPickerModal from './components/PlayerPickerModal.vue'

const {
    selectedFormation,
    teams,
    teamPlayers,
    fieldSpots,
    activeSpotForSelection,
    selectedTeamFilter,
    searchQuery,
    filteredUsers,
    onTeamFilterChange,
    openPlayerPicker,
    closePlayerPicker,
    assignPlayerToSpot,
    removePlayerFromSpot,
    onDragStart,
    onDropOnPitch,
    applyFormation,
    saveTactics
} = useTacticsBoard()
</script>
