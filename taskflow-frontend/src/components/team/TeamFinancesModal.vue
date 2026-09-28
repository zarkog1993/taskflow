<template>
    <div
        class="bg-gray-800/60 border border-gray-700/60 backdrop-blur-md rounded-2xl p-6 shadow-xl space-y-6"
    >
        <!-- Zaglavlje sekcije + Kontrole -->
        <div
            class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 pb-4 border-b border-gray-700/60"
        >
            <div>
                <h3
                    class="text-xl font-bold text-white flex items-center gap-2"
                >
                    <span>💳</span> Finansijski Karton Ekipe
                </h3>
                <p class="text-xs text-gray-400 mt-1">
                    Upravljanje mesečnim članarinama za akademiju i isplatama za
                    prvotimce
                </p>
            </div>

            <div
                class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto"
            >
                <!-- Tab Prekidač: Članarine / Isplate -->
                <div
                    class="flex bg-gray-900/90 p-1 rounded-xl border border-gray-700 text-xs w-full sm:w-auto"
                >
                    <button
                        @click="activeType = 'membership'"
                        :class="
                            activeType === 'membership'
                                ? 'bg-indigo-600 text-white font-semibold shadow'
                                : 'text-gray-400 hover:text-white'
                        "
                        class="px-4 py-2 rounded-lg transition text-center flex-1 sm:flex-none"
                    >
                        Mesečne Članarine (Akademija)
                    </button>
                    <button
                        @click="activeType = 'stipend'"
                        :class="
                            activeType === 'stipend'
                                ? 'bg-indigo-600 text-white font-semibold shadow'
                                : 'text-gray-400 hover:text-white'
                        "
                        class="px-4 py-2 rounded-lg transition text-center flex-1 sm:flex-none"
                    >
                        Isplate Prvotimcima (Seniori)
                    </button>
                </div>

                <!-- Filter za Mesec -->
                <div
                    class="flex items-center gap-2 text-xs w-full sm:w-auto justify-end"
                >
                    <span class="text-gray-400 font-medium">Mesec:</span>
                    <input
                        type="month"
                        v-model="selectedPeriod"
                        class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-1.5 text-white font-mono focus:outline-none focus:border-indigo-500"
                    />
                </div>
            </div>
        </div>

        <!-- Status Učitavanja -->
        <div
            v-if="loading"
            class="py-12 text-center text-gray-400 text-sm font-medium flex justify-center items-center gap-2"
        >
            <svg
                class="animate-spin h-5 w-5 text-indigo-500"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>
                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
            </svg>
            Učitavanje finansijskih podataka...
        </div>

        <!-- Tabela sa Igračima i Uplatama -->
        <div
            v-else-if="players && players.length > 0"
            class="overflow-x-auto rounded-xl border border-gray-700/60"
        >
            <table class="w-full text-left text-sm text-gray-300">
                <thead
                    class="bg-gray-900/80 text-xs uppercase text-gray-400 border-b border-gray-700"
                >
                    <tr>
                        <th class="py-3.5 px-4">Igrač</th>
                        <th class="py-3.5 px-4">Dres / Pozicija</th>
                        <th class="py-3.5 px-4">Iznos (RSD / €)</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Promeni Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50 bg-gray-900/30">
                    <tr
                        v-for="player in players"
                        :key="player.id"
                        class="hover:bg-gray-700/20 transition"
                    >
                        <td class="py-3.5 px-4 font-semibold text-white">
                            {{ player.name }}
                        </td>
                        <td class="py-3.5 px-4 text-xs text-gray-400">
                            #{{ player.jersey_number || "-" }} |
                            {{ player.position || "Igrač" }}
                        </td>
                        <td class="py-3.5 px-4">
                            <input
                                type="number"
                                v-model.number="getPayment(player.id).amount"
                                @change="savePayment(player.id)"
                                class="w-28 bg-gray-950 border border-gray-700 rounded-lg px-2.5 py-1 text-sm font-semibold text-emerald-400 focus:outline-none focus:border-indigo-500"
                            />
                        </td>
                        <td class="py-3.5 px-4">
                            <span
                                :class="{
                                    'bg-emerald-500/10 text-emerald-400 border-emerald-500/30':
                                        getPayment(player.id).status === 'paid',
                                    'bg-amber-500/10 text-amber-400 border-amber-500/30':
                                        getPayment(player.id).status ===
                                        'pending',
                                    'bg-rose-500/10 text-rose-400 border-rose-500/30':
                                        getPayment(player.id).status ===
                                        'overdue',
                                }"
                                class="px-2.5 py-1 rounded-full text-xs font-bold border uppercase tracking-wider inline-block"
                            >
                                {{
                                    getPayment(player.id).status === "paid"
                                        ? "Plaćeno"
                                        : getPayment(player.id).status ===
                                            "pending"
                                          ? "Na čekanju"
                                          : "Kasni"
                                }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button
                                    @click="updateStatus(player.id, 'paid')"
                                    :class="
                                        getPayment(player.id).status === 'paid'
                                            ? 'bg-emerald-600 text-white'
                                            : 'bg-gray-800 text-gray-300 hover:bg-emerald-600/30'
                                    "
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-700 transition"
                                >
                                    ✓ Plaćeno
                                </button>
                                <button
                                    @click="updateStatus(player.id, 'pending')"
                                    :class="
                                        getPayment(player.id).status ===
                                        'pending'
                                            ? 'bg-amber-600 text-white'
                                            : 'bg-gray-800 text-gray-300 hover:bg-amber-600/30'
                                    "
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-700 transition"
                                >
                                    ⏳ Čeka
                                </button>
                                <button
                                    @click="updateStatus(player.id, 'overdue')"
                                    :class="
                                        getPayment(player.id).status ===
                                        'overdue'
                                            ? 'bg-rose-600 text-white'
                                            : 'bg-gray-800 text-gray-300 hover:bg-rose-600/30'
                                    "
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-700 transition"
                                >
                                    ✕ Kasni
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-else
            class="py-12 text-center text-gray-400 text-sm bg-gray-900/30 rounded-xl border border-gray-700/60"
        >
            Ova ekipa trenutno nema dodeljenih igrača.
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useTeamFinances } from "../composables/useTeamFinances";

const props = defineProps({
    teamId: { type: Number, required: true },
    players: { type: Array, default: () => [] },
});

const teamIdRef = computed(() => props.teamId);

const {
    activeType,
    selectedPeriod,
    loading,
    getPayment,
    updateStatus,
    savePayment,
    loadPayments,
} = useTeamFinances(teamIdRef);

onMounted(() => {
    loadPayments();
});
</script>
