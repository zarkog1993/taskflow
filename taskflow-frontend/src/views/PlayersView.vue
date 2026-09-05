<template>
  <div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-white">Registar Igrača Kluba</h2>
        <p class="text-sm text-gray-400 mt-1">Pregled igračkog kadra, kategorija i fizičke spreme</p>
      </div>

      <!-- Dugme za dodavanje novog igrača -->
      <button 
        @click="showAddModal = true"
        class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Dodaj Novog Igrača
      </button>
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
      <div v-if="userStore.loading" class="p-8 text-center text-gray-400">
        Učitavanje registra igrača...
      </div>

      <table v-else class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-900/60 border-b border-gray-700 text-gray-400 uppercase text-xs">
            <th class="py-3.5 px-6">Broj</th>
            <th class="py-3.5 px-6">Ime i Prezime</th>
            <th class="py-3.5 px-6">Email</th>
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
            <td class="py-4 px-6 text-gray-400 text-xs">
              {{ user.email }}
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

    <!-- Modal za Dodavanje Novog Igrača -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
      <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold text-white mb-4">Dodaj Novog Igrača u Klub</h3>

        <form @submit.prevent="handleCreatePlayer" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Ime i Prezime</label>
              <input 
                v-model="newPlayer.name" 
                type="text" 
                required 
                placeholder="npr. Marko Marković"
                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none focus:border-indigo-500" 
              />
            </div>
            <div>
              <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Email</label>
              <input 
                v-model="newPlayer.email" 
                type="email" 
                required 
                placeholder="marko@klub.rs"
                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none focus:border-indigo-500" 
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Lozinka</label>
              <input 
                v-model="newPlayer.password" 
                type="password" 
                required
                placeholder="Min 8 karaktera"
                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none focus:border-indigo-500" 
              />
            </div>
            <div>
              <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Broj Dresa</label>
              <input 
                v-model.number="newPlayer.jersey_number" 
                type="number" 
                placeholder="npr. 10"
                class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none focus:border-indigo-500" 
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Pozicija</label>
              <select v-model="newPlayer.primary_position" class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none">
                <option value="GK">Golman (GK)</option>
                <option value="CB">Štoper (CB)</option>
                <option value="LB">Levi Bek (LB)</option>
                <option value="RB">Desni Bek (RB)</option>
                <option value="DM">Zadnji Vezni (DM)</option>
                <option value="CM">Centralni Vezni (CM)</option>
                <option value="AM">Ofanzivni Vezni (AM)</option>
                <option value="LW">Levo Krilo (LW)</option>
                <option value="RW">Desno Krilo (RW)</option>
                <option value="ST">Napadač (ST)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Noga</label>
              <select v-model="newPlayer.preferred_foot" class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none">
                <option value="right">Desna</option>
                <option value="left">Leva</option>
                <option value="both">Obe</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Kategorija</label>
              <select v-model="newPlayer.category" class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none">
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
              <select v-model="newPlayer.seniority" class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none">
                <option value="senior">Prvi Tim (Senior)</option>
                <option value="youth">Omladinski Pogon (Youth)</option>
                <option value="academy">Škola Fudbala (Academy)</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Status Fizičke Spreme</label>
            <select v-model="newPlayer.fitness_status" class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white text-sm outline-none">
              <option value="fit">Spreman</option>
              <option value="injured">Povređen</option>
              <option value="rehab">U Oporavku</option>
              <option value="absent">Odsutan</option>
            </select>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
            <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Odustani</button>
            <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-semibold">Registruj Igrača</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useUserStore } from '../stores/user'
import api from '../services/api'

const userStore = useUserStore()
const selectedCategory = ref('all')
const selectedSeniority = ref('all')
const showAddModal = ref(false)

const newPlayer = reactive({
  name: '',
  email: '',
  password: '',
  jersey_number: '',
  primary_position: '',
  preferred_foot: '',
  category: '',
  seniority: '',
  fitness_status: ''
})

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

const handleCreatePlayer = async () => {
  try {
    await api.post('/users', {
      name: newPlayer.name,
      email: newPlayer.email,
      password: newPlayer.password,
      password_confirmation: newPlayer.password, // Dodato polje da prođe validaciju
      player_profile: {
        jersey_number: newPlayer.jersey_number,
        primary_position: newPlayer.primary_position,
        preferred_foot: newPlayer.preferred_foot,
        category: newPlayer.category,
        seniority: newPlayer.seniority,
        fitness_status: newPlayer.fitness_status
      }
    })

    showAddModal.value = false
    userStore.fetchUsers()

    // Reset forme
    newPlayer.name = ''
    newPlayer.email = ''
    newPlayer.jersey_number = ''
    newPlayer.password = ''
    newPlayer.category = ''
    newPlayer.seniority = ''
    newPlayer.primary_position = ''
    newPlayer.preferred_foot = ''
    newPlayer.fitness_status = ''
  } catch (err) {
    alert(err.response?.data?.message || 'Greška pri kreiranju igrača')
  }
}

const fitnessLabel = (status) => {
  const labels = { fit: 'Spreman', injured: 'Povređen', rehab: 'Oporavak', absent: 'Odsutan' }
  return labels[status] || 'Spreman'
}
</script>