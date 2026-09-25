// Composable koji objedinjuje mesečnu navigaciju kalendara i upravljanje događajima
// za stranicu kalendara (fetch pri montiranju, kreiranje događaja, detalji/odziv).
import { onMounted } from 'vue'
import { useCalendarMonth } from './useCalendarMonth'
import { useCalendarEvents } from './useCalendarEvents'

export function useCalendarPage() {
    const {
        events,
        teams,
        availablePlayers,
        showCreateModal,
        selectedEvent,
        eventForm,
        fetchAllData,
        onTeamChange,
        openCreateModal,
        selectedPlayersCount,
        allSelected,
        toggleSelectAll,
        handleCreateEvent,
        openEventDetails
    } = useCalendarEvents()

    const { currentYear, currentMonthName, prevMonth, nextMonth, calendarDays } =
        useCalendarMonth(events)

    onMounted(() => {
        fetchAllData()
    })

    return {
        // mesečna navigacija
        currentYear,
        currentMonthName,
        prevMonth,
        nextMonth,
        calendarDays,
        // događaji, ekipe, igrači
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
    }
}
