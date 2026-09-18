<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Zaglavlje i Dugme -->
        <div
            class="flex flex-col sm:flex-row justify-between sm:items-center gap-4"
        >
            <div>
                <h1 class="text-2xl font-black text-white">
                    Utakmice & Zapisnici
                </h1>
                <p class="text-xs text-gray-400">
                    Pregled zakazanih mečeva, sastava i statistike igrača
                </p>
            </div>
            <button
                @click="openCreateModal"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-lg cursor-pointer"
            >
                + Zakaži Utakmicu
            </button>
        </div>

        <!-- Tabovi -->
        <div class="flex space-x-2 border-b border-gray-700/60">
            <button
                @click="activeTab = 'upcoming'"
                :class="
                    activeTab === 'upcoming'
                        ? 'border-indigo-500 text-indigo-400 font-bold'
                        : 'border-transparent text-gray-400 hover:text-gray-200'
                "
                class="py-2.5 px-4 border-b-2 text-xs transition cursor-pointer"
            >
                ⚡ Predstojeće Utakmice ({{ upcomingMatches.length }})
            </button>
            <button
                @click="activeTab = 'completed'"
                :class="
                    activeTab === 'completed'
                        ? 'border-indigo-500 text-indigo-400 font-bold'
                        : 'border-transparent text-gray-400 hover:text-gray-200'
                "
                class="py-2.5 px-4 border-b-2 text-xs transition cursor-pointer"
            >
                ✅ Odigrane Utakmice ({{ completedMatches.length }})
            </button>
        </div>

        <!-- Kartice Utakmica -->
        <div v-if="filteredMatches.length" class="space-y-4">
            <div
                v-for="match in filteredMatches"
                :key="match.id"
                class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-5 shadow-lg space-y-4"
            >
                <div
                    class="flex flex-col md:flex-row justify-between md:items-center gap-4"
                >
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span
                                class="text-[10px] font-bold uppercase text-indigo-400 bg-indigo-950/80 px-2.5 py-0.5 rounded border border-indigo-800/60"
                            >
                                {{ match.team?.name || "Selekcija" }}
                            </span>
                            <span
                                class="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                                :class="
                                    match.is_home
                                        ? 'bg-emerald-950 text-emerald-400 border border-emerald-800'
                                        : 'bg-amber-950 text-amber-400 border border-amber-800'
                                "
                            >
                                {{ match.is_home ? "Domaćin" : "Gost" }}
                            </span>
                        </div>

                        <!-- Rezultat ili Vs -->
                        <h3 class="text-xl font-black text-white">
                            {{ match.is_home ? "Rudar" : match.opponent }}
                            <span
                                v-if="match.status === 'completed'"
                                class="text-indigo-400 font-mono px-2"
                            >
                                {{ match.home_score }} : {{ match.away_score }}
                            </span>
                            <span v-else class="text-gray-400 font-normal px-1">
                                vs
                            </span>
                            {{ match.is_home ? match.opponent : "Rudar" }}
                        </h3>

                        <div
                            class="flex items-center gap-4 text-xs text-gray-400 mt-2"
                        >
                            <span>📅 {{ formatDate(match.scheduled_at) }}</span>
                            <span>📍 {{ match.location || "Nije uneto" }}</span>
                        </div>
                    </div>

                    <button
                        @click="openStatsModal(match)"
                        class="bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 border border-indigo-500/30 text-xs font-bold px-4 py-2.5 rounded-xl transition cursor-pointer flex items-center gap-2"
                    >
                        📋 Detalji & Zapisnik
                    </button>
                </div>

                <!-- Pregled Učinka (Ako je odigrano) -->
                <div
                    v-if="match.status === 'completed'"
                    class="pt-3 border-t border-gray-700/50 flex flex-wrap gap-6 text-xs"
                >
                    <div>
                        <span class="text-gray-400 font-bold">⚽ Strelci:</span>
                        <span class="text-white ml-1.5 font-medium">
                            {{
                                getScorersText(match) || "Nema upisanih golova"
                            }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 font-bold"
                        >🎯 Asistenti:</span
                        >
                        <span class="text-white ml-1.5 font-medium">
                            {{
                                getAssistersText(match) ||
                                "Nema upisanih asistencija"
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="text-center py-12 bg-gray-800/40 border border-gray-700/50 rounded-2xl"
        >
            <p class="text-sm text-gray-400 italic">
                Nema utakmica u ovoj kategoriji.
            </p>
        </div>

        <!-- MODAL: DETALJI I ZAPISNIK -->
        <div
            v-if="selectedMatch"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
        >
            <div
                class="bg-gray-800 border border-gray-700 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-5"
            >
                <!-- Zaglavlje Modala -->
                <div
                    class="flex justify-between items-center pb-3 border-b border-gray-700"
                >
                    <div>
                        <h3 class="text-base font-bold text-white">
                            Zapisnik Utakmice
                        </h3>
                        <p class="text-xs text-indigo-400 font-medium">
                            {{ selectedMatch.team?.name }} vs
                            {{ selectedMatch.opponent }}
                        </p>
                    </div>
                    <button
                        @click="selectedMatch = null"
                        class="text-gray-400 hover:text-white text-lg cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <!-- Navigacija Kroz Tabove Modala -->
                <div
                    class="flex space-x-1 bg-gray-900/80 p-1.5 rounded-xl border border-gray-700/60"
                >
                    <button
                        @click="modalTab = 'info'"
                        :class="
                            modalTab === 'info'
                                ? 'bg-indigo-600 text-white font-bold'
                                : 'text-gray-400 hover:text-white'
                        "
                        class="flex-1 py-2 text-xs rounded-lg transition cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <span>📅</span>
                        <span>Info & Status</span>
                    </button>

                    <button
                        @click="modalTab = 'squad'"
                        :class="
                            modalTab === 'squad'
                                ? 'bg-indigo-600 text-white font-bold'
                                : 'text-gray-400 hover:text-white'
                        "
                        class="flex-1 py-2 text-xs rounded-lg transition cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <span>👥</span>
                        <span
                        >Sastav ({{
                                statsForm.players.filter((p) => p.attended)
                                    .length
                            }})</span
                        >
                    </button>

                    <button
                        @click="modalTab = 'stats'"
                        :class="
                            modalTab === 'stats'
                                ? 'bg-indigo-600 text-white font-bold'
                                : 'text-gray-400 hover:text-white'
                        "
                        class="flex-1 py-2 text-xs rounded-lg transition cursor-pointer flex items-center justify-center gap-1.5"
                    >
                        <span>⚽</span>
                        <span>Strelci & Asistencije</span>
                    </button>
                </div>

                <!-- TAB 1: INFO & STATUS UTKMICE -->
                <div v-if="modalTab === 'info'" class="space-y-4 py-2">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase text-gray-400 mb-1"
                            >Status Utakmice</label
                            >
                            <select
                                v-model="statsForm.status"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                            >
                                <option value="scheduled">Zakazana</option>
                                <option value="completed">
                                    Odigrana (Završena)
                                </option>
                                <option value="canceled">Otkazana</option>
                            </select>
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase text-gray-400 mb-1"
                            >Domaći Golovi</label
                            >
                            <input
                                v-model.number="statsForm.home_score"
                                type="number"
                                min="0"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold text-center"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-bold uppercase text-gray-400 mb-1"
                            >Gostujući Golovi</label
                            >
                            <input
                                v-model.number="statsForm.away_score"
                                type="number"
                                min="0"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold text-center"
                            />
                        </div>
                    </div>

                    <div
                        class="p-4 bg-gray-900/60 rounded-xl border border-gray-700/50 space-y-2 text-xs text-gray-300"
                    >
                        <div class="flex justify-between">
                            <span class="text-gray-400">Datum i vreme:</span>
                            <span class="font-bold text-white">{{
                                    formatDate(selectedMatch.scheduled_at)
                                }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Teren / Lokacija:</span>
                            <span class="font-bold text-white">{{
                                    selectedMatch.location || "Nije uneto"
                                }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Uloga:</span>
                            <span class="font-bold text-white">{{
                                    selectedMatch.is_home ? "Domaćin" : "Gost"
                                }}</span>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: SASTAV / PRISUSTVO -->
                <div v-if="modalTab === 'squad'" class="space-y-3">
                    <div class="flex justify-between items-center px-1">
                        <span class="text-xs text-gray-400"
                        >Štikliraj igrače koji su igrali na ovoj
                            utakmici:</span
                        >
                        <button
                            type="button"
                            @click="toggleSelectAllSquad"
                            class="text-[10px] text-indigo-400 hover:underline font-bold cursor-pointer"
                        >
                            {{
                                allSquadSelected ? "Poništi sve" : "Označi sve"
                            }}
                        </button>
                    </div>

                    <div class="max-h-[40vh] overflow-y-auto space-y-1.5 pr-1">
                        <div
                            v-for="player in statsForm.players"
                            :key="player.id"
                            @click="player.attended = !player.attended"
                            class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition select-none"
                            :class="
                                player.attended
                                    ? 'bg-indigo-950/70 border-indigo-700/60'
                                    : 'bg-gray-900/60 border-gray-800'
                            "
                        >
                            <div class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    v-model="player.attended"
                                    class="w-4 h-4 text-indigo-600 rounded bg-gray-800 border-gray-600"
                                    @click.stop
                                />
                                <div>
                                    <p class="text-xs font-bold text-white">
                                        {{ player.name }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">
                                        #{{ player.jersey_number || "-" }} •
                                        {{ player.position || "N/A" }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded"
                                :class="
                                    player.attended
                                        ? 'bg-emerald-950 text-emerald-400 border border-emerald-800'
                                        : 'bg-gray-800 text-gray-500'
                                "
                            >
                                {{ player.attended ? "Igrao" : "Nije igrao" }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: STRELCI I ASISTENTI -->
                <div v-if="modalTab === 'stats'" class="space-y-3">
                    <p class="text-xs text-gray-400 px-1">
                        Unos pojedinačnog Učinka (prikazani su samo igrači koji
                        su igrali):
                    </p>

                    <div
                        v-if="
                            statsForm.players.filter((p) => p.attended).length
                        "
                        class="max-h-[40vh] overflow-y-auto space-y-2 pr-1"
                    >
                        <div
                            v-for="player in statsForm.players.filter(
                                (p) => p.attended,
                            )"
                            :key="player.id"
                            class="flex items-center justify-between bg-gray-900/80 p-3 rounded-xl border border-gray-700/60"
                        >
                            <div>
                                <p class="text-xs font-bold text-white">
                                    {{ player.name }}
                                </p>
                                <p class="text-[10px] text-gray-400">
                                    #{{ player.jersey_number || "-" }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <!-- Golovi -->
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="text-xs"
                                        title="Postignuti Golovi"
                                    >⚽</span
                                    >
                                    <input
                                        type="number"
                                        min="0"
                                        v-model.number="player.goals"
                                        class="w-12 bg-gray-800 border border-gray-700 rounded-lg text-center text-xs py-1.5 text-white font-mono font-bold"
                                    />
                                </div>

                                <!-- Asistencije -->
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs" title="Asistencije"
                                    >🎯</span
                                    >
                                    <input
                                        type="number"
                                        min="0"
                                        v-model.number="player.assists"
                                        class="w-12 bg-gray-800 border border-gray-700 rounded-lg text-center text-xs py-1.5 text-white font-mono font-bold"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="text-center py-8 bg-gray-900/40 rounded-xl border border-gray-800"
                    >
                        <p class="text-xs text-gray-500 italic">
                            Nijedan igrač nije označen kao prisutan. Prvo
                            označite sastav u tabu "Sastav".
                        </p>
                    </div>
                </div>

                <!-- Akcije na dnu -->
                <div
                    class="flex justify-end gap-3 pt-3 border-t border-gray-700"
                >
                    <button
                        @click="selectedMatch = null"
                        class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white cursor-pointer"
                    >
                        Odustani
                    </button>
                    <button
                        @click="saveMatchStats"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2 rounded-xl transition cursor-pointer"
                    >
                        Sačuvaj Zapisnik
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../services/api";

const matches = ref([]);
const activeTab = ref("upcoming");
const selectedMatch = ref(null);

const statsForm = ref({
    status: "scheduled",
    home_score: 0,
    away_score: 0,
    players: [],
});

const upcomingMatches = computed(() =>
    matches.value.filter((m) => m.status === "scheduled"),
);
const completedMatches = computed(() =>
    matches.value.filter((m) => m.status === "completed"),
);
const filteredMatches = computed(() =>
    activeTab.value === "upcoming"
        ? upcomingMatches.value
        : completedMatches.value,
);

const fetchMatches = async () => {
    try {
        const res = await api.get("/matches");
        matches.value = res.data.data;
    } catch (err) {
        console.error("Greška pri učitavanju utakmica:", err);
    }
};

const modalTab = ref("info"); // 'info', 'squad' ili 'stats'

const openStatsModal = (match) => {
    selectedMatch.value = match;
    modalTab.value = "info"; // Podrazumevano otvara Info tab

    const teamUsers = match.team?.users || match.team?.players || [];

    statsForm.value = {
        status: match.status || "scheduled",
        home_score: match.home_score ?? 0,
        away_score: match.away_score ?? 0,
        players: teamUsers.map((user) => {
            const pivotData = match.players?.find(
                (p) => p.id === user.id,
            )?.pivot;
            return {
                id: user.id,
                name: user.name,
                jersey_number: user.player_profile?.jersey_number,
                position: user.player_profile?.primary_position,
                attended: pivotData ? Boolean(pivotData.attended) : false,
                goals: pivotData ? pivotData.goals : 0,
                assists: pivotData ? pivotData.assists : 0,
            };
        }),
    };
};

const allSquadSelected = computed(() => {
    return (
        statsForm.value.players.length > 0 &&
        statsForm.value.players.every((p) => p.attended)
    );
});

const toggleSelectAllSquad = () => {
    const target = !allSquadSelected.value;
    statsForm.value.players.forEach((p) => (p.attended = target));
};

const saveMatchStats = async () => {
    try {
        await api.put(`/matches/${selectedMatch.value.id}/stats`, {
            status: statsForm.value.status, // Osigurava prelazak u 'completed'
            home_score: statsForm.value.home_score,
            away_score: statsForm.value.away_score,
            players: statsForm.value.players,
        });

        selectedMatch.value = null;
        await fetchMatches(); // Osvežava listu i prebacuje meč u drugi tab
    } catch (err) {
        alert(err.response?.data?.message || "Greška pri čuvanju zapisnika");
    }
};

const getScorersText = (match) => {
    if (!match.players) return "";
    return match.players
        .filter((p) => p.pivot?.goals > 0)
        .map((p) => `${p.name} (${p.pivot.goals})`)
        .join(", ");
};

const getAssistersText = (match) => {
    if (!match.players) return "";
    return match.players
        .filter((p) => p.pivot?.assists > 0)
        .map((p) => `${p.name} (${p.pivot.assists})`)
        .join(", ");
};

const formatDate = (dateStr) => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString("sr-RS", {
        day: "numeric",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
    });
};

onMounted(fetchMatches);
</script>
