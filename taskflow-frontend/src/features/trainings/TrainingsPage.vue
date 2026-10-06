<!-- Stranica trening sesija: pregled po mesecima, statistika, zakazivanje, evidencija prisustva i brisanje. -->
<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Gornja traka: Naslov, Mesec i Dodavanje -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-100/80 dark:bg-gray-800/80 p-5 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 shadow-xl">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Trening Sesije</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Pregled svih zakazanih treninga i evidencija prisustva</p>
            </div>

            <div class="grid w-full grid-cols-2 items-center gap-2 sm:flex sm:w-auto sm:justify-end sm:gap-3">
                <button
                    @click="trainingStore.fetchSessions()"
                    :disabled="trainingStore.loading"
                    title="Osveži odgovore na pozivnice"
                    class="min-h-[44px] min-w-[44px] bg-white dark:bg-gray-900 hover:bg-gray-200 dark:hover:bg-gray-700 border border-gray-200/80 dark:border-gray-700/80 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-xl transition text-xs cursor-pointer disabled:opacity-50 sm:min-h-0 sm:min-w-0 sm:p-2.5"
                >
                    {{ trainingStore.loading ? '⏳' : '↻' }}
                </button>

                <!-- Izbor meseca -->
                <div class="min-w-0 flex items-center justify-between bg-white dark:bg-gray-900 rounded-xl p-1 border border-gray-200/80 dark:border-gray-700/80">
                    <button @click="changeMonth(-1)" class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition cursor-pointer">←</button>
                    <span class="min-w-0 flex-1 px-1 sm:px-3 text-[11px] sm:text-xs font-bold text-gray-900 dark:text-white text-center capitalize">
            {{ currentMonthName }} {{ currentYear }}
          </span>
                    <button @click="changeMonth(1)" class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition cursor-pointer">→</button>
                </div>

                <button
                    @click="handleOpenCreateModal"
                    class="col-span-2 w-full justify-center bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-3 sm:py-2.5 rounded-xl shadow-lg transition flex items-center gap-1.5 border border-indigo-500/30 cursor-pointer sm:col-span-1 sm:w-auto"
                >
                    <span>➕</span> Zakaži Trening
                </button>
            </div>
        </div>

        <!-- Statistički pregled za izabrani mesec -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-100/60 dark:bg-gray-800/60 border border-gray-200/60 dark:border-gray-700/60 p-4 rounded-2xl">
                <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Ukupno Treninga</div>
                <div class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ monthlySessions.length }}</div>
            </div>
            <button
                type="button"
                @click="showAttendanceOverview = true"
                aria-label="Prikaži odziv igrača na treninge ovog meseca"
                class="bg-gray-100/60 dark:bg-gray-800/60 border border-gray-200/60 dark:border-gray-700/60 hover:border-emerald-500/50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-emerald-600 p-4 rounded-2xl text-left transition cursor-pointer"
            >
                <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Prosečan Odziv</div>
                <div class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1">{{ averageMonthlyAttendance }}%</div>
            </button>
            <div class="bg-gray-100/60 dark:bg-gray-800/60 border border-gray-200/60 dark:border-gray-700/60 p-4 rounded-2xl">
                <div class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">Naredni Trening</div>
                <div class="text-sm font-bold text-indigo-700 dark:text-indigo-300 mt-1 truncate">
                    {{ nextUpcomingSession ? nextUpcomingSession.title : 'Nema zakazanih treninga' }}
                </div>
            </div>
        </div>

        <!-- Lista Treninga po Danima -->
        <div v-if="monthlySessions.length > 0" class="space-y-3">
            <TrainingSessionRow
                v-for="session in monthlySessions"
                :key="session.id"
                :session="session"
                :can-edit="canManageSessions"
                @delete="handleDeleteSession"
                @edit="handleEditSession"
            />
        </div>

        <!-- Prazno stanje -->
        <div v-else class="text-center py-12 bg-gray-100/40 dark:bg-gray-800/40 border border-dashed border-gray-200/80 dark:border-gray-700/80 rounded-2xl space-y-3">
            <div class="text-3xl">⚽</div>
            <div class="text-sm font-bold text-gray-700 dark:text-gray-300">Nema zakazanih treninga za {{ currentMonthName }} {{ currentYear }}</div>
            <p class="text-xs text-gray-500">Kliknite na dugme "+ Zakaži Trening" za dodavanje novog treninga.</p>
        </div>

        <!-- MODAL 1: Zakazivanje Novog Treninga -->
        <CreateSessionModal
            v-if="showCreateModal || sessionToEdit"
            :form="sessionToEdit ? editSessionForm : newSession"
            :teams="teams"
            :session="sessionToEdit"
            :attendees="editAttendees"
            :is-editing="Boolean(sessionToEdit)"
            :is-saving="isSavingSession"
            v-model:form-date="formDate"
            v-model:form-hours="formHours"
            v-model:form-minutes="formMinutes"
            @close="showCreateModal = false; sessionToEdit = null"
            @submit="sessionToEdit ? handleUpdateSession() : handleCreateSession()"
        />

        <!-- MODAL 2: Potvrda Brisanja Treninga -->
        <DeleteSessionModal
            v-if="sessionToDelete"
            :session-title="sessionToDelete.title"
            :is-deleting="isDeleting"
            @close="sessionToDelete = null"
            @confirm="confirmDeleteSession"
        />

        <AttendanceOverviewModal
            v-if="showAttendanceOverview"
            :sessions="monthlySessions"
            :month-name="currentMonthName"
            :year="currentYear"
            @close="showAttendanceOverview = false"
        />
    </div>
</template>

<script setup>
import TrainingSessionRow from './components/TrainingSessionRow.vue'
import CreateSessionModal from './components/CreateSessionModal.vue'
import DeleteSessionModal from './components/DeleteSessionModal.vue'
import AttendanceOverviewModal from './components/AttendanceOverviewModal.vue'
import { useTrainingsPage } from './composables/useTrainingsPage'

const {
    trainingStore,
    teams,
    newSession,
    formDate,
    formHours,
    formMinutes,
    showCreateModal,
    canManageSessions,
    sessionToEdit,
    editSessionForm,
    editAttendees,
    isSavingSession,
    currentYear,
    currentMonthName,
    monthlySessions,
    nextUpcomingSession,
    averageMonthlyAttendance,
    showAttendanceOverview,
    changeMonth,
    handleOpenCreateModal,
    handleCreateSession,
    handleEditSession,
    handleUpdateSession,
    sessionToDelete,
    isDeleting,
    handleDeleteSession,
    confirmDeleteSession
} = useTrainingsPage()
</script>
