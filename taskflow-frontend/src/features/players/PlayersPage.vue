<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Zaglavlje -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Registar Igrača Kluba</h1>
                <p class="text-xs text-gray-400 mt-0.5">Pregled i upravljanje igračkim kadrom akademije</p>
            </div>

            <button
                @click="showCreatePlayerModal = true"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 border border-indigo-500/30 cursor-pointer"
            >
                <span>➕</span> DODAJ NOVOG IGRAČA
            </button>
        </div>

        <!-- Pretraga i Filteri -->
        <div class="bg-gray-800/60 p-4 rounded-2xl border border-gray-700/60 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-2.5 text-gray-400 text-xs">🔍</span>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Pretraži po imenu, poziciji..."
                    class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-9 pr-4 py-2 text-xs text-white outline-none focus:border-indigo-500 transition"
                />
            </div>

            <select
                v-model="selectedSeniority"
                class="bg-gray-900 border border-gray-700 text-white text-xs font-bold rounded-xl px-3 py-2 outline-none focus:border-indigo-500 cursor-pointer"
            >
                <option value="all">Svi nivoi senioriteta</option>
                <option value="Seniori">Seniori</option>
                <option value="U19">U19 (Omladinci)</option>
                <option value="U17">U17 (Kadeti)</option>
                <option value="U15">U15 (Pioniri)</option>
            </select>
        </div>

        <!-- Tabela Igrača -->
        <PlayersTable :players="filteredPlayers" @delete="confirmDelete" />

        <!-- MODAL: Dodaj Novog Igrača -->
        <CreatePlayerModal
            v-if="showCreatePlayerModal"
            :form="newPlayer"
            :teams="teams"
            :is-submitting="isSubmitting"
            @close="showCreatePlayerModal = false"
            @submit="onCreatePlayer"
        />

        <!-- MODAL: Potvrda Brisanja -->
        <DeletePlayerModal
            v-if="playerToDelete"
            :player-name="playerToDelete.name"
            :is-deleting="isDeleting"
            @close="playerToDelete = null"
            @confirm="onDeletePlayer"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import PlayersTable from './components/PlayersTable.vue'
import CreatePlayerModal from './components/CreatePlayerModal.vue'
import DeletePlayerModal from './components/DeletePlayerModal.vue'
import { usePlayersRegistry } from './composables/usePlayersRegistry'

const {
    teams,
    searchQuery,
    selectedSeniority,
    isSubmitting,
    isDeleting,
    newPlayer,
    fetchData,
    filteredPlayers,
    handleCreatePlayer,
    handleDeletePlayer
} = usePlayersRegistry()

const showCreatePlayerModal = ref(false)
const playerToDelete = ref(null)

onMounted(fetchData)

const onCreatePlayer = (photoFile) => {
    return handleCreatePlayer(photoFile, {
        onSuccess: () => { showCreatePlayerModal.value = false }
    })
}

const confirmDelete = (player) => {
    playerToDelete.value = player
}

const onDeletePlayer = () => {
    return handleDeletePlayer(playerToDelete.value, {
        onSuccess: () => { playerToDelete.value = null }
    })
}
</script>
