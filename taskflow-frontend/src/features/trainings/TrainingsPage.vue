<!-- Stranica trening sesija: pregled po mesecima, statistika, zakazivanje, evidencija prisustva i brisanje. -->
<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Gornja traka: Naslov, Mesec i Dodavanje -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <div>
                <h2 class="text-2xl font-black text-white tracking-tight">Trening Sesije</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pregled svih zakazanih treninga i evidencija prisustva</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <!-- Izbor meseca -->
                <div class="flex items-center bg-gray-900 rounded-xl p-1 border border-gray-700/80">
                    <button @click="changeMonth(-1)" class="p-2 text-gray-400 hover:text-white transition cursor-pointer">←</button>
                    <span class="px-3 text-xs font-bold text-white min-w-[120px] text-center capitalize">
            {{ currentMonthName }} {{ currentYear }}
          </span>
                    <button @click="changeMonth(1)" class="p-2 text-gray-400 hover:text-white transition cursor-pointer">→</button>
                </div>

                <button
                    @click="showCreateModal = true"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-1.5 border border-indigo-500/30 cursor-pointer"
                >
                    <span>➕</span> Zakaži Trening
                </button>
            </div>
        </div>

        <!-- Statistički pregled za izabrani mesec -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-800/60 border border-gray-700/60 p-4 rounded-2xl">
                <div class="text-xs font-bold text-gray-400 uppercase">Ukupno Treninga</div>
                <div class="text-2xl font-black text-white mt-1">{{ monthlySessions.length }}</div>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 p-4 rounded-2xl">
                <div class="text-xs font-bold text-gray-400 uppercase">Prosečan Odziv</div>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ averageMonthlyAttendance }}%</div>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 p-4 rounded-2xl">
                <div class="text-xs font-bold text-gray-400 uppercase">Naredni Trening</div>
                <div class="text-sm font-bold text-indigo-300 mt-1 truncate">
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
                @open-attendance="openAttendanceModal"
                @delete="handleDeleteSession"
            />
        </div>

        <!-- Prazno stanje -->
        <div v-else class="text-center py-12 bg-gray-800/40 border border-dashed border-gray-700/80 rounded-2xl space-y-3">
            <div class="text-3xl">⚽</div>
            <div class="text-sm font-bold text-gray-300">Nema zakazanih treninga za {{ currentMonthName }} {{ currentYear }}</div>
            <p class="text-xs text-gray-500">Kliknite na dugme "+ Zakaži Trening" za dodavanje novog treninga.</p>
        </div>

        <!-- MODAL 1: Zakazivanje Novog Treninga -->
        <CreateSessionModal
            v-if="showCreateModal"
            :form="newSession"
            :teams="teams"
            v-model:form-date="formDate"
            v-model:form-hours="formHours"
            v-model:form-minutes="formMinutes"
            @close="showCreateModal = false"
            @submit="handleCreateSession"
        />

        <!-- MODAL 2: Evidencija Prisustva -->
        <AttendanceModal
            v-if="showAttendanceModal && activeSession"
            :session="activeSession"
            :users="userStore.users"
            :selected-ids="selectedAttendeeIds"
            @close="showAttendanceModal = false"
            @toggle="togglePlayerAttendance"
            @select-all="selectAllPlayers"
            @clear-all="clearAllPlayers"
            @save="handleSaveAttendance"
        />

        <!-- MODAL 3: Potvrda Brisanja Treninga -->
        <DeleteSessionModal
            v-if="sessionToDelete"
            :session-title="sessionToDelete.title"
            :is-deleting="isDeleting"
            @close="sessionToDelete = null"
            @confirm="confirmDeleteSession"
        />
    </div>
</template>

<script setup>
import TrainingSessionRow from './components/TrainingSessionRow.vue'
import CreateSessionModal from './components/CreateSessionModal.vue'
import AttendanceModal from './components/AttendanceModal.vue'
import DeleteSessionModal from './components/DeleteSessionModal.vue'
import { useTrainingsPage } from './composables/useTrainingsPage'

const {
    userStore,
    teams,
    newSession,
    formDate,
    formHours,
    formMinutes,
    showCreateModal,
    currentYear,
    currentMonthName,
    monthlySessions,
    nextUpcomingSession,
    averageMonthlyAttendance,
    changeMonth,
    handleCreateSession,
    showAttendanceModal,
    activeSession,
    selectedAttendeeIds,
    openAttendanceModal,
    togglePlayerAttendance,
    selectAllPlayers,
    clearAllPlayers,
    handleSaveAttendance,
    sessionToDelete,
    isDeleting,
    handleDeleteSession,
    confirmDeleteSession
} = useTrainingsPage()
</script>
