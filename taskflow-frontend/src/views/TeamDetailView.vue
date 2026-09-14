<template>
    <!-- Modal za Izmenu Učinka i Senioriteta Igrača -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold text-white mb-1">Ažuriraj Učinak i Status</h3>
            <p class="text-xs text-gray-400 mb-4">{{ selectedPlayer?.name }}</p>

            <form @submit.prevent="handleSaveStats" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Kategorija</label>
                        <select v-model="statsForm.category" class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-xs outline-none">
                            <option value="seniori">Seniori</option>
                            <option value="u19">U19 (Omladinci)</option>
                            <option value="u17">U17 (Kadeti)</option>
                            <option value="u15">U15 (Pioniri)</option>
                            <option value="u13">U13 (Mlađi pioniri)</option>
                            <option value="u11">U11 (Petlići)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Senioritet</label>
                        <select v-model="statsForm.seniority" class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-xs outline-none">
                            <option value="senior">Prvi Tim (Senior)</option>
                            <option value="youth">Omladinski Pogon (Youth)</option>
                            <option value="academy">Škola Fudbala (Academy)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Odrađeni Treninzi</label>
                        <input
                            v-model.number="statsForm.trainings_attended"
                            type="number"
                            min="0"
                            class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Odigrane Utakmice</label>
                        <input
                            v-model.number="statsForm.matches_played"
                            type="number"
                            min="0"
                            class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Postignuti Golovi</label>
                        <input
                            v-model.number="statsForm.goals"
                            type="number"
                            min="0"
                            class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Asistencije</label>
                        <input
                            v-model.number="statsForm.assists"
                            type="number"
                            min="0"
                            class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
                        />
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Odustani</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded font-medium">Sačuvaj Učinak</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useTeamStore } from '../stores/team'
import api from '../services/api'

const route = useRoute()
const teamStore = useTeamStore()

// Reaktivna stanja za modal i izabranog igrača
const showEditModal = ref(false)
const selectedPlayer = ref(null)

const statsForm = reactive({
    trainings_attended: 0,
    matches_played: 0,
    goals: 0,
    assists: 0,
    category: 'seniori',
    seniority: 'senior'
})

// Pronalaženje trenutnog tima na osnovu URL parametra /teams/:id
const currentTeam = computed(() => {
    return teamStore.teams.find(t => t.id === parseInt(route.params.id))
})

onMounted(() => {
    if (teamStore.teams.length === 0) {
        teamStore.fetchTeams()
    }
})

// Otvaranje modula i popunjavanje forme postojećim podacima
const openEditModal = (player) => {
    selectedPlayer.value = player
    statsForm.trainings_attended = player.player_profile?.trainings_attended || 0
    statsForm.matches_played = player.player_profile?.matches_played || 0
    statsForm.goals = player.player_profile?.goals || 0
    statsForm.assists = player.player_profile?.assists || 0
    statsForm.category = player.player_profile?.category || 'seniori'
    statsForm.seniority = player.player_profile?.seniority || 'senior'
    showEditModal.value = true
}

// Slanje ažuriranih podataka na API endpoint
const handleSaveStats = async () => {
    if (!selectedPlayer.value) return

    try {
        await api.put(`/users/${selectedPlayer.value.id}/stats`, statsForm)
        showEditModal.value = false
        await teamStore.fetchTeams() // Osvežava podatke tima u lokalnom store-u
    } catch (err) {
        alert(err.response?.data?.message || 'Greška pri čuvanju statistike')
    }
}
</script>