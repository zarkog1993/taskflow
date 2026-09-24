<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- ZAGLAVLJE I DUGME -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Upravljanje Ekipama</h1>
                <p class="text-xs text-gray-400 mt-0.5">Pregled svih timova, starosnih kategorija i sastava ekipa</p>
            </div>

            <button
                @click="showCreateTeamModal = true"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 border border-indigo-500/30 cursor-pointer"
            >
                <span>➕</span> NOVA EKIPA
            </button>
        </div>

        <!-- STATISTIČKE KARTICE NA VRHU -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Ukupno Ekipa -->
            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">UKUPNO EKIPA</span>
                    <div class="text-2xl font-black text-white mt-1 font-mono">{{ teams.length }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-950 border border-indigo-800 text-indigo-400 flex items-center justify-center text-lg">🛡️</div>
            </div>

            <!-- Registrovani Igrači -->
            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">REGISTROVANI IGRAČI</span>
                    <div class="text-2xl font-black text-emerald-400 mt-1 font-mono">{{ totalPlayersCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-950 border border-emerald-800 text-emerald-400 flex items-center justify-center text-lg">🏃</div>
            </div>

            <!-- Slobodni Igrači -->
            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">SLOBODNI IGRAČI</span>
                    <div class="text-2xl font-black text-amber-400 mt-1 font-mono">{{ freePlayersCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-950 border border-amber-800 text-amber-400 flex items-center justify-center text-lg">📋</div>
            </div>
        </div>

        <!-- KARTICE EKIPA -->
        <div v-if="teams.length" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div v-for="team in teams" :key="team.id" class="bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl space-y-4 flex flex-col justify-between">
                <div class="space-y-4">
                    <!-- Header Ekipe -->
                    <div class="flex justify-between items-start border-b border-gray-700/60 pb-3">
                        <div>
              <span class="text-[10px] font-bold uppercase text-indigo-400 bg-indigo-950 px-2.5 py-0.5 rounded border border-indigo-800">
                SELEKCIJA EKIPE
              </span>
                            <h3 class="text-xl font-black text-white mt-1">{{ team.name }}</h3>
                            <p class="text-xs text-gray-400">Ukupno {{ getTeamPlayers(team).length }} dodeljenih igrač(a)</p>
                        </div>
                        <span class="text-xs font-black text-gray-300 bg-gray-900 border border-gray-700 px-3 py-1 rounded-xl uppercase">
              {{ team.category || 'Seniori' }}
            </span>
                    </div>

                    <!-- IGRAČKI KADAR EKIPE -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-bold uppercase text-gray-400 px-1">
                            <span>IGRAČKI KADAR</span>
                            <span>DRES / POZICIJA</span>
                        </div>

                        <div v-if="getTeamPlayers(team).length" class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            <div
                                v-for="player in getTeamPlayers(team)"
                                :key="player.id"
                                class="p-3 bg-gray-900/80 border border-gray-700/60 rounded-xl flex items-center justify-between hover:border-gray-600 transition"
                            >
                                <!-- Slika i Ime -->
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-950 border border-indigo-800 overflow-hidden flex items-center justify-center font-bold text-indigo-300 text-xs shrink-0">
                                        <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover" />
                                        <span v-else>#{{ player.jersey_number || '-' }}</span>
                                    </div>
                                    <div>
                                        <router-link :to="`/players/${player.id}`" class="text-xs font-bold text-white hover:text-indigo-400 transition">
                                            {{ player.name }}
                                        </router-link>
                                        <div class="text-[10px] text-gray-400 font-mono">{{ player.email || 'Nema email' }}</div>
                                    </div>
                                </div>

                                <!-- Pozicija i DRES -->
                                <div class="flex items-center gap-2">
                  <span class="text-[10px] font-black px-2 py-0.5 rounded bg-indigo-950 text-indigo-400 border border-indigo-800">
                    {{ player.primary_position }}
                  </span>
                                    <span class="text-[10px] font-mono font-bold text-gray-300 bg-gray-800 px-2 py-0.5 rounded border border-gray-700">
                    #{{ player.jersey_number || '-' }}
                  </span>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8 text-xs text-gray-500 italic bg-gray-900/40 rounded-xl border border-gray-800">
                            Nema dodeljenih igrača za ovu ekipu.
                        </div>
                    </div>
                </div>

                <!-- Akcije na dnu kartice -->
                <div class="flex items-center gap-3 pt-3 border-t border-gray-700/60">
                    <button @click="openManageModal(team)" class="flex-1 py-2.5 bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl border border-gray-700 transition cursor-pointer flex items-center justify-center gap-1.5">
                        ⚙️ Upravljaj Sastavom
                    </button>
                    <router-link :to="`/teams/${team.id}`" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl text-center transition shadow-lg border border-indigo-500/30">
                        Prikaži Detalje →
                    </router-link>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-16 bg-gray-800/40 border border-gray-700/50 rounded-2xl">
            <p class="text-sm text-gray-400 italic">Nema definisanih ekipa. Kliknite na "NOVA EKIPA" da biste je kreirali.</p>
        </div>

        <!-- MODAL 1: Nova Ekipa -->
        <div v-if="showCreateTeamModal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5">
                <div class="flex justify-between items-center border-b border-gray-700 pb-3">
                    <h3 class="text-lg font-black text-white">Dodaj Novu Ekipu</h3>
                    <button @click="showCreateTeamModal = false" class="text-gray-400 hover:text-white font-bold">✕</button>
                </div>

                <form @submit.prevent="handleCreateTeam" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Naziv Ekipe *</label>
                        <input v-model="newTeam.name" type="text" required placeholder="npr. Rudar U19" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Kategorija</label>
                        <select v-model="newTeam.age_group" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
                            <option value="senior">Seniori</option>
                            <option value="u19">U19 (Omladinci)</option>
                            <option value="u17">U17 (Kadeti)</option>
                            <option value="u15">U15 (Pioniri)</option>
                            <option value="u13">U13 (Mlađi pioniri)</option>
                            <option value="u11">U11 (Petlići)</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                        <button type="button" @click="showCreateTeamModal = false" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Odustani</button>
                        <button type="submit" :disabled="isSubmitting" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2 rounded-xl transition shadow-lg cursor-pointer disabled:opacity-50">
                            {{ isSubmitting ? 'Kreiranje...' : 'Sačuvaj Ekipu' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: Upravljanje Sastavom Ekipe -->
        <div v-if="selectedTeamForManage" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-5 max-h-[85vh] flex flex-col">
                <div class="flex justify-between items-center border-b border-gray-700 pb-3 shrink-0">
                    <div>
                        <h3 class="text-lg font-black text-white">Sastav Ekipe: {{ selectedTeamForManage.name }}</h3>
                        <p class="text-xs text-gray-400">Dodajte ili izbacite igrače iz ove selekcije</p>
                    </div>
                    <button @click="selectedTeamForManage = null" class="text-gray-400 hover:text-white font-bold">✕</button>
                </div>

                <div class="overflow-y-auto space-y-4 pr-1 flex-1">
                    <!-- Dodavanje slobodnih igrača -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1.5">Dodaj Slobodnog Igrača u Ekipu:</label>
                        <div class="flex gap-2">
                            <select v-model="selectedPlayerToAssign" class="flex-1 bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
                                <option :value="null" disabled>Izaberite igratča bez ekipe...</option>
                                <option v-for="p in unassignedPlayers" :key="p.id" :value="p.id">
                                    {{ p.name }} (#{{ p.jersey_number || '-' }} - {{ p.primary_position }})
                                </option>
                            </select>
                            <button @click="assignPlayerToTeam" :disabled="!selectedPlayerToAssign" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition disabled:opacity-50 cursor-pointer">
                                + Dodaj
                            </button>
                        </div>
                    </div>

                    <!-- Lista Igrača u Ekipi sa opcijom za uklanjanje -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1.5">Trenutni Igrači u Ekipi:</label>
                        <div v-if="getTeamPlayers(selectedTeamForManage).length" class="space-y-2">
                            <div v-for="player in getTeamPlayers(selectedTeamForManage)" :key="player.id" class="flex items-center justify-between p-3 bg-gray-900/80 border border-gray-700/60 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-950 border border-indigo-800 flex items-center justify-center font-bold text-indigo-300 text-xs">
                                        #{{ player.jersey_number || '-' }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white">{{ player.name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ player.primary_position }}</p>
                                    </div>
                                </div>
                                <button @click="removePlayerFromTeam(player.id)" class="text-rose-400 hover:text-rose-300 text-xs font-bold px-2 py-1 rounded bg-rose-950/60 border border-rose-800/80 cursor-pointer">
                                    Ukloni
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-xs text-gray-500 italic py-4 text-center">Nema igrača u ovoj ekipi.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-700 shrink-0">
                    <button @click="selectedTeamForManage = null" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2 rounded-xl transition cursor-pointer">
                        Završi
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../services/api'

const teams = ref([])
const allPlayers = ref([])

const showCreateTeamModal = ref(false)
const selectedTeamForManage = ref(null)
const selectedPlayerToAssign = ref(null)
const isSubmitting = ref(false)

const newTeam = reactive({
    name: '',
    age_group: 'senior'
})

// Dohvatanje timova i svih igrača sa backenda
const fetchData = async () => {
    try {
        const [teamsRes, playersRes] = await Promise.all([
            api.get('/teams'),
            api.get('/players')
        ])
        teams.value = teamsRes.data.data || teamsRes.data || []
        allPlayers.value = playersRes.data.data || playersRes.data || []
    } catch (err) {
        console.error('Greška pri učitavanju timova i igrača:', err)
    }
}

onMounted(fetchData)

// Dobijanje igračkog kadra za određeni tim (radi i ako backend vraća $team->players ili spaja preko team_id)
const getTeamPlayers = (team) => {
    if (team.players && team.players.length) return team.players
    return allPlayers.value.filter(p => p.team_id === team.id)
}

// Slobodni igrači bez ekipe
const unassignedPlayers = computed(() => {
    return allPlayers.value.filter(p => !p.team_id)
})

// Ukupno i slobodni igrači
const totalPlayersCount = computed(() => allPlayers.value.length)
const freePlayersCount = computed(() => unassignedPlayers.value.length)

// Kreiranje nove ekipe
const handleCreateTeam = async () => {
    isSubmitting.value = true
    try {
        await api.post('/teams', newTeam)
        showCreateTeamModal.value = false
        newTeam.name = ''
        newTeam.age_group = 'senior'
        await fetchData()
    } catch (err) {
        alert(err.response?.data?.message || 'Greška pri kreiranju ekipe')
    } finally {
        isSubmitting.value = false
    }
}

// Otvaranje modala za upravljanje sastavom
const openManageModal = (team) => {
    selectedTeamForManage.value = team
    selectedPlayerToAssign.value = null
}

// Dodavanje igrača u ekipu
const assignPlayerToTeam = async () => {
    if (!selectedPlayerToAssign.value || !selectedTeamForManage.value) return
    try {
        await api.put(`/players/${selectedPlayerToAssign.value}`, {
            team_id: selectedTeamForManage.value.id
        })
        selectedPlayerToAssign.value = null
        await fetchData()
    } catch (err) {
        alert(err.response?.data?.message || 'Greška pri dodeljivanju igrača')
    }
}

// Uklanjanje igrača iz ekipe
const removePlayerFromTeam = async (playerId) => {
    try {
        await api.put(`/players/${playerId}`, {
            team_id: null
        })
        await fetchData()
    } catch (err) {
        alert(err.response?.data?.message || 'Greška pri uklanjanju igrača')
    }
}
</script>