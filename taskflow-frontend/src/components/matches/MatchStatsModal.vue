<template>
    <div
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
                        {{ match.team?.name }} vs {{ match.opponent }}
                    </p>
                </div>
                <button
                    @click="$emit('close')"
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
                    @click="currentTab = 'info'"
                    :class="
                        currentTab === 'info'
                            ? 'bg-indigo-600 text-white font-bold'
                            : 'text-gray-400 hover:text-white'
                    "
                    class="flex-1 py-2 text-xs rounded-lg transition cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <span>📅</span>
                    <span>Info & Status</span>
                </button>

                <button
                    @click="currentTab = 'squad'"
                    :class="
                        currentTab === 'squad'
                            ? 'bg-indigo-600 text-white font-bold'
                            : 'text-gray-400 hover:text-white'
                    "
                    class="flex-1 py-2 text-xs rounded-lg transition cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <span>👥</span>
                    <span>Sastav ({{ attendedCount }})</span>
                </button>

                <button
                    @click="currentTab = 'stats'"
                    :class="
                        currentTab === 'stats'
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
            <div v-if="currentTab === 'info'" class="space-y-4 py-2">
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
                                formatDate(match.scheduled_at)
                            }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Teren / Lokacija:</span>
                        <span class="font-bold text-white">{{
                                match.location || "Nije uneto"
                            }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Uloga:</span>
                        <span class="font-bold text-white">{{
                                match.is_home ? "Domaćin" : "Gost"
                            }}</span>
                    </div>
                </div>
            </div>

            <!-- TAB 2: SASTAV / PRISUSTVO -->
            <div v-if="currentTab === 'squad'" class="space-y-3">
                <div class="flex justify-between items-center px-1">
                    <span class="text-xs text-gray-400"
                    >Štikliraj igrače koji su igrali na ovoj utakmici:</span
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
            <div v-if="currentTab === 'stats'" class="space-y-3">
                <p class="text-xs text-gray-400 px-1">
                    Unos pojedinačnog učinka (prikazani su samo igrači koji su
                    igrali):
                </p>

                <div
                    v-if="attendedPlayers.length"
                    class="max-h-[40vh] overflow-y-auto space-y-2 pr-1"
                >
                    <div
                        v-for="player in attendedPlayers"
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
                                <span class="text-xs" title="Postignuti Golovi"
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
                        Nijedan igrač nije označen kao prisutan. Prvo označite
                        sastav u tabu "Sastav".
                    </p>
                </div>
            </div>

            <!-- Akcije na dnu -->
            <div
                class="flex justify-end gap-3 pt-3 border-t border-gray-700"
            >
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white cursor-pointer"
                >
                    Odustani
                </button>
                <button
                    @click="handleSave"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2 rounded-xl transition cursor-pointer"
                >
                    Sačuvaj Zapisnik
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    match: {
        type: Object,
        required: true,
    },
    statsForm: {
        type: Object,
        required: true,
    },
});

const currentTab = ref("info");

const attendedPlayers = computed(() => {
    return props.statsForm.players.filter((p) => p.attended);
});

const attendedCount = computed(() => attendedPlayers.value.length);

const allSquadSelected = computed(() => {
    return (
        props.statsForm.players.length > 0 &&
        props.statsForm.players.every((p) => p.attended)
    );
});

const toggleSelectAllSquad = () => {
    const target = !allSquadSelected.value;
    props.statsForm.players.forEach((p) => (p.attended = target));
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

const emit = defineEmits(["close", "save"]);

const handleSave = () => {
    // Ako je status ostao 'scheduled', a uneti su golovi ili je popunjen zapisnik, prebacujemo u 'completed'
    if (props.statsForm.status === 'scheduled') {
        props.statsForm.status = 'completed';
    }
    emit('save');
};
</script>