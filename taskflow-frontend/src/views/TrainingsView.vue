<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Gornja traka: Navigacija kroz mesece i dodavanje -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <div>
                <h2 class="text-2xl font-black text-white tracking-tight">Kalendar Treninga i Utakmica</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pregled zakazanih aktivnosti po danima i evidencija prisustva</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <!-- Promena meseca -->
                <div class="flex items-center bg-gray-900 rounded-xl p-1 border border-gray-700/80">
                    <button @click="changeMonth(-1)" class="p-2 text-gray-400 hover:text-white transition">
                        ←
                    </button>
                    <span class="px-3 text-xs font-bold text-white min-w-[110px] text-center capitalize">
            {{ currentMonthName }} {{ currentYear }}
          </span>
                    <button @click="changeMonth(1)" class="p-2 text-gray-400 hover:text-white transition">
                        →
                    </button>
                </div>

                <button
                    @click="showCreateModal = true"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-1.5 border border-indigo-500/30"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    + Zakaži Događaj
                </button>
            </div>
        </div>

        <!-- Mesečna Mreža Kalendara -->
        <div class="bg-gray-800/90 border border-gray-700/80 rounded-2xl p-4 shadow-2xl overflow-x-auto">
            <div class="grid grid-cols-7 gap-2 mb-2 text-center text-[11px] font-extrabold uppercase text-gray-400 tracking-wider min-w-[700px]">
                <div class="py-2 bg-gray-900/60 rounded-lg">Ponedeljak</div>
                <div class="py-2 bg-gray-900/60 rounded-lg">Utorak</div>
                <div class="py-2 bg-gray-900/60 rounded-lg">Sreda</div>
                <div class="py-2 bg-gray-900/60 rounded-lg">Četvrtak</div>
                <div class="py-2 bg-gray-900/60 rounded-lg">Petak</div>
                <div class="py-2 bg-gray-900/60 rounded-lg text-indigo-400">Subota</div>
                <div class="py-2 bg-gray-900/60 rounded-lg text-indigo-400">Nedelja</div>
            </div>

            <div class="grid grid-cols-7 gap-2 min-w-[700px]">
                <div v-for="blank in firstDayOffset" :key="'blank-' + blank" class="min-h-[110px] bg-gray-900/20 rounded-xl border border-gray-800/40 opacity-40"></div>

                <div
                    v-for="day in daysInMonth"
                    :key="day"
                    :class="[
            'min-h-[120px] p-2 rounded-xl border transition flex flex-col justify-between',
            isToday(day) ? 'bg-indigo-950/40 border-indigo-500/60' : 'bg-gray-900/60 border-gray-700/50 hover:border-gray-600'
          ]"
                >
                    <div class="flex justify-between items-center mb-1">
            <span :class="['text-xs font-bold font-mono px-2 py-0.5 rounded-md', isToday(day) ? 'bg-indigo-600 text-white' : 'text-gray-300']">
              {{ day }}
            </span>
                    </div>

                    <div class="space-y-1.5 flex-1 overflow-y-auto max-h-[90px] custom-scrollbar">
                        <div
                            v-for="session in getSessionsForDay(day)"
                            :key="session.id"
                            class="p-1.5 rounded-lg border text-[11px] leading-tight flex flex-col gap-1 shadow-sm transition"
                            :class="[
                session.type === 'match' ? 'bg-emerald-950/60 border-emerald-500/40 text-emerald-300' : 'bg-indigo-950/60 border-indigo-500/40 text-indigo-200'
              ]"
                        >
                            <div class="flex justify-between items-start font-bold">
                                <span class="truncate max-w-[90px]">{{ session.title }}</span>
                                <span class="text-[9px] font-mono opacity-80">{{ formatTime(session.scheduled_at) }}</span>
                            </div>

                            <button
                                @click="openAttendanceModal(session)"
                                class="w-full mt-1 py-0.5 px-1 bg-gray-800/90 hover:bg-gray-700 text-gray-200 text-[10px] rounded border border-gray-600/50 flex items-center justify-between transition"
                            >
                                <span>👥 Igrači</span>
                                <span class="font-bold text-indigo-400">({{ session.attendees?.length || 0 }})</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL 1: Zakazivanje Novog Događaja -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">

                <!-- Zaglavlje Modala -->
                <div class="p-6 bg-gradient-to-r from-gray-900 via-gray-900/90 to-gray-800 border-b border-gray-700/70 relative">
                    <button
                        @click="showCreateModal = false"
                        class="absolute top-5 right-5 text-gray-400 hover:text-white p-1.5 rounded-xl hover:bg-gray-800 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400">Novi Raspored</span>
                    </div>
                    <h3 class="text-2xl font-black text-white tracking-tight">Zakaži Događaj</h3>
                    <p class="text-xs text-gray-400 mt-1">Unesite detalje treninga ili utakmice za trenažni mikrociklus</p>
                </div>

                <!-- Forma -->
                <form @submit.prevent="handleCreateSession" class="p-6 space-y-4">
                    <!-- Naslov / Tema -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Naslov / Tema Događaja</label>
                        <input
                            v-model="newSession.title"
                            type="text"
                            required
                            placeholder="npr. Taktička priprema za meč ili Utakmica protiv Rudara"
                            class="w-full bg-gray-900/90 border border-gray-700/80 rounded-xl px-4 py-3 text-white text-xs outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition placeholder-gray-500"
                        />
                    </div>

                    <!-- Tip Događaja i Lokacija (u 2 kolone) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Tip Događaja</label>
                            <select
                                v-model="newSession.type"
                                class="w-full bg-gray-900/90 border border-gray-700/80 rounded-xl px-3.5 py-3 text-white text-xs outline-none focus:border-indigo-500 transition cursor-pointer"
                            >
                                <option value="training">🏃‍♂️ Trening</option>
                                <option value="match">⚽ Utakmica</option>
                                <option value="tactical_analysis">📋 Taktička Analiza</option>
                                <option value="fitness">🏋️‍♂️ Kondicija / Oporavak</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Lokacija / Teren</label>
                            <input
                                v-model="newSession.location"
                                type="text"
                                placeholder="npr. Glavni Teren A"
                                class="w-full bg-gray-900/90 border border-gray-700/80 rounded-xl px-4 py-3 text-white text-xs outline-none focus:border-indigo-500 transition placeholder-gray-500"
                            />
                        </div>
                    </div>

                    <!-- Datum i Vreme Održavanja (Custom 24h Selector) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Datum i Vreme Održavanja</label>
                        <div class="grid grid-cols-3 gap-3">
                            <!-- Datum -->
                            <div class="col-span-1 sm:col-span-1">
                                <input
                                    v-model="formDate"
                                    type="date"
                                    required
                                    class="w-full bg-gray-900/90 border border-gray-700/80 rounded-xl px-3 py-2.5 text-white text-xs outline-none focus:border-indigo-500 transition font-mono dark-date-picker"
                                />
                            </div>

                            <!-- Sati (00 - 23) -->
                            <div>
                                <select
                                    v-model="formHours"
                                    class="w-full bg-gray-900/90 border border-gray-700/80 rounded-xl px-3 py-2.5 text-white text-xs outline-none focus:border-indigo-500 transition font-mono cursor-pointer"
                                >
                                    <option v-for="h in 24" :key="h-1" :value="String(h-1).padStart(2, '0')">
                                        {{ String(h-1).padStart(2, '0') }}h
                                    </option>
                                </select>
                            </div>

                            <!-- Minuti (00, 15, 30, 45) -->
                            <div>
                                <select
                                    v-model="formMinutes"
                                    class="w-full bg-gray-900/90 border border-gray-700/80 rounded-xl px-3 py-2.5 text-white text-xs outline-none focus:border-indigo-500 transition font-mono cursor-pointer"
                                >
                                    <option value="00">00 min</option>
                                    <option value="15">15 min</option>
                                    <option value="30">30 min</option>
                                    <option value="45">45 min</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Opis / Napomena -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Opis i Trenažne Napomene</label>
                        <textarea
                            v-model="newSession.description"
                            rows="3"
                            placeholder="Kratka uputstva za igrače, cilj treninga ili oprema..."
                            class="w-full bg-gray-900/90 border border-gray-700/80 rounded-xl p-3.5 text-white text-xs outline-none focus:border-indigo-500 transition placeholder-gray-500 resize-none"
                        ></textarea>
                    </div>

                    <!-- Podnožje sa Dugmadima -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-700/70">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="px-5 py-2.5 text-xs font-semibold text-gray-400 hover:text-white transition"
                        >
                            Odustani
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition flex items-center gap-2 border border-indigo-500/30"
                        >
                            <span>Sačuvaj Događaj</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
        <!-- MODAL 2: Pregled i Evidencija Prisustva -->
        <div v-if="showAttendanceModal && activeSession" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <!-- Zaglavlje Modala (Detalji Događaja + Opis) -->
                <div class="p-6 bg-gradient-to-r from-gray-900 via-gray-900/90 to-gray-800 border-b border-gray-700/70 relative">
                    <button
                        @click="showAttendanceModal = false"
                        class="absolute top-5 right-5 text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="flex items-center gap-2 mb-2">
                        <span :class="[
                          'text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full border tracking-wider',
                          activeSession.type === 'match' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30'
                        ]">
                          {{ activeSession.type === 'match' ? '⚽ Utakmica' : '🏃‍♂️ Trening' }}
                        </span>
                        <span class="text-xs text-gray-400 font-medium">📍 {{ activeSession.location || 'Glavni Teren' }}</span>
                    </div>

                    <h3 class="text-2xl font-black text-white tracking-tight mb-2">{{ activeSession.title }}</h3>

                    <!-- Prikaz Unetog Opisa / Napomene -->
                    <div class="bg-gray-800/80 p-3 rounded-xl border border-gray-700/60 mt-2">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 mb-0.5">Opis / Trenažni Cilj:</div>
                        <p class="text-xs text-gray-200 leading-relaxed">
                            {{ activeSession.description || 'Nema unetog dodatnog opisa za ovaj događaj.' }}
                        </p>
                    </div>
                </div>

                <div class="p-4 bg-gray-900/60 border-b border-gray-700/60 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <div class="bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-xl text-xs">
                                <span class="text-gray-400">Prisutni: </span>
                                <span class="font-extrabold text-emerald-400">{{ selectedAttendeeIds.length }}</span>
                            </div>
                            <div class="bg-indigo-500/10 border border-indigo-500/20 px-3 py-1.5 rounded-xl text-xs">
                                <span class="text-gray-400">Odziv: </span>
                                <span class="font-extrabold text-indigo-400">{{ attendancePercentage }}%</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="selectAllPlayers" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 bg-indigo-950/60 px-2.5 py-1.5 rounded-lg border border-indigo-800/50 transition">✓ Označi sve</button>
                            <button type="button" @click="clearAllPlayers" class="text-[11px] font-bold text-gray-400 hover:text-white bg-gray-800 px-2.5 py-1.5 rounded-lg border border-gray-700 transition">✕ Poništi</button>
                        </div>
                    </div>

                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Pretraži igrače..."
                        class="w-full bg-gray-900 border border-gray-700/80 rounded-xl px-3.5 py-2 text-xs text-white placeholder-gray-500 outline-none focus:border-indigo-500 transition"
                    />
                </div>

                <div class="p-4 overflow-y-auto space-y-2 flex-1 custom-scrollbar">
                    <div
                        v-for="user in filteredPlayers"
                        :key="user.id"
                        @click="togglePlayerAttendance(user.id)"
                        :class="[
              'flex items-center justify-between p-3 rounded-xl border transition cursor-pointer select-none',
              selectedAttendeeIds.includes(user.id) ? 'bg-emerald-950/20 border-emerald-500/50' : 'bg-gray-900/50 border-gray-700/50'
            ]"
                    >
                        <div class="flex items-center space-x-3">
                            <img
                                :src="user.player_profile?.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=312e81&color=c7d2fe&size=96`"
                                class="w-9 h-9 rounded-full object-cover border border-gray-600 shadow-sm"
                                :alt="user.name"
                            />
                            <div>
                                <div class="text-xs font-bold text-white">{{ user.name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">
                                    {{ user.player_profile?.primary_position || 'CM' }} • #{{ user.player_profile?.jersey_number || '-' }}
                                </div>
                            </div>
                        </div>

                        <span :class="[
              'text-xs font-bold uppercase px-3 py-1 rounded-xl border',
              selectedAttendeeIds.includes(user.id) ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40' : 'bg-gray-800 text-gray-500 border-gray-700'
            ]">
              {{ selectedAttendeeIds.includes(user.id) ? 'Prisutan' : 'Odsutan' }}
            </span>
                    </div>
                </div>

                <div class="p-4 bg-gray-900/80 border-t border-gray-700/80 flex items-center justify-between">
                    <span class="text-xs text-gray-400">Prisutno: <strong class="text-white">{{ selectedAttendeeIds.length }}</strong></span>
                    <div class="flex space-x-3">
                        <button type="button" @click="showAttendanceModal = false" class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white">Odustani</button>
                        <button @click="handleSaveAttendance" class="px-5 py-2 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition">Sačuvaj Prisustvo</button>
                    </div>
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
const searchQuery = ref('')

const newSession = reactive({
    title: '',
    type: 'training',
    scheduled_at: '',
    location: '',
    description: '',
    status: 'planned'
})

onMounted(() => {
    trainingStore.fetchSessions()
    userStore.fetchUsers()
})
// Pomocne promenljive za 24h birač
const formDate = ref(new Date().toISOString().split('T')[0])
const formHours = ref('18')
const formMinutes = ref('00')
const currentYear = computed(() => currentDate.value.getFullYear())
const currentMonth = computed(() => currentDate.value.getMonth())

const currentMonthName = computed(() => {
    return currentDate.value.toLocaleString('sr-RS', { month: 'long' })
})

const daysInMonth = computed(() => {
    return new Date(currentYear.value, currentMonth.value + 1, 0).getDate()
})

const firstDayOffset = computed(() => {
    let day = new Date(currentYear.value, currentMonth.value, 1).getDay()
    return day === 0 ? 6 : day - 1
})

const changeMonth = (step) => {
    currentDate.value = new Date(currentYear.value, currentMonth.value + step, 1)
}

const isToday = (day) => {
    const today = new Date()
    return today.getDate() === day && today.getMonth() === currentMonth.value && today.getFullYear() === currentYear.value
}

const getSessionsForDay = (day) => {
    return trainingStore.sessions.filter(s => {
        const d = new Date(s.scheduled_at)
        return d.getDate() === day && d.getMonth() === currentMonth.value && d.getFullYear() === currentYear.value
    })
}

// U funkciji handleCreateSession dodajte spajanje u ISO format
const handleCreateSession = async () => {
    // Spajamo datum, sate i minute u pun format za backend
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
    selectedAttendeeIds.value = session.attendees ? session.attendees.map(a => a.id) : []
    searchQuery.value = ''
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

const filteredPlayers = computed(() => {
    if (!searchQuery.value) return userStore.users
    const q = searchQuery.value.toLowerCase()
    return userStore.users.filter(u => u.name.toLowerCase().includes(q))
})

const attendancePercentage = computed(() => {
    if (!userStore.users.length) return 0
    return Math.round((selectedAttendeeIds.value.length / userStore.users.length) * 100)
})

const handleSaveAttendance = async () => {
    if (!activeSession.value) return
    const success = await trainingStore.saveAttendance(activeSession.value.id, selectedAttendeeIds.value)
    if (success) {
        showAttendanceModal.value = false
    }
}

const formatTime = (dateStr) => {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleTimeString('sr-RS', { hour: '2-digit', minute: '2-digit' })
}
</script>