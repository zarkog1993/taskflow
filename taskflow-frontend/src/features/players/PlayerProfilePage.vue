<template>
    <div v-if="player" class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Gornja traka: Nazad i Izmeni -->
        <div class="flex justify-between items-center bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <router-link to="/players" class="px-4 py-2 bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl transition flex items-center gap-2">
                ← Nazad na Registar
            </router-link>

            <button @click="openEditModal" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition cursor-pointer flex items-center gap-2">
                ✏️ Izmeni Podatke Igrača
            </button>
        </div>

        <!-- Statistički Sažetak na Vrhu -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 text-center shadow-lg">
                <div class="text-2xl font-black text-indigo-400 font-mono">{{ getStat('trainings_attended') }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Treninga</div>
            </div>
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 text-center shadow-lg">
                <div class="text-2xl font-black text-emerald-400 font-mono">{{ getStat('matches_played') }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Utakmica</div>
            </div>
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 text-center shadow-lg">
                <div class="text-2xl font-black text-amber-400 font-mono">{{ getStat('goals') }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Golova</div>
            </div>
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 text-center shadow-lg">
                <div class="text-2xl font-black text-sky-400 font-mono">{{ getStat('assists') }}</div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Asistencija</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEVA KOLONA: Osnovne Kartice Igrača -->
            <div class="bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl space-y-6">
                <div class="flex flex-col items-center text-center">
                    <div class="w-32 h-32 rounded-full bg-indigo-950 border-4 border-indigo-600/50 shadow-2xl overflow-hidden relative flex items-center justify-center font-black text-3xl text-indigo-300">
                        <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover" />
                        <span v-else>#{{ player.jersey_number || '-' }}</span>

                        <span class="absolute bottom-1 right-1 bg-emerald-500 text-gray-950 text-[10px] font-black px-2 py-0.5 rounded-full border-2 border-gray-900 shadow">
          #{{ player.jersey_number || '?' }}
        </span>
                    </div>

                    <h2 class="text-2xl font-black text-white mt-4">{{ player.name }}</h2>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ player.email || 'Nema upisan email' }}</p>

                    <div class="flex items-center gap-2 mt-3">
        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-indigo-950 text-indigo-400 border border-indigo-800 uppercase">
          {{ player.primary_position }}
        </span>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-gray-900 text-gray-300 border border-gray-700 uppercase">
          {{ player.seniority || 'Seniori' }}
        </span>
                    </div>
                </div>

                <div class="border-t border-gray-700/60 pt-4 space-y-3 text-xs">
                    <h3 class="font-bold uppercase text-gray-400 tracking-wider text-[10px]">Detaljni Podaci Profila</h3>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-gray-900/80 p-3 rounded-xl border border-gray-700/60">
                            <span class="text-gray-400 text-[10px] block font-bold uppercase">Visina:</span>
                            <span class="text-white font-mono font-bold">{{ player.height ? player.height + ' cm' : 'Nije uneto' }}</span>
                        </div>
                        <div class="bg-gray-900/80 p-3 rounded-xl border border-gray-700/60">
                            <span class="text-gray-400 text-[10px] block font-bold uppercase">Težina:</span>
                            <span class="text-white font-mono font-bold">{{ player.weight ? player.weight + ' kg' : 'Nije uneto' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-gray-900/80 p-3 rounded-xl border border-gray-700/60">
                            <span class="text-gray-400 text-[10px] block font-bold uppercase">Jača noga:</span>
                            <span class="text-white font-bold capitalize">{{ formatFoot(player.preferred_foot) }}</span>
                        </div>
                        <div class="bg-gray-900/80 p-3 rounded-xl border border-gray-700/60">
                            <span class="text-gray-400 text-[10px] block font-bold uppercase">Datum rođenja:</span>
                            <span class="text-white font-mono font-bold">{{ player.date_of_birth || 'Nije uneto' }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-900/80 p-3.5 rounded-xl border border-gray-700/60 space-y-1">
                        <span class="text-indigo-400 text-[10px] block font-bold uppercase">📝 Beleška Trenera:</span>
                        <p class="text-gray-300 italic leading-relaxed">{{ player.coach_notes || 'Nema zabeleženih opaski trenera.' }}</p>
                    </div>
                </div>
            </div>

            <!-- DESNA KOLONA: Mapa Pozicije na Terenu -->
            <PlayerPositionMap :position="player.primary_position" />
        </div>

        <!-- MODAL FORMA ZA IZMENU PODATAKA IGRAČA -->
        <EditPlayerModal
            v-if="showEditModal"
            :form="editForm"
            :current-photo-url="player.photo_url"
            :is-submitting="isSubmitting"
            @close="showEditModal = false"
            @submit="onUpdatePlayer"
        />
    </div>

    <div v-else class="text-center py-20 text-gray-400">
        <div class="animate-spin text-2xl mb-2">⏳</div>
        <p class="text-xs">Učitavanje profila igrača...</p>
    </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import PlayerPositionMap from './components/PlayerPositionMap.vue'
import EditPlayerModal from './components/EditPlayerModal.vue'
import { usePlayerProfile } from './composables/usePlayerProfile'

const route = useRoute()

const {
    player,
    showEditModal,
    isSubmitting,
    editForm,
    fetchPlayerProfile,
    getStat,
    openEditModal,
    handleUpdatePlayer,
    formatFoot
} = usePlayerProfile(route.params.id)

onMounted(fetchPlayerProfile)

const onUpdatePlayer = (photoFile) => {
    return handleUpdatePlayer(photoFile, {
        onSuccess: () => { showEditModal.value = false }
    })
}
</script>
