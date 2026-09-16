<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        <!-- Gornje Zaglavlje -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/60 p-6 rounded-2xl border border-gray-700/60 backdrop-blur-md shadow-xl"
        >
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">
                    Registar Igrača Kluba
                </h2>
                <p class="text-xs text-gray-400 mt-1">
                    Pregled svih registrovanih igrača akademije
                </p>
            </div>

            <button
                @click="showCreateModal = true"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider px-5 py-3 rounded-xl shadow-lg hover:shadow-indigo-500/25 transition flex items-center gap-2 border border-indigo-500/30"
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
                        stroke-width="2.5"
                        d="M12 4v16m8-8H4"
                    ></path>
                </svg>
                Dodaj Novog Igrača
            </button>
        </div>

        <!-- Filteri i Pretraga -->
        <div
            class="bg-gray-800/80 border border-gray-700/80 p-4 rounded-2xl shadow-xl flex flex-col sm:flex-row items-center gap-4"
        >
            <!-- Search Input -->
            <div class="relative w-full flex-1">
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Pretraži po imenu, email-u ili poziciji..."
                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-gray-500 outline-none focus:border-indigo-500 transition"
                />
                <svg
                    class="w-4 h-4 text-gray-500 absolute left-3.5 top-3"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    ></path>
                </svg>
            </div>

            <!-- Filter po Senioritetu -->
            <div class="w-full sm:w-48">
                <select
                    v-model="selectedSeniority"
                    class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer"
                >
                    <option value="all">Svi nivoi senioriteta</option>
                    <option value="senior">Senior (Prvi tim)</option>
                    <option value="youth">Youth (Omladinski)</option>
                    <option value="academy">Academy (Škola)</option>
                </select>
            </div>
        </div>

        <!-- Učitavanje -->
        <div
            v-if="userStore.loading"
            class="text-center py-16 text-gray-400 font-medium"
        >
            Učitavanje igrača...
        </div>

        <!-- Tabela Igrača sa Prilagođenim Prikazom za Mobilne Uređaje -->
        <div
            class="bg-gray-800/90 border border-gray-700/80 rounded-2xl shadow-xl overflow-hidden"
        >
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-900/70 border-b border-gray-700/70 text-gray-400 uppercase text-[11px] font-extrabold tracking-wider"
                    >
                        <th class="py-4 px-3 sm:px-6">Slika</th>
                        <th class="py-4 px-3 sm:px-6">Ime i Prezime</th>
                        <!-- Sakriveno na mobilnom, vidljivo od sm ekrana pa naviše -->
                        <th class="hidden sm:table-cell py-4 px-6">Email</th>
                        <th class="hidden sm:table-cell py-4 px-6 text-center">
                            Pozicija
                        </th>
                        <th class="hidden sm:table-cell py-4 px-6 text-center">
                            Senioritet
                        </th>
                        <th class="py-4 px-3 sm:px-6 text-right">Akcije</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50 text-xs">
                    <tr
                        v-for="user in filteredPlayers"
                        :key="user.id"
                        class="hover:bg-gray-700/40 transition group"
                    >
                        <!-- Slika (Vidljiva uvek) -->
                        <td class="py-3 px-3 sm:px-6">
                            <img
                                :src="
                                    user.player_profile?.photo_url ||
                                    `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=312e81&color=c7d2fe&size=64`
                                "
                                class="w-10 h-10 rounded-full object-cover border border-gray-600 shadow-sm"
                                :alt="user.name"
                            />
                        </td>

                        <!-- Ime i Prezime (Vidljivo uvek) -->
                        <td class="py-3 px-3 sm:px-6 font-bold text-white">
                            <router-link
                                :to="`/players/${user.id}`"
                                class="hover:text-indigo-400 transition flex items-center gap-1.5"
                            >
                                <span
                                    class="truncate max-w-[130px] sm:max-w-none"
                                    >{{ user.name }}</span
                                >
                                <span
                                    class="text-[10px] font-mono text-indigo-400 bg-indigo-950 px-1.5 py-0.5 rounded border border-indigo-800/50"
                                >
                                    #{{
                                        user.player_profile?.jersey_number ||
                                        "-"
                                    }}
                                </span>
                            </router-link>
                        </td>

                        <!-- Email (Sakriven na mobilnom) -->
                        <td
                            class="hidden sm:table-cell py-3.5 px-6 text-gray-300 font-mono"
                        >
                            {{ user.email }}
                        </td>

                        <!-- Pozicija (Sakriveno na mobilnom) -->
                        <td
                            class="hidden sm:table-cell py-3.5 px-6 text-center"
                        >
                            <span
                                class="bg-indigo-950/80 text-indigo-300 border border-indigo-800/60 font-mono font-bold text-xs px-2.5 py-1 rounded-lg"
                            >
                                {{
                                    user.player_profile?.primary_position ||
                                    "CM"
                                }}
                            </span>
                        </td>

                        <!-- Senioritet (Sakriveno na mobilnom) -->
                        <td
                            class="hidden sm:table-cell py-3.5 px-6 text-center"
                        >
                            <span
                                class="bg-gray-900 text-gray-300 border border-gray-700 font-semibold text-[11px] uppercase px-3 py-1 rounded-xl"
                            >
                                {{ user.player_profile?.seniority || "Senior" }}
                            </span>
                        </td>

                        <!-- Akciona Dugmad (Vidljiva uvek) -->
                        <td class="py-3 px-3 sm:px-6 text-right">
                            <div
                                class="flex items-center justify-end space-x-1.5 sm:space-x-2"
                            >
                                <!-- Dugme za profil (ikonica na mobilnom, profil + strelica na većim ekranima) -->
                                <router-link
                                    :to="`/players/${user.id}`"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 sm:px-3 sm:py-1.5 bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-300 hover:text-white rounded-xl transition border border-indigo-500/30 font-semibold text-xs group/btn"
                                    title="Otvorite Profil Igrača"
                                >
                                    <span class="hidden sm:inline"></span>
                                    <svg
                                        class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                                        ></path>
                                    </svg>
                                </router-link>

                                <!-- Obriši dugme -->
                                <button
                                    @click="confirmDelete(user)"
                                    class="p-1.5 sm:p-2 bg-red-500/10 hover:bg-red-500/25 text-red-400 hover:text-red-300 rounded-xl transition border border-red-500/20"
                                    title="Obrišite Igrača"
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
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        ></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="filteredPlayers.length === 0">
                        <td
                            colspan="6"
                            class="text-center py-10 text-gray-400 italic"
                        >
                            Nema pronađenih igrača koji odgovaraju pretrazi.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- MODAL ZA DODAVANJE NOVOG IGRAČA -->
        <div
            v-if="showCreateModal"
            class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50"
        >
            <div
                class="bg-gray-800 border border-gray-700 rounded-2xl p-6 w-full max-w-md shadow-2xl"
            >
                <h3 class="text-xl font-bold text-white mb-4">
                    Dodaj Novog Igrača
                </h3>

                <form @submit.prevent="handleCreatePlayer" class="space-y-4">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                            >Ime i Prezime</label
                        >
                        <input
                            v-model="newPlayer.name"
                            type="text"
                            required
                            placeholder="npr. Marko Marković"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white text-xs outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                            >Email Adresa</label
                        >
                        <input
                            v-model="newPlayer.email"
                            type="email"
                            required
                            placeholder="marko@example.com"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white text-xs outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                            >Lozinka</label
                        >
                        <input
                            v-model="newPlayer.password"
                            type="password"
                            required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white text-xs outline-none focus:border-indigo-500"
                        />
                        <input
                            v-model="newPlayer.password_confirmation"
                            type="password"
                            required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white text-xs outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                                >Broj Dresa</label
                            >
                            <input
                                v-model="newPlayer.jersey_number"
                                type="text"
                                placeholder="10"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white text-xs outline-none focus:border-indigo-500 font-mono"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                                >Pozicija</label
                            >
                            <select
                                v-model="newPlayer.primary_position"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white text-xs outline-none"
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

                    <div
                        class="flex justify-end space-x-3 pt-4 border-t border-gray-700"
                    >
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white"
                        >
                            Odustani
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold transition shadow-lg"
                        >
                            Registruj Igrača
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL POTVRDE BRISANJA -->
        <div
            v-if="playerToDelete"
            class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50"
        >
            <div
                class="bg-gray-800 border border-gray-700 rounded-2xl p-6 w-full max-w-sm shadow-2xl text-center"
            >
                <div
                    class="w-12 h-12 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center mx-auto mb-3 text-xl border border-red-500/30"
                >
                    ⚠️
                </div>
                <h3 class="text-lg font-bold text-white mb-1">
                    Obriši Igrača?
                </h3>
                <p class="text-xs text-gray-400 mb-6">
                    Da li ste sigurni da želite obrisati igrača
                    <strong class="text-white">{{ playerToDelete.name }}</strong
                    >? Ova akcija se ne može poništiti.
                </p>

                <div class="flex justify-center space-x-3">
                    <button
                        @click="playerToDelete = null"
                        class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white"
                    >
                        Odustani
                    </button>
                    <button
                        @click="handleDeletePlayer"
                        class="px-5 py-2.5 text-xs bg-red-600 hover:bg-red-500 text-white rounded-xl font-bold transition shadow-lg"
                    >
                        Obriši
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useUserStore } from "../stores/user";
import api from "../services/api";

const userStore = useUserStore();

const searchQuery = ref("");
const selectedSeniority = ref("all");
const showCreateModal = ref(false);
const playerToDelete = ref(null);

const newPlayer = reactive({
    name: "",
    email: "",
    password: "password123",
    password_confirmation: "password123",
    jersey_number: "",
    primary_position: "CM",
    seniority: "senior",
});

onMounted(() => {
    userStore.fetchUsers();
});

// Filtriranje igrača na osnovu Search ulaza i Senioriteta
const filteredPlayers = computed(() => {
    return userStore.users.filter((user) => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch =
            user.name.toLowerCase().includes(q) ||
            user.email.toLowerCase().includes(q) ||
            (user.player_profile?.primary_position || "")
                .toLowerCase()
                .includes(q);

        const matchesSeniority =
            selectedSeniority.value === "all" ||
            (user.player_profile?.seniority || "senior") ===
                selectedSeniority.value;

        return matchesSearch && matchesSeniority;
    });
});

const handleCreatePlayer = async () => {
    try {
        await api.post("/users", newPlayer);
        showCreateModal.value = false;
        newPlayer.name = "";
        newPlayer.email = "";
        newPlayer.jersey_number = "";
        await userStore.fetchUsers();
    } catch (err) {
        alert(err.response?.data?.message || "Greška pri kreiranju igrača");
    }
};

const confirmDelete = (user) => {
    playerToDelete.value = user;
};

const handleDeletePlayer = async () => {
    if (!playerToDelete.value) return;
    try {
        await api.delete(`/users/${playerToDelete.value.id}`);
        playerToDelete.value = null;
        await userStore.fetchUsers();
    } catch (err) {
        alert(err.response?.data?.message || "Greška pri brisanju igrača");
    }
};
</script>
