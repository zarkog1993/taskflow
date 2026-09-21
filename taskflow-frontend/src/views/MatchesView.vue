<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Zaglavlje -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-white">Utakmice & Zapisnici</h1>
                <p class="text-xs text-gray-400">Pregled zakazanih mečeva, sastava i statistike igrača</p>
            </div>
            <button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-lg cursor-pointer">
                + Zakaži Utakmicu
            </button>
        </div>

        <!-- Tabovi -->
        <div class="flex space-x-2 border-b border-gray-700/60">
            <button @click="activeTab = 'upcoming'" :class="activeTab === 'upcoming' ? 'border-indigo-500 text-indigo-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200'" class="py-2.5 px-4 border-b-2 text-xs transition cursor-pointer">
                ⚡ Predstojeće Utakmice ({{ upcomingMatches.length }})
            </button>
            <button @click="activeTab = 'completed'" :class="activeTab === 'completed' ? 'border-indigo-500 text-indigo-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200'" class="py-2.5 px-4 border-b-2 text-xs transition cursor-pointer">
                ✅ Odigrane Utakmice ({{ completedMatches.length }})
            </button>
        </div>

        <!-- Kartice -->
        <div v-if="filteredMatches.length" class="space-y-4">
            <MatchCard
                v-for="match in filteredMatches"
                :key="match.id"
                :match="match"
                @open-stats="openStatsModal"
                @delete="handleDeleteMatch"
            />
        </div>
        <div v-else class="text-center py-12 bg-gray-800/40 border border-gray-700/50 rounded-2xl">
            <p class="text-sm text-gray-400 italic">Nema utakmica u ovoj kategoriji.</p>
        </div>

        <!-- Modali -->
        <MatchStatsModal v-if="selectedMatch" :match="selectedMatch" :stats-form="statsForm" :modal-tab="modalTab" @close="selectedMatch = null" @save="saveMatchStats" />
        <DeleteMatchModal
            v-if="matchToDelete"
            :match-title="matchToDelete.opponent || 'Utakmica'"
            :is-deleting="isDeleting"
            @close="matchToDelete = null"
            @confirm="confirmDeleteMatch"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../services/api";
import MatchCard from "../components/matches/MatchCard.vue";
import MatchStatsModal from "../components/matches/MatchStatsModal.vue";
import DeleteMatchModal from "../components/matches/DeleteMatchModal.vue";
import { useMatchStats } from "../composables/useMatchStats";

const matches = ref([]);
const activeTab = ref("upcoming");

const fetchMatches = async () => {
    try {
        const res = await api.get("/matches");
        matches.value = res.data.data;
    } catch (err) {
        console.error("Greška pri učitavanju utakmica:", err);
    }
};

const { selectedMatch, modalTab, statsForm, openStatsModal, saveMatchStats } = useMatchStats(fetchMatches);

const upcomingMatches = computed(() => matches.value.filter((m) => m.status === "scheduled"));
const completedMatches = computed(() => matches.value.filter((m) => m.status === "completed"));
const filteredMatches = computed(() => activeTab.value === "upcoming" ? upcomingMatches.value : completedMatches.value);
const matchToDelete = ref(null);
const isDeleting = ref(false);

const handleDeleteMatch = (match) => {
    matchToDelete.value = match;
};

const confirmDeleteMatch = async () => {
    if (!matchToDelete.value) return;

    isDeleting.value = true;
    try {
        await api.delete(`/matches/${matchToDelete.value.id}`);
        // Osvežavamo listu mečeva nakon brisanja
        await fetchMatches();
        matchToDelete.value = null;
    } catch (err) {
        console.error("Greška pri brisanju meča:", err);
        alert("Došlo je do greške prilikom brisanja.");
    } finally {
        isDeleting.value = false;
    }
};

onMounted(fetchMatches);
</script>