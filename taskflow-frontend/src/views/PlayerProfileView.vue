<template>
    <div class="max-w-7xl mx-auto p-6">
        <!-- Navigacija Nazad -->
        <button @click="$router.back()" class="mb-6 flex items-center gap-2 text-xs font-semibold text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg transition">
            ← Nazad na Ekipu
        </button>

        <div v-if="loading" class="text-center py-12 text-gray-400">
            Učitavanje profila igrača...
        </div>

        <div v-else-if="player" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kartica Igrača: Profilna Slika i Osnovne Informacije -->
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl text-center flex flex-col items-center">
                <div class="relative w-32 h-32 mb-4">
                    <img
                        :src="player.player_profile?.photo_url || 'https://via.placeholder.com/150/1e293b/64748b?text=Igra%C4%8D'"
                        class="w-full h-full object-cover rounded-full border-4 border-indigo-500/30 shadow-lg"
                        alt="Profilna Slika"
                    />
                    <span class="absolute bottom-1 right-1 bg-indigo-600 text-white font-mono font-extrabold text-xs px-2.5 py-1 rounded-full shadow">
            #{{ player.player_profile?.jersey_number || '-' }}
          </span>
                </div>

                <h3 class="text-2xl font-bold text-white mb-1">{{ player.name }}</h3>
                <p class="text-xs text-gray-400 mb-4">{{ player.email }}</p>

                <div class="flex flex-wrap justify-center gap-2 mb-6">
          <span class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 text-xs font-bold uppercase px-3 py-1 rounded-full">
            {{ player.player_profile?.primary_position || 'CM' }}
          </span>
                    <span class="bg-purple-600/20 text-purple-400 border border-purple-500/30 text-xs font-bold uppercase px-3 py-1 rounded-full">
            {{ player.player_profile?.category || 'Seniori' }}
          </span>
                </div>

                <!-- Fizički Status / Napomena -->
                <div class="w-full bg-gray-900/60 p-4 rounded-xl border border-gray-700/50 text-left">
                    <div class="text-[10px] font-bold uppercase text-gray-400 mb-1">Status Fizičke Spreme</div>
                    <div class="text-sm font-semibold text-emerald-400">
                        {{ player.player_profile?.fitness_status || 'Spreman za utakmicu' }}
                    </div>
                </div>
            </div>

            <!-- Statistika i Treninzi -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Kartice Statistike -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-gray-800 border border-gray-700/80 p-4 rounded-xl text-center shadow-lg">
                        <div class="text-2xl font-black text-blue-400 mb-1">
                            {{ player.player_profile?.trainings_attended || 0 }}
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Treninga</div>
                    </div>

                    <div class="bg-gray-800 border border-gray-700/80 p-4 rounded-xl text-center shadow-lg">
                        <div class="text-2xl font-black text-emerald-400 mb-1">
                            {{ player.player_profile?.matches_played || 0 }}
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Utakmica</div>
                    </div>

                    <div class="bg-gray-800 border border-gray-700/80 p-4 rounded-xl text-center shadow-lg">
                        <div class="text-2xl font-black text-yellow-400 mb-1">
                            {{ player.player_profile?.goals || 0 }}
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Golova</div>
                    </div>

                    <div class="bg-gray-800 border border-gray-700/80 p-4 rounded-xl text-center shadow-lg">
                        <div class="text-2xl font-black text-purple-400 mb-1">
                            {{ player.player_profile?.assists || 0 }}
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Asistencija</div>
                    </div>
                </div>

                <!-- Evidencija Treninga i Napomena -->
                <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6 shadow-xl">
                    <h4 class="text-lg font-bold text-white mb-4">Detaljni Podaci Profila</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">
                            <span class="text-gray-400 block mb-1">Jača noga:</span>
                            <span class="text-white font-semibold uppercase">{{ player.player_profile?.preferred_foot || 'Desna' }}</span>
                        </div>

                        <div class="bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">
                            <span class="text-gray-400 block mb-1">Datum rođenja:</span>
                            <span class="text-white font-semibold">{{ player.player_profile?.date_of_birth || 'Nije uneto' }}</span>
                        </div>

                        <div class="bg-gray-900/50 p-3 rounded-lg border border-gray-700/50 md:col-span-2">
                            <span class="text-gray-400 block mb-1">Medicinske napomene / Povrede:</span>
                            <span class="text-gray-200">{{ player.player_profile?.medical_notes || 'Nema zabeleženih medicinskih napomena.' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const player = ref(null)
const loading = ref(true)

onMounted(async () => {
    try {
        const res = await api.get(`/users/${route.params.id}`)
        player.value = res.data.data || res.data
    } catch (err) {
        alert('Greška pri učitavanju profila igrača.')
    } finally {
        loading.value = false
    }
})
</script>