<!-- Stranica kalendara: mesečni pregled treninga i utakmica, zakazivanje događaja i pregled odziva. -->
<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <CalendarToolbar
            :current-month-name="currentMonthName"
            :current-year="currentYear"
            @create="openCreateModal"
            @prev="prevMonth"
            @next="nextMonth"
        />

        <CalendarGrid :days="calendarDays" @open-event="openEventDetails" />

        <CreateCalendarEventModal
            v-if="showCreateModal"
            :form="eventForm"
            :teams="teams"
            :players="availablePlayers"
            :selected-players-count="selectedPlayersCount"
            :all-selected="allSelected"
            @close="showCreateModal = false"
            @submit="handleCreateEvent"
            @team-change="onTeamChange"
            @toggle-select-all="toggleSelectAll"
        />

        <EventDetailsModal
            v-if="selectedEvent"
            :event="selectedEvent"
            @close="selectedEvent = null"
        />
    </div>
</template>

<script setup>
import CalendarToolbar from './components/CalendarToolbar.vue'
import CalendarGrid from './components/CalendarGrid.vue'
import CreateCalendarEventModal from './components/CreateCalendarEventModal.vue'
import EventDetailsModal from './components/EventDetailsModal.vue'
import { useCalendarPage } from './composables/useCalendarPage'

const {
    currentYear,
    currentMonthName,
    prevMonth,
    nextMonth,
    calendarDays,
    teams,
    availablePlayers,
    showCreateModal,
    selectedEvent,
    eventForm,
    onTeamChange,
    openCreateModal,
    selectedPlayersCount,
    allSelected,
    toggleSelectAll,
    handleCreateEvent,
    openEventDetails
} = useCalendarPage()
</script>
