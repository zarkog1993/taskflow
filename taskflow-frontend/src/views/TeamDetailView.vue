<template>
  <div class="max-w-7xl mx-auto p-6">
    <!-- Zaglavlje -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-4">
        <router-link to="/teams" class="p-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg transition">
          ← Nazad
        </router-link>
        <div>
          <h2 class="text-2xl font-bold text-white">{{ currentTeam?.name || 'Učitavanje ekipe...' }}</h2>
          <span class="text-xs font-mono font-bold uppercase text-indigo-400 bg-indigo-600/20 px-2.5 py-0.5 rounded border border-indigo-500/30">
            {{ currentTeam?.age_group }}
          </span>
        </div>
      </div>
    </div>

    <!-- Tabela Igrača sa Statistikom -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-900/60 border-b border-gray-700 text-gray-400 uppercase text-xs">
            <th class="py-3.5 px-6">Broj</th>
            <th class="py-3.5 px-6">Igrač</th>
            <th class="py-3.5 px-6">Pozicija</th>
            <th class="py-3.5 px-6 text-center">Treninzi</th>
            <th class="py-3.5 px-6 text-center">Utakmice</th>
            <th class="py-3.5 px-6 text-center">Golovi</th>
            <th class="py-3.5 px-6 text-center">Asistencije</th>
            <th class="py-3.5 px-6 text-right">Akcije</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-700/50 text-sm">
          <tr v-for="member in currentTeam?.members" :key="member.id" class="hover:bg-gray-700/30 transition">
            <td class="py-4 px-6 text-indigo-400 font-bold">
              #{{ member.player_profile?.jersey_number || '-' }}
            </td>
            <td class="py-4 px-6 font-semibold text-white">
              {{ member.name }}
            </td>
            <td class="py-4 px-6 font-mono text-gray-300">
              {{ member.player_profile?.primary_position || 'CM' }}
            </td>
            <td class="py-4 px-6 text-center font-semibold text-blue-400">
              {{ member.player_profile?.trainings_attended || 0 }}
            </td>
            <td class="py-4 px-6 text-center font-semibold text-emerald-400">
              {{ member.player_profile?.matches_played || 0 }}
            </td>
            <td class="py-4 px-6 text-center font-semibold text-yellow-400">
              {{ member.player_profile?.goals || 0 }}
            </td>
            <td class="py-4 px-6 text-center font-semibold text-purple-400">
              {{ member.player_profile?.assists || 0 }}
            </td>
            <td class="py-4 px-6 text-right">
              <button 
                @click="openEditModal(member)"
                class="px-3 py-1 bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 border border-indigo-500/30 rounded text-xs font-medium transition"
              >
                Izmeni Učinak
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal za Izmenu Učinka Igrača -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
      <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-bold text-white mb-1">Ažuriraj Učinak</h3>
        <p class="text-xs text-gray-400 mb-4">{{ selectedPlayer?.name }}</p>

        <form @submit.prevent="handleSaveStats" class="space-y-4">
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
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useTeamStore } from '../stores/team'
import api from '../services/api'

const route = useRoute()
const teamStore = useTeamStore()

const showEditModal = ref(false)
const selectedPlayer = ref(null)

const statsForm = reactive({
  trainings_attended: 0,
  matches_played: 0,
  goals: 0,
  assists: 0
})

const currentTeam = computed(() => {
  return teamStore.teams.find(t => t.id === parseInt(route.params.id))
})

onMounted(() => {
  if (teamStore.teams.length === 0) {
    teamStore.fetchTeams()
  }
})

const openEditModal = (player) => {
  selectedPlayer.value = player
  statsForm.trainings_attended = player.player_profile?.trainings_attended || 0
  statsForm.matches_played = player.player_profile?.matches_played || 0
  statsForm.goals = player.player_profile?.goals || 0
  statsForm.assists = player.player_profile?.assists || 0
  showEditModal.value = true
}

const handleSaveStats = async () => {
  if (!selectedPlayer.value) return

  try {
    await api.put(`/users/${selectedPlayer.value.id}/stats`, statsForm)
    showEditModal.value = false
    await teamStore.fetchTeams() // Osvežava podatke o timu i statistikama
  } catch (err) {
    alert(err.response?.data?.message || 'Greška pri čuvanju statistike')
  }
}
</script>