<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const teams = ref([])
const availableUsers = ref([])
const subscription = ref(null)

const showAssignModal = ref(false)
const selectedTeam = ref(null)
const selectedUserIds = ref([])

const token = localStorage.getItem('token')
const axiosConfig = { headers: { Authorization: `Bearer ${token}` } }

const loadData = async () => {
    try {
        const [teamsRes, usersRes] = await Promise.all([
            axios.get('/api/teams', axiosConfig),
            axios.get('/api/users', axiosConfig)
        ])
        teams.value = teamsRes.data.data || teamsRes.data
        availableUsers.value = usersRes.data.data || usersRes.data
    } catch (err) {
        console.error('Greška pri učitavanju timova:', err)
    }
}

const openAssignModal = (team) => {
    selectedTeam.value = team
    // Označi već dodeljene igrače
    selectedUserIds.value = team.players ? team.players.map(p => p.id) : []
    showAssignModal.value = true
}

const assignMembers = async () => {
    if (!selectedTeam.value) return
    try {
        await axios.post(
            `/api/teams/${selectedTeam.value.id}/members`,
            { user_ids: selectedUserIds.value },
            axiosConfig
        )
        showAssignModal.value = false
        await loadData() // Osveži podatke
    } catch (err) {
        alert(err.response?.data?.message || 'Greška pri dodeljivanju članova.')
    }
}

onMounted(loadData)
</script>

<template>
    <div class="p-6 max-w-7xl mx-auto space-y-6 text-white">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Upravljanje Timovima i Članovima</h1>
        </div>

        <!-- LISTA TIMOVA SA DODELJENIM KORISNICIMA -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                v-for="team in teams"
                :key="team.id"
                class="bg-slate-800 border border-slate-700 rounded-xl p-5 space-y-4"
            >
                <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                    <div>
                        <h3 class="font-bold text-lg text-emerald-400">{{ team.name }}</h3>
                        <span class="text-xs text-slate-400 font-mono uppercase">{{ team.age_group }}</span>
                    </div>
                    <button
                        @click="openAssignModal(team)"
                        class="px-3 py-1.5 text-xs font-semibold bg-emerald-500 hover:bg-emerald-400 text-slate-950 rounded-lg transition"
                    >
                        + Dodeli Članove
                    </button>
                </div>

                <!-- ČLANOVI TIMA -->
                <div>
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">
                        Članovi tima ({{ team.players?.length || 0 }})
                    </h4>
                    <div v-if="team.players && team.players.length" class="space-y-2">
                        <div
                            v-for="player in team.players"
                            :key="player.id"
                            class="flex justify-between items-center bg-slate-900 px-3 py-2 rounded-lg text-sm border border-slate-700/50"
                        >
                            <div>
                                <p class="font-medium text-slate-200">{{ player.name }}</p>
                                <p class="text-xs text-slate-400">{{ player.email }}</p>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-slate-500 italic py-2">Nema dodeljenih članova za ovaj tim.</p>
                </div>
            </div>
        </div>

        <!-- MODAL ZA DODELJIVANJE ČLANOVA -->
        <div v-if="showAssignModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 max-w-lg w-full space-y-4">
                <h3 class="text-xl font-bold text-white">Dodeli članove timu: {{ selectedTeam?.name }}</h3>

                <div class="max-h-60 overflow-y-auto space-y-2 pr-2">
                    <label
                        v-for="user in availableUsers"
                        :key="user.id"
                        class="flex items-center justify-between p-3 bg-slate-900 border border-slate-700 rounded-lg cursor-pointer hover:border-slate-500 transition"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                :value="user.id"
                                v-model="selectedUserIds"
                                class="w-4 h-4 text-emerald-500 rounded bg-slate-800 border-slate-600 focus:ring-emerald-500"
                            />
                            <div>
                                <p class="text-sm font-medium text-white">{{ user.name }}</p>
                                <p class="text-xs text-slate-400">{{ user.email }}</p>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                    <button
                        @click="showAssignModal = false"
                        class="px-4 py-2 text-sm text-slate-400 hover:text-white"
                    >
                        Otkaži
                    </button>
                    <button
                        @click="assignMembers"
                        class="px-4 py-2 text-sm font-semibold bg-emerald-400 text-slate-950 rounded-lg hover:bg-emerald-300"
                    >
                        Sačuvaj Promene
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>