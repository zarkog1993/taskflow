<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        <!-- Gornja Traka: Navigacija i Dugme za Izmenu -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white-50 dark:bg-gray-800/60 p-5 rounded-2xl border border-gray-700/60 backdrop-blur-md shadow-xl"
        >
            <button
                @click="$router.back()"
                class="flex items-center gap-2 text-xs font-semibold text-gray-300 hover:text-white bg-gray-800 hover:bg-gray-700 px-3.5 py-2 rounded-xl transition border border-gray-700"
            >
                ← Nazad na Ekipu
            </button>

            <button
                v-if="player"
                @click="openEditModal"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 border border-indigo-500/30"
            >
                ✏️ Izmeni Podatke Igrača
            </button>
        </div>

        <div v-if="loading" class="text-center py-20 text-gray-400 font-medium">
            Učitavanje profila igrača...
        </div>

        <div v-else-if="player" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kartica Igrača: Profilna Slika i Osnovne Informacije -->
            <div
                class="bg-white-50 dark:bg-gray-800/90 border border-gray-700/80 rounded-3xl p-6 shadow-xl text-center flex flex-col items-center justify-between"
            >
                <div class="w-full flex flex-col items-center">
                    <div class="relative w-36 h-36 mb-4">
                        <img
                            :src="
                                player.player_profile?.photo_url ||
                                `https://ui-avatars.com/api/?name=${encodeURIComponent(player.name)}&background=312e81&color=c7d2fe&size=150`
                            "
                            class="w-full h-full object-cover rounded-full border-4 border-indigo-500/40 shadow-xl"
                            alt="Profilna Slika"
                        />
                        <span
                            class="absolute bottom-1 right-1 bg-indigo-600 text-white font-mono font-extrabold text-xs px-3 py-1 rounded-full shadow-lg border border-indigo-400"
                        >
                            #{{ player.player_profile?.jersey_number || "-" }}
                        </span>
                    </div>

                    <h3
                        class="text-2xl font-extrabold text-gray-500 dark:text-white mb-0.5"
                    >
                        {{ player.name }}
                    </h3>
                    <p
                        class="text-xs text-gray-500 dark:text-gray-400 font-mono mb-4"
                    >
                        {{ player.email }}
                    </p>

                    <div class="flex flex-wrap justify-center gap-2 mb-6">
                        <span
                            class="bg-indigo-600/20 dark:bg-indigo-600/10 text-indigo-300 border border-indigo-500/30 text-xs font-mono font-extrabold uppercase px-3 py-1 rounded-xl"
                        >
                            {{
                                player.player_profile?.primary_position || "CM"
                            }}
                        </span>
                        <span
                            class="bg-purple-600/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase px-3 py-1 rounded-xl"
                        >
                            {{ player.player_profile?.category || "Seniori" }}
                        </span>
                    </div>
                </div>
                <div
                    class="bg-white/80 dark:bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl space-y-4"
                >
                    <h4
                        class="text-lg font-bold text-white border-b border-gray-700/60 pb-3"
                    >
                        Detaljni Podaci Profila
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div
                            class="bg-white/80 dark:bg-gray-900/60 p-4 rounded-2xl border border-gray-700/50"
                        >
                            <span class="text-gray-400 block mb-1"
                            >Jača noga:</span
                            >
                            <span
                                class="dark:text-white font-bold uppercase text-gray-700"
                            >{{
                                    player.player_profile?.preferred_foot ||
                                    "Nije podešeno"
                                }}</span
                            >
                        </div>

                        <div
                            class="bg-white/80 dark:bg-gray-900/60 p-4 rounded-2xl border border-gray-700/50"
                        >
                            <span class="text-gray-400 block mb-1"
                            >Datum rođenja:</span
                            >
                            <span
                                class="dark:text-white font-bold text-gray-700"
                            >{{
                                    player.player_profile?.date_of_birth ||
                                    "Nije uneto"
                                }}</span
                            >
                        </div>

                        <div
                            class="bg-white/80 dark:bg-gray-900/60 p-4 rounded-2xl border border-gray-700/50 md:col-span-2"
                        >
                            <span
                                class="text-gray-400 dark:text-gray-100 block mb-1"
                            >Medicinske napomene / Povrede:</span
                            >
                            <span
                                class="dark:text-gray-200 font-medium text-gray-800"
                            >{{
                                    player.player_profile?.medical_notes ||
                                    "Nema zabeleženih medicinskih napomena."
                                }}</span
                            >
                        </div>
                    </div>
                </div>


                <!-- Fizički Status / Napomena -->
                <div
                    class="w-full bg-gray-900/70 p-4 rounded-2xl border border-gray-700/60 text-left"
                >
                    <div
                        class="text-[10px] font-bold uppercase text-gray-400 mb-1"
                    >
                        Status Fizičke Spreme
                    </div>
                    <div
                        class="text-sm font-semibold text-emerald-400 flex items-center gap-2"
                    >
                        <span
                            class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"
                        ></span>
                        {{
                            player.player_profile?.fitness_status ||
                            "Spreman za utakmicu"
                        }}
                    </div>
                </div>
            </div>

            <!-- Statistika i Detaljni Podaci -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Kartice Statistike -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div
                        class="bg-white/80 dark:bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl text-center shadow-lg"
                    >
                        <div class="text-3xl font-black text-blue-400 mb-1">
                            {{ player.player_profile?.trainings_attended || 0 }}
                        </div>
                        <div
                            class="text-[10px] font-bold uppercase tracking-wider text-gray-400"
                        >
                            Treninga
                        </div>
                    </div>

                    <div
                        class="bg-white/80 border dark:bg-gray-800/80 border-gray-700/80 p-5 rounded-2xl text-center shadow-lg"
                    >
                        <div class="text-3xl font-black text-emerald-400 mb-1">
                            {{ player.player_profile?.matches_played || 0 }}
                        </div>
                        <div
                            class="text-[10px] font-bold uppercase tracking-wider text-gray-400"
                        >
                            Utakmica
                        </div>
                    </div>

                    <div
                        class="bg-white/80 dark:bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl text-center shadow-lg"
                    >
                        <div class="text-3xl font-black text-yellow-400 mb-1">
                            {{ player.player_profile?.goals || 0 }}
                        </div>
                        <div
                            class="text-[10px] font-bold uppercase tracking-wider text-gray-400"
                        >
                            Golova
                        </div>
                    </div>

                    <div
                        class="bg-white/80 dark:bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl text-center shadow-lg"
                    >
                        <div class="text-3xl font-black text-purple-400 mb-1">
                            {{ player.player_profile?.assists || 0 }}
                        </div>
                        <div
                            class="text-[10px] font-bold uppercase tracking-wider text-gray-400"
                        >
                            Asistencija
                        </div>
                    </div>
                </div>
                <PitchPositionMap
                    :position="playerProfile?.primary_position || playerProfile?.player_profile?.primary_position || player?.primary_position || player?.player_profile?.primary_position"
                />
                <!-- Evidencija Treninga i Napomena -->
            </div>
        </div>

        <!-- MODAL ZA IZMENU PODATAKA IGRAČA -->
        <div
            v-if="showEditModal"
            class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50"
        >
            <div
                class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200 max-h-[90vh] flex flex-col"
            >
                <!-- Zaglavlje -->
                <div
                    class="p-6 bg-gradient-to-r from-gray-900 via-gray-900/90 to-gray-800 border-b border-gray-700/70 relative"
                >
                    <button
                        @click="showEditModal = false"
                        class="absolute top-5 right-5 text-gray-400 hover:text-white p-1 rounded-lg hover:bg-gray-800 transition"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                    <h3 class="text-xl font-black text-white">
                        Izmena Podataka Igrača
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">
                        Ažuriranje ličnih i sportskih informacija
                    </p>
                </div>

                <!-- Forma -->
                <form
                    @submit.prevent="handleSaveProfile"
                    class="p-6 space-y-4 overflow-y-auto flex-1 custom-scroll"
                >
                    <div>
                        <label
                            class="block text-xs font-bold uppercase text-gray-400 mb-1"
                            >Ime i Prezime</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold uppercase text-gray-400 mb-1"
                                >Broj Dresa</label
                            >
                            <input
                                v-model="form.jersey_number"
                                type="text"
                                placeholder="npr. 10"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500 font-mono"
                            />
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase text-gray-400 mb-1"
                                >Primarna Pozicija</label
                            >
                            <select
                                v-model="form.primary_position"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                            >
                                <option value="GK">GK - Golman</option>
                                <option value="CB">CB - Centralni Bek</option>
                                <option value="LB">LB - Levi Bek</option>
                                <option value="RB">RB - Desni Bek</option>
                                <option value="CM">CM - Vezni</option>
                                <option value="CAM">
                                    CAM - Ofanzivni Vezni
                                </option>
                                <option value="LW">LW - Levo Krilo</option>
                                <option value="RW">RW - Desno Krilo</option>
                                <option value="ST">ST - Napadač</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-bold uppercase text-gray-400 mb-1"
                                >Jača Noga</label
                            >
                            <select
                                v-model="form.preferred_foot"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                            >
                                <option value="desna">Desna</option>
                                <option value="leva">Leva</option>
                                <option value="obe">Obe noge</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-xs font-bold uppercase text-gray-400 mb-1"
                                >Datum Rođenja</label
                            >
                            <input
                                v-model="form.date_of_birth"
                                type="date"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500 font-mono dark-date-picker"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold uppercase text-gray-400 mb-1"
                            >URL Profilne Slike (Opciono)</label
                        >
                        <input
                            v-model="form.photo_url"
                            type="url"
                            placeholder="https://..."
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold uppercase text-gray-400 mb-1"
                        >
                            Status Fizičke Spreme
                        </label>
                        <div class="relative">
                            <select
                                v-model="form.fitness_status"
                                class="w-full appearance-none bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500 transition cursor-pointer pr-10"
                            >
                                <option
                                    value=""
                                    disabled
                                    class="bg-gray-800 text-gray-400"
                                >
                                    Izaberite status...
                                </option>
                                <option
                                    value="fit"
                                    class="bg-gray-800 text-white"
                                >
                                    Spreman za utakmicu
                                </option>
                                <option
                                    value="injured"
                                    class="bg-gray-800 text-white"
                                >
                                    Povređen
                                </option>
                                <option
                                    value="rehab"
                                    class="bg-gray-800 text-white"
                                >
                                    Rovito / Oporavak
                                </option>
                                <option
                                    value="absent"
                                    class="bg-gray-800 text-white"
                                >
                                    Odsutan
                                </option>
                            </select>

                            <!-- Custom SVG strelica umesto sistemske -->
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-gray-400"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold uppercase text-gray-400 mb-1"
                            >Medicinske Napomene / Povrede</label
                        >
                        <textarea
                            v-model="form.medical_notes"
                            rows="3"
                            placeholder="Zabeleške o pošteđenosti ili tretmanima..."
                            class="w-full dark:bg-gray-900 border border-gray-700 rounded-xl p-3 text-xs text-white outline-none dark:text-gray-900 focus:border-indigo-500 resize-none"
                        ></textarea>
                    </div>

                    <div
                        class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-700"
                    >
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white"
                        >
                            Odustani
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition"
                        >
                            Sačuvaj Promene
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRoute } from "vue-router";
import api from "../services/api";
import PitchPositionMap from '../components/PitchPositionMap.vue'

const route = useRoute();
const player = ref(null);
const loading = ref(true);
const showEditModal = ref(false);

const form = reactive({
    name: "",
    jersey_number: "",
    primary_position: "CM",
    preferred_foot: "desna",
    date_of_birth: "",
    photo_url: "",
    fitness_status: "",
    medical_notes: "",
});

const fetchPlayerProfile = async () => {
    loading.value = true;
    try {
        const res = await api.get(`/users/${route.params.id}`);
        player.value = res.data.data || res.data;
    } catch (err) {
        alert("Greška pri učitavanju profila igrača.");
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchPlayerProfile();
});

const openEditModal = () => {
    if (!player.value) return;

    form.name = player.value.name || "";
    form.jersey_number = player.value.player_profile?.jersey_number !== null && player.value.player_profile?.jersey_number !== undefined
      ? String(player.value.player_profile?.jersey_number) 
      : null;
    form.primary_position =
        player.value.player_profile?.primary_position || "CM";
    form.preferred_foot =
        player.value.player_profile?.preferred_foot || "desna";
    form.date_of_birth = player.value.player_profile?.date_of_birth || "";
    form.photo_url = player.value.player_profile?.photo_url || "";
    form.fitness_status =
        player.value.player_profile?.fitness_status || "Spreman za utakmicu";
    form.medical_notes = player.value.player_profile?.medical_notes || "";

    showEditModal.value = true;
};

const handleSaveProfile = async () => {
    try {
        const res = await api.put(`/users/${route.params.id}/profile`, form);
        player.value = res.data.data || res.data;
        showEditModal.value = false;
    } catch (err) {
        alert(
            err.response?.data?.message ||
                "Greška pri sačuvavanju profila igrača.",
        );
    }
};
</script>

<style scoped>
.dark-date-picker::-webkit-calendar-picker-indicator {
    filter: invert(1) hue-rotate(180deg);
    cursor: pointer;
    opacity: 0.8;
}
</style>
