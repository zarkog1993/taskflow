<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Gornja traka Taktike -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <div>
                <h2 class="text-2xl font-black text-white tracking-tight">Taktička Tabla</h2>
                <p class="text-xs text-gray-400 mt-0.5">Klikni na poziciju da dodijeliš igrača ili ih prevlači po terenu</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 bg-gray-900 border border-gray-700 rounded-xl px-3 py-1.5">
                    <span class="text-xs font-bold text-gray-400 uppercase">EKIPA:</span>
                    <select v-model="selectedTeamFilter" @change="onTeamFilterChange" class="bg-transparent text-white text-xs font-bold outline-none cursor-pointer">
                        <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </select>
                </div>

                <select v-model="selectedFormation" @change="applyFormation" class="bg-gray-900 border border-gray-700 text-white text-xs font-bold rounded-xl px-3 py-2.5 outline-none cursor-pointer">
                    <option value="4-3-3">Formacija 4-3-3</option>
                    <option value="4-4-2">Formacija 4-4-2</option>
                    <option value="4-2-3-1">Formacija 4-2-3-1</option>
                    <option value="3-5-2">Formacija 3-5-2</option>
                </select>

                <button @click="saveTactics" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer">
                    <span>💾</span> Sačuvaj Taktiku
                </button>
            </div>
        </div>

        <!-- Teren i Sastav Ekipe -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- LEVO: Fudbalski Teren -->
            <div class="lg:col-span-3 bg-gray-900/90 border border-gray-800 rounded-3xl p-4 sm:p-6 shadow-2xl relative overflow-hidden">
                <div ref="pitchRef" @dragover.prevent @drop="onDropOnPitch" class="relative w-full aspect-[1.5/1] bg-emerald-950/50 rounded-2xl border-2 border-emerald-800/60 overflow-hidden select-none">

                    <svg class="absolute inset-0 w-full h-full stroke-emerald-500/30 fill-none pointer-events-none" stroke-width="2">
                        <rect x="2" y="2" width="99%" height="96%" rx="10" />
                        <line x1="50%" y1="0" x2="50%" y2="100%" />
                        <circle cx="50%" cy="50%" r="12%" />
                        <rect x="2" y="22%" width="16%" height="56%" />
                        <rect x="82%" y="22%" width="16%" height="56%" />
                        <rect x="2" y="36%" width="6%" height="28%" />
                        <rect x="92%" y="36%" width="6%" height="28%" />
                    </svg>

                    <!-- Igrači na Terenu -->
                    <div
                        v-for="spot in fieldSpots"
                        :key="spot.id"
                        draggable="true"
                        @dragstart="onDragStart($event, spot)"
                        @click="openPlayerPicker(spot)"
                        class="absolute -translate-x-1/2 -translate-y-1/2 cursor-pointer transition-transform hover:scale-110 z-10 group"
                        :style="{ left: spot.x + '%', top: spot.y + '%' }"
                    >
                        <div class="flex flex-col items-center">
                            <div
                                class="w-11 h-11 border-2 text-white font-black text-xs rounded-full flex items-center justify-center shadow-2xl relative transition"
                                :class="spot.player ? 'bg-indigo-600 border-white' : 'bg-gray-800/90 border-dashed border-gray-500 text-gray-400'"
                            >
                                #{{ spot.player?.player_profile?.jersey_number || spot.defaultNumber || '?' }}
                                <span class="absolute -bottom-1 -right-1 bg-emerald-500 text-gray-950 text-[9px] font-extrabold px-1 rounded border border-white">
                  {{ spot.position }}
                </span>
                            </div>
                            <span class="text-[10px] font-bold text-white bg-gray-950/90 px-2 py-0.5 rounded-md border border-gray-800 mt-1 shadow-md whitespace-nowrap">
                {{ spot.player ? spot.player.name : spot.roleName }}
              </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DESNO: Sastav Ekipe -->
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-3xl p-5 shadow-xl space-y-4 flex flex-col">
                <h3 class="text-xs font-bold uppercase text-gray-400 tracking-wider">SASTAV EKIPE ({{ teamPlayers.length }})</h3>

                <div class="space-y-2 flex-1 overflow-y-auto max-h-[500px] pr-1">
                    <div
                        v-for="player in teamPlayers"
                        :key="player.id"
                        class="p-3 bg-gray-900/80 border border-gray-700/60 rounded-xl flex items-center justify-between"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-950 border border-indigo-800 text-indigo-300 font-bold text-xs flex items-center justify-center">
                                #{{ player.player_profile?.jersey_number || '-' }}
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white">{{ player.name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ player.player_profile?.primary_position || 'N/A' }}</div>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-gray-800 text-gray-400">Dostupan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL ZA IZBOR IGRAČA -->
        <div v-if="activeSpotForSelection" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b border-gray-800 pb-3">
                    <div>
            <span class="text-[10px] font-mono font-bold uppercase text-indigo-400 bg-indigo-950 px-2 py-0.5 rounded border border-indigo-800">
              {{ activeSpotForSelection.position }} - {{ activeSpotForSelection.roleName }}
            </span>
                        <h3 class="text-xl font-black text-white mt-1">Izaberi Igrača za Poziciju</h3>
                    </div>
                    <button @click="activeSpotForSelection = null" class="text-gray-400 hover:text-white font-bold text-lg">✕</button>
                </div>

                <div class="space-y-2">
                    <input v-model="searchQuery" type="text" placeholder="Pretraži po imenu..." class="w-full bg-gray-950 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500" />
                </div>

                <div class="max-h-64 overflow-y-auto space-y-2 pr-1">
                    <div
                        v-for="user in filteredUsers"
                        :key="user.id"
                        @click="assignPlayerToSpot(user)"
                        class="p-3 bg-gray-800/60 hover:bg-indigo-950/60 border border-gray-700/50 rounded-xl flex items-center justify-between cursor-pointer transition"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-950 border border-indigo-800 flex items-center justify-center font-bold text-xs text-indigo-300">
                                #{{ user.player_profile?.jersey_number || '-' }}
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white">{{ user.name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ user.player_profile?.primary_position || 'N/A' }}</div>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-indigo-400 bg-indigo-950 px-2 py-1 rounded border border-indigo-800">Postavi</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-800 flex justify-between items-center">
                    <button v-if="activeSpotForSelection.player" @click="removePlayerFromSpot" class="text-xs font-bold text-rose-400">🗑️ Ukloni sa pozicije</button>
                    <div v-else></div>
                    <button @click="activeSpotForSelection = null" class="px-4 py-2 bg-gray-800 text-gray-300 text-xs font-bold rounded-xl">Zatvori</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const pitchRef = ref(null)
const selectedFormation = ref('4-3-3')
const draggedSpot = ref(null)

const teams = ref([])
const allUsers = ref([])
const teamPlayers = ref([])
const activeSpotForSelection = ref(null)
const selectedTeamFilter = ref('')
const searchQuery = ref('')

const fieldSpots = ref([
    { id: 1, position: 'GK', roleName: 'Golman', defaultNumber: 1, x: 8, y: 50, player: null },
    { id: 2, position: 'RB', roleName: 'Desni Bek', defaultNumber: 2, x: 25, y: 18, player: null },
    { id: 3, position: 'CB', roleName: 'Štoper D', defaultNumber: 5, x: 25, y: 40, player: null },
    { id: 4, position: 'CB', roleName: 'Štoper L', defaultNumber: 4, x: 25, y: 60, player: null },
    { id: 5, position: 'LB', roleName: 'Levi Bek', defaultNumber: 3, x: 25, y: 82, player: null },
    { id: 6, position: 'CM', roleName: 'Vezni D', defaultNumber: 10, x: 50, y: 30, player: null },
    { id: 7, position: 'CM', roleName: 'Vezni C', defaultNumber: 8, x: 48, y: 50, player: null },
    { id: 8, position: 'CM', roleName: 'Vezni L', defaultNumber: 6, x: 50, y: 70, player: null },
    { id: 9, position: 'RW', roleName: 'Desno Krilo', defaultNumber: 7, x: 78, y: 20, player: null },
    { id: 10, position: 'ST', roleName: 'Napadač', defaultNumber: 9, x: 85, y: 50, player: null },
    { id: 11, position: 'LW', roleName: 'Levo Krilo', defaultNumber: 11, x: 78, y: 80, player: null },
])

const fetchData = async () => {
    try {
        const [teamsRes, usersRes] = await Promise.all([
            api.get('/teams'),
            api.get('/users')
        ])
        teams.value = teamsRes.data.data || teamsRes.data || []
        allUsers.value = usersRes.data.data || usersRes.data || []

        if (teams.value.length > 0) {
            selectedTeamFilter.value = teams.value[0].id
            loadTeamPlayers(teams.value[0].id)
        }
    } catch (err) {
        console.error('Greška pri učitavanju:', err)
    }
}

const loadTeamPlayers = (teamId) => {
    const selectedTeam = teams.value.find(t => t.id === Number(teamId))
    teamPlayers.value = selectedTeam?.users || selectedTeam?.players || allUsers.value
}

const onTeamFilterChange = () => {
    loadTeamPlayers(selectedTeamFilter.value)
}

const filteredUsers = computed(() => {
    return teamPlayers.value.filter(user => {
        return !searchQuery.value || user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    })
})

const openPlayerPicker = (spot) => {
    activeSpotForSelection.value = spot
    searchQuery.value = ''
}

const assignPlayerToSpot = (user) => {
    if (!activeSpotForSelection.value) return
    fieldSpots.value.forEach(s => {
        if (s.player?.id === user.id) s.player = null
    })
    activeSpotForSelection.value.player = user
    activeSpotForSelection.value = null
}

const removePlayerFromSpot = () => {
    if (activeSpotForSelection.value) {
        activeSpotForSelection.value.player = null
        activeSpotForSelection.value = null
    }
}

const onDragStart = (event, spot) => { draggedSpot.value = spot }

const onDropOnPitch = (event) => {
    if (!pitchRef.value || !draggedSpot.value) return
    const rect = pitchRef.value.getBoundingClientRect()
    const x = Math.min(Math.max(((event.clientX - rect.left) / rect.width) * 100, 5), 95)
    const y = Math.min(Math.max(((event.clientY - rect.top) / rect.height) * 100, 5), 95)
    draggedSpot.value.x = Math.round(x)
    draggedSpot.value.y = Math.round(y)
    draggedSpot.value = null
}

const applyFormation = () => {
    if (selectedFormation.value === '4-3-3') {
        fieldSpots.value[1].x = 25; fieldSpots.value[1].y = 18
        fieldSpots.value[2].x = 25; fieldSpots.value[2].y = 40
        fieldSpots.value[3].x = 25; fieldSpots.value[3].y = 60
        fieldSpots.value[4].x = 25; fieldSpots.value[4].y = 82
        fieldSpots.value[5].x = 50; fieldSpots.value[5].y = 30
        fieldSpots.value[6].x = 48; fieldSpots.value[6].y = 50
        fieldSpots.value[7].x = 50; fieldSpots.value[7].y = 70
        fieldSpots.value[8].x = 78; fieldSpots.value[8].y = 20
        fieldSpots.value[9].x = 85; fieldSpots.value[9].y = 50
        fieldSpots.value[10].x = 78; fieldSpots.value[10].y = 80
    }
}

const saveTactics = () => { alert('Taktika je uspešno sačuvana!') }

onMounted(() => { fetchData() })
</script>