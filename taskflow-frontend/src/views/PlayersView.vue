<template>
  <div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-white">Registar Igrača Kluba</h2>
        <p class="text-sm text-gray-400 mt-1">Pregled igračkog kadra, kategorija i fizičke spreme</p>
      </div>
    </div>

    <!-- Filteri po kategoriji i senioritetu -->
    <div class="flex flex-wrap gap-4 mb-6 bg-gray-800/60 p-4 rounded-xl border border-gray-700">
      <div>
        <label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Kategorija</label>
        <select v-model="selectedCategory" class="bg-gray-900 text-white text-xs border border-gray-700 rounded-lg p-2 outline-none">
          <option value="all">Sve kategorije</option>
          <option value="seniori">Seniori</option>
          <option value="u19">U19 (Omladinci)</option>
          <option value="u17">U17 (Kadeti)</option>
          <option value="u15">U15 (Pioniri)</option>
          <option value="u13">U13 (Mlađi pioniri)</option>
          <option value="u11">U11 (Petlići)</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-400 uppercase mb-1">Senioritet</label>
        <select v-model="selectedSeniority" class="bg-gray-900 text-white text-xs border border-gray-700 rounded-lg p-2 outline-none">
          <option value="all">Svi nivoi</option>
          <option value="senior">Prvi Tim (Senior)</option>
          <option value="youth">Omladinski Pogon (Youth)</option>
          <option value="academy">Škola Fudbala (Academy)</option>
        </select>
      </div>
    </div>

    <!-- Tabela Igrača -->
    <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-900/60 border-b border-gray-700 text-gray-400 uppercase text-xs">
            <th class="py-3.5 px-6">Broj</th>
            <th class="py-3.5 px-6">Ime i Prezime</th>
            <th class="py-3.5 px-6">Pozicija</th>
            <th class="py-3.5 px-6">Kategorija</th>
            <th class="py-3.5 px-6">Senioritet</th>
            <th class="py-3.5 px-6">Status Spreme</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-700/50 text-sm">
          <tr v-for="user in filteredPlayers" :key="user.id" class="hover:bg-gray-700/30 transition">
            <td class="py-4 px-6 text-indigo-400 font-bold">
              #{{ user.player_profile?.jersey_number || '-' }}
            </td>
            <td class="py-4 px-6 font-semibold text-white">
              {{ user.name }}
            </td>
            <td class="py-4 px-6 font-mono text-gray-300">
              {{ user.player_profile?.primary_position || 'CM' }}
            </td>
            <td class="py-4 px-6 uppercase text-xs font-bold text-indigo-300">
              {{ user.player_profile?.category || 'Seniori' }}
            </td>
            <td class="py-4 px-6">
              <span class="bg-gray-700 text-gray-300 text-xs px-2.5 py-1 rounded-full capitalize">
                {{ user.player_profile?.seniority || 'senior' }}
              </span>
            </td>
            <td class="py-4 px-6">
              <span :class="[
                'text-xs font-bold px-2.5 py-1 rounded-full border',
                user.player_profile?.fitness_status === 'injured' ? 'bg-red-500/20 text-red-400 border-red-500/30' :
                user.player_profile?.fitness_status === 'rehab' ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' :
                'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
              ]">
                {{ fitnessLabel(user.player_profile?.fitness_status) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useUserStore } from '../stores/user'

const userStore = useUserStore()
const selectedCategory = ref('all')
const selectedSeniority = ref('all')

onMounted(() => {
  userStore.fetchUsers()
})

const filteredPlayers = computed(() => {
  return userStore.users.filter(user => {
    const profile = user.player_profile
    const matchCategory = selectedCategory.value === 'all' || profile?.category === selectedCategory.value
    const matchSeniority = selectedSeniority.value === 'all' || profile?.seniority === selectedSeniority.value
    return matchCategory && matchSeniority
  })
})

const fitnessLabel = (status) => {
  const labels = { fit: 'Spreman', injured: 'Povređen', rehab: 'Oporavak', absent: 'Odsutan' }
  return labels[status] || 'Spreman'
}
</script>