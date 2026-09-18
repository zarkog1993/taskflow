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
            <div
                v-for="session in monthlySessions"
                :key="session.id"
                class="bg-gray-800/90 border border-gray-700/80 hover:border-indigo-500/50 rounded-2xl p-4 transition shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
                <!-- Datum i Vreme -->
                <div class="flex items-center gap-4 min-w-[200px]">
                    <div class="bg-indigo-950/80 border border-indigo-800/80 rounded-xl px-3 py-2 text-center min-w-[65px]">
                        <div class="text-[10px] font-bold text-indigo-400 uppercase">{{ getDayName(session.scheduled_at) }}</div>
                        <div class="text-lg font-black text-white">{{ getDayNumber(session.scheduled_at) }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-mono font-bold text-indigo-300">⏰ {{ formatTime(session.scheduled_at) }}</div>
                        <div class="text-[11px] text-gray-400">📍 {{ session.location || 'Glavni Teren' }}</div>
                    </div>
                </div>

                <!-- Naziv i Opis -->
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
            <span
                :class="[
                'text-[10px] font-bold uppercase px-2 py-0.5 rounded-md border',
                session.type === 'match' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30'
              ]"
            >
              {{ session.type === 'match' ? '⚽ Utakmica' : '🏃‍♂️ Trening' }}
            </span>
                        <h3 class="text-base font-bold text-white">{{ session.title }}</h3>
                    </div>
                    <p class="text-xs text-gray-400 line-clamp-1">
                        {{ session.description || 'Nema unetog opisa za ovaj trening.' }}
                    </p>
                </div>

                <!-- Prisustvo i Dugme -->
                <div class="flex items-center justify-between md:justify-end gap-4 border-t md:border-t-0 border-gray-700/60 pt-3 md:pt-0">
                    <div class="text-left md:text-right">
                        <div class="text-[10px] font-bold uppercase text-gray-400">Prisustvo</div>
                        <div class="text-xs font-black text-emerald-400">
                            {{ getAttendedCount(session) }} / {{ session.users?.length || session.attendees?.length || 0 }} igrača
                        </div>
                    </div>

                    <button
                        @click="openAttendanceModal(session)"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow-md flex items-center gap-1.5 cursor-pointer"
                    >
                        <span>👥</span> Evidencija
                    </button>
                    <!-- NOVO: Dugme za Brisanje Treninga -->
                    <button
                        @click="handleDeleteSession(session)"
                        title="Obriši trening"
                        class="p-2 bg-rose-950/60 hover:bg-rose-900 border border-rose-800/80 text-rose-300 hover:text-white rounded-xl transition cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Prazno stanje -->
        <div v-else class="text-center py-12 bg-gray-800/40 border border-dashed border-gray-700/80 rounded-2xl space-y-3">
            <div class="text-3xl">⚽</div>
            <div class="text-sm font-bold text-gray-300">Nema zakazanih treninga za {{ currentMonthName }} {{ currentYear }}</div>
            <p class="text-xs text-gray-500">Kliknite na dugme "+ Zakaži Trening" za dodavanje novog treninga.</p>
        </div>

        <!-- MODAL 1: Zakazivanje Novog Treninga -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden">
                <div class="p-6 bg-gray-900 border-b border-gray-700/70 relative">
                    <button @click="showCreateModal = false" class="absolute top-5 right-5 text-gray-400 hover:text-white">✕</button>
                    <h3 class="text-2xl font-black text-white">Zakaži Novi Trening</h3>
                </div>

                <form @submit.prevent="handleCreateSession" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Naslov / Tema</label>
                        <input v-model="newSession.title" type="text" required placeholder="npr. Taktička priprema za meč" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Tip Događaja</label>
                            <select v-model="newSession.type" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white">
                                <option value="training">🏃‍♂️ Trening</option>
                                <option value="match">⚽ Utakmica</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Lokacija</label>
                            <input v-model="newSession.location" type="text" placeholder="Glavni Teren A" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 text-xs text-white" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Datum i Vreme</label>
                        <div class="grid grid-cols-3 gap-2">
                            <input v-model="formDate" type="date" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white" />
                            <select v-model="formHours" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-2 py-2 text-xs text-white">
                                <option v-for="h in 24" :key="h-1" :value="String(h-1).padStart(2,'0')">{{ String(h-1).padStart(2,'0') }}h</option>
                            </select>
                            <select v-model="formMinutes" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-2 py-2 text-xs text-white">
                                <option value="00">00 min</option>
                                <option value="15">15 min</option>
                                <option value="30">30 min</option>
                                <option value="45">45 min</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Opis / Napomena</label>
                        <textarea v-model="newSession.description" rows="3" placeholder="Uputstva za igrače..." class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-xs text-white resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Odustani</button>
                        <button type="submit" class="px-5 py-2 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl">Sačuvaj</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: Evidencija Prisustva -->
        <div v-if="showAttendanceModal && activeSession" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-6 bg-gray-900 border-b border-gray-700 relative">
                    <button @click="showAttendanceModal = false" class="absolute top-5 right-5 text-gray-400 hover:text-white">✕</button>
                    <h3 class="text-2xl font-black text-white">{{ activeSession.title }}</h3>
                    <p class="text-xs text-gray-400 mt-1">📍 {{ activeSession.location || 'Glavni Teren' }} | ⏰ {{ formatTime(activeSession.scheduled_at) }}</p>
                </div>

                <div class="p-4 bg-gray-900/60 border-b border-gray-700 flex justify-between items-center">
                    <div class="text-xs font-bold text-emerald-400">Prisutni: {{ selectedAttendeeIds.length }}</div>
                    <div class="flex gap-2">
                        <button @click="selectAllPlayers" class="text-xs font-bold text-indigo-400 bg-indigo-950/60 px-2.5 py-1 rounded-lg border border-indigo-800">✓ Označi sve</button>
                        <button @click="clearAllPlayers" class="text-xs font-bold text-gray-400 bg-gray-800 px-2.5 py-1 rounded-lg border border-gray-700">✕ Poništi</button>
                    </div>
                </div>

                <div class="p-4 overflow-y-auto space-y-2 flex-1">
                    <div
                        v-for="user in userStore.users"
                        :key="user.id"
                        @click="togglePlayerAttendance(user.id)"
                        class="flex items-center justify-between p-3 rounded-xl border cursor-pointer select-none"
                        :class="selectedAttendeeIds.includes(user.id) ? 'bg-emerald-950/20 border-emerald-500/50' : 'bg-gray-900/50 border-gray-700/50'"
                    >
                        <span class="text-xs font-bold text-white">{{ user.name }}</span>
                        <span :class="['text-xs font-bold px-3 py-1 rounded-xl border', selectedAttendeeIds.includes(user.id) ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40' : 'bg-gray-800 text-gray-500 border-gray-700']">
              {{ selectedAttendeeIds.includes(user.id) ? 'Prisutan' : 'Odsutan' }}
            </span>
                    </div>
                </div>

                <div class="p-4 bg-gray-900 border-t border-gray-700 flex justify-end gap-3">
                    <button @click="showAttendanceModal = false" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Odustani</button>
                    <button @click="handleSaveAttendance" class="px-5 py-2 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl">Sačuvaj Prisustvo</button>
                </div>
            </div>
        </div>
        <!-- MODAL 3: Potvrda Brisanja Treninga -->
        <div
            v-if="sessionToDelete"
            class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50 animate-in fade-in duration-200"
        >
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5 text-center">

                <!-- Ikonica Upozorenja -->
                <div class="w-14 h-14 bg-rose-950/80 border border-rose-800/80 text-rose-400 rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner">
                    🗑️
                </div>

                <!-- Naslov i Poruka -->
                <div class="space-y-2">
                    <h3 class="text-xl font-black text-white">Brisanje Treninga</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Da li ste sigurni da želite da obrišete trening
                        <strong class="text-white font-bold">"{{ sessionToDelete.title }}"</strong>?
                        Ova akcija je trajna i obrisaće sve podatke o prisustvu.
                    </p>
                </div>

                <!-- Dugmad za Akciju -->
                <div class="flex items-center justify-center gap-3 pt-2">
                    <button
                        @click="sessionToDelete = null"
                        class="w-full py-2.5 bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl border border-gray-700 transition cursor-pointer"
                    >
                        Odustani
                    </button>

                    <button
                        @click="confirmDeleteSession"
                        :disabled="isDeleting"
                        class="w-full py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <span v-if="isDeleting" class="animate-spin">⏳</span>
                        <span>{{ isDeleting ? 'Brisanje...' : 'Da, Obriši' }}</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useTrainingStore } from '../stores/training'
import { useUserStore } from '../stores/user'

const trainingStore = useTrainingStore()
const userStore = useUserStore()

const currentDate = ref(new Date())
const showAttendanceModal = ref(false)
const showCreateModal = ref(false)
const activeSession = ref(null)
const selectedAttendeeIds = ref([])

const newSession = reactive({
    title: '',
    type: 'training',
    scheduled_at: '',
    location: '',
    description: '',
    status: 'planned'
})

const formDate = ref(new Date().toISOString().split('T')[0])
const formHours = ref('18')
const formMinutes = ref('00')

const currentYear = computed(() => currentDate.value.getFullYear())
const currentMonth = computed(() => currentDate.value.getMonth())

const currentMonthName = computed(() => {
    return currentDate.value.toLocaleString('sr-RS', { month: 'long' })
})

const monthlySessions = computed(() => {
    return trainingStore.sessions
        .filter(s => {
            const d = new Date(s.scheduled_at)
            return d.getMonth() === currentMonth.value && d.getFullYear() === currentYear.value
        })
        .sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at))
})

const nextUpcomingSession = computed(() => {
    const now = new Date()
    return trainingStore.sessions
        .filter(s => new Date(s.scheduled_at) >= now)
        .sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at))[0]
})

const averageMonthlyAttendance = computed(() => {
    if (!monthlySessions.value.length || !userStore.users.length) return 0
    let totalAttended = 0
    let totalPossible = monthlySessions.value.length * userStore.users.length

    monthlySessions.value.forEach(s => {
        totalAttended += getAttendedCount(s)
    })

    return Math.round((totalAttended / totalPossible) * 100) || 0
})

const changeMonth = (step) => {
    currentDate.value = new Date(currentYear.value, currentMonth.value + step, 1)
}

const getDayName = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('sr-RS', { weekday: 'short' })
}

const getDayNumber = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).getDate()
}

const formatTime = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleTimeString('sr-RS', { hour: '2-digit', minute: '2-digit' })
}

const getAttendedCount = (session) => {
    const list = session.users || session.attendees || []
    return list.filter(u => u.pivot?.attended === 1 || u.pivot?.attended === true).length
}

const handleCreateSession = async () => {
    newSession.scheduled_at = `${formDate.value} ${formHours.value}:${formMinutes.value}:00`
    const success = await trainingStore.createSession(newSession)
    if (success) {
        showCreateModal.value = false
        newSession.title = ''
        newSession.location = ''
        newSession.description = ''
    }
}

const openAttendanceModal = (session) => {
    activeSession.value = session
    const attendeesList = session.users || session.attendees || []
    selectedAttendeeIds.value = attendeesList.map(a => a.id)
    showAttendanceModal.value = true
}

const togglePlayerAttendance = (userId) => {
    const idx = selectedAttendeeIds.value.indexOf(userId)
    if (idx > -1) {
        selectedAttendeeIds.value.splice(idx, 1)
    } else {
        selectedAttendeeIds.value.push(userId)
    }
}

const selectAllPlayers = () => {
    selectedAttendeeIds.value = userStore.users.map(u => u.id)
}

const clearAllPlayers = () => {
    selectedAttendeeIds.value = []
}

const handleSaveAttendance = async () => {
    if (!activeSession.value) return
    const success = await trainingStore.saveAttendance(activeSession.value.id, selectedAttendeeIds.value)
    if (success) {
        showAttendanceModal.value = false
    }
}

// Reaktivna stanja za modal brisanja
const sessionToDelete = ref(null)
const isDeleting = ref(false)

// Otvaranje modala sa izabranim treningom
const handleDeleteSession = (session) => {
    sessionToDelete.value = session
}

// Potvrda i slanje DELETE zahteva na backend
const confirmDeleteSession = async () => {
    if (!sessionToDelete.value) return

    isDeleting.value = true
    const success = await trainingStore.deleteSession(sessionToDelete.value.id)
    isDeleting.value = false

    if (success) {
        sessionToDelete.value = null
    }
}

onMounted(() => {
    trainingStore.fetchSessions()
    userStore.fetchUsers()
})
</script>