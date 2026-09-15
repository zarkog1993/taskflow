<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
        <!-- Navigacija i Zaglavlje Ekipe -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/60 p-6 rounded-2xl border border-gray-700/60 backdrop-blur-md shadow-xl">
            <div class="flex items-center space-x-4">
                <router-link
                    to="/teams"
                    class="p-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white rounded-xl transition text-xs font-semibold flex items-center gap-2 border border-gray-700"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Nazad na Ekipe
                </router-link>

                <div v-if="team">
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-black text-white tracking-tight">{{ team.name }}</h2>
                        <span class="text-xs font-mono font-bold uppercase text-indigo-400 bg-indigo-600/20 px-3 py-1 rounded-lg border border-indigo-500/30">
              {{ team.age_group }}
            </span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Ukupno {{ team.members?.length || 0 }} igrača u sastavu</p>
                </div>
            </div>
        </div>

        <!-- Status Učitavanja -->
        <div v-if="loading" class="text-center py-20 text-gray-400 font-medium flex justify-center items-center gap-3">
            <svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Učitavanje igrača ekipe...
        </div>

        <!-- Grid Kartica Igrača -->
        <div v-else-if="team" class="space-y-6">
            <div v-if="team.members && team.members.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <div
                    v-for="member in team.members"
                    :key="member.id"
                    class="bg-gray-800/80 hover:bg-gray-800 border border-gray-700/80 hover:border-indigo-500/50 rounded-2xl p-5 shadow-xl transition-all duration-300 flex flex-col justify-between group relative overflow-hidden"
                >
                    <!-- Gornji Akcenat sa Brojem Dres-a -->
                    <div class="flex justify-between items-start mb-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 bg-indigo-950/80 px-2.5 py-1 rounded-md border border-indigo-800/50">
              {{ member.player_profile?.primary_position || 'CM' }}
            </span>
                        <span class="text-xs font-mono font-extrabold text-white bg-indigo-600 px-2.5 py-1 rounded-lg shadow-md">
              #{{ member.player_profile?.jersey_number || '-' }}
            </span>
                    </div>

                    <!-- Slika, Ime i Klik ka Detaljima -->
                    <div
                        @click="$router.push(`/players/${member.id}`)"
                        class="cursor-pointer text-center space-y-3 my-2 group-hover:transform group-hover:-translate-y-1 transition duration-200"
                    >
                        <div class="relative w-24 h-24 mx-auto">
                            <img
                                :src="member.player_profile?.photo_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(member.name)}&background=312e81&color=c7d2fe&size=128`"
                                class="w-full h-full object-cover rounded-full border-2 border-indigo-500/30 group-hover:border-indigo-500 shadow-lg transition"
                                :alt="member.name"
                            />
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-white group-hover:text-indigo-300 transition-colors leading-snug">
                                {{ member.name }}
                            </h3>
                            <p class="text-[11px] text-gray-400 mt-0.5 font-mono">
                                {{ member.player_profile?.category || 'Seniori' }}
                            </p>
                        </div>
                    </div>

                    <!-- Mini Statistika Igrača na Kartici -->
                    <div class="grid grid-cols-3 gap-2 my-4 bg-gray-900/60 p-2.5 rounded-xl border border-gray-700/50 text-center text-xs">
                        <div>
                            <div class="text-[9px] font-bold uppercase text-gray-400">Utakmice</div>
                            <div class="font-bold text-emerald-400 mt-0.5">{{ member.player_profile?.matches_played || 0 }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold uppercase text-gray-400">Golovi</div>
                            <div class="font-bold text-yellow-400 mt-0.5">{{ member.player_profile?.goals || 0 }}</div>
                        </div>
                        <div>
                            <div class="text-[9px] font-bold uppercase text-gray-400">Asist.</div>
                            <div class="font-bold text-purple-400 mt-0.5">{{ member.player_profile?.assists || 0 }}</div>
                        </div>
                    </div>

                    <!-- Akcija: Izmena Učinka -->
                    <button
                        @click="openEditModal(member)"
                        class="w-full py-2 px-3 bg-gray-700/60 hover:bg-gray-700 text-gray-200 text-xs font-semibold rounded-xl transition border border-gray-600/50 flex items-center justify-center gap-1.5"
                    >
                        ✏️ Izmeni Učinak
                    </button>
                </div>
            </div>

            <!-- Prazan Sastav -->
            <div v-else class="text-center py-16 bg-gray-800/40 rounded-2xl border border-dashed border-gray-700/60">
                <p class="text-sm text-gray-400 italic">Ova ekipa trenutno nema dodeljenih igrača.</p>
            </div>
        </div>

        <!-- Modal za Izmenu Učinka i Statusa Igrača -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-1">Ažuriraj Učinak i Status</h3>
                <p class="text-xs text-indigo-400 font-semibold mb-4">{{ selectedPlayer?.name }}</p>

                <form @submit.prevent="handleSaveStats" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Kategorija</label>
                            <select v-model="statsForm.category" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white text-xs outline-none">
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
                            <select v-model="statsForm.seniority" class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white text-xs outline-none">
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
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white outline-none focus:border-indigo-500 text-xs"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Odigrane Utakmice</label>
                            <input
                                v-model.number="statsForm.matches_played"
                                type="number"
                                min="0"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white outline-none focus:border-indigo-500 text-xs"
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
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white outline-none focus:border-indigo-500 text-xs"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Asistencije</label>
                            <input
                                v-model.number="statsForm.assists"
                                type="number"
                                min="0"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl p-2.5 text-white outline-none focus:border-indigo-500 text-xs"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs text-gray-400 hover:text-white font-semibold">Odustani</button>
                        <button type="submit" class="px-5 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold transition shadow-lg">Sačuvaj Učinak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const team = ref(null)
const loading = ref(true)
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

const loadTeamData = async () => {
    loading.value = true
    try {
        const res = await api.get('/teams')
        const teams = res.data.data || res.data
        team.value = teams.find(t => String(t.id) === String(route.params.id))
    } catch (err) {
        console.error('Greška pri učitavanju tima:', err)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    loadTeamData()
})

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

const handleSaveStats = async () => {
    if (!selectedPlayer.value) return

    try {
        await api.put(`/users/${selectedPlayer.value.id}/stats`, statsForm)
        showEditModal.value = false
        await loadTeamData()
    } catch (err) {
        alert(err.response?.data?.message || 'Greška pri čuvanju statistike')
    }
}
</script>