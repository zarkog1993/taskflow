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
        <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-gray-900/80 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/80">
                        <th class="p-4">Slika</th>
                        <th class="p-4">Ime i Prezime</th>
                        <th class="p-4">Pozicija</th>
                        <th class="p-4">Senioritet</th>
                        <th class="p-4">Beleška Trenera</th>
                        <th class="p-4 text-right">Akcije</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/60 text-xs">
                    <tr v-for="player in filteredPlayers" :key="player.id" class="hover:bg-gray-700/30 transition">
                        <td class="p-4">
                            <router-link :to="`/players/${player.id}`" class="block w-10 h-10 rounded-full bg-indigo-950 border border-indigo-800 overflow-hidden flex items-center justify-center font-bold text-indigo-300 text-xs shadow-md hover:border-indigo-500 transition">
                                <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover" />
                                <span v-else>#{{ player.jersey_number || '-' }}</span>
                            </router-link>
                        </td>

                        <!-- Poveznica za profil igrača -->
                        <td class="p-4 font-bold text-white">
                            <router-link :to="`/players/${player.id}`" class="hover:text-indigo-400 transition underline-offset-2 hover:underline">
                                {{ player.name }}
                            </router-link>
                        </td>

                        <td class="p-4">
                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-950 text-indigo-400 border border-indigo-800">
                  {{ player.primary_position }}
                </span>
                        </td>
                        <td class="p-4 text-gray-300">{{ player.seniority || 'Seniori' }}</td>
                        <td class="p-4 text-gray-400 italic max-w-xs truncate">
                            {{ player.coach_notes || '-' }}
                        </td>
                        <td class="p-4 text-right">
                            <button
                                @click="confirmDelete(player)"
                                title="Obriši igrača"
                                class="p-2 bg-rose-950/60 hover:bg-rose-900 border border-rose-800/80 text-rose-300 hover:text-white rounded-xl transition cursor-pointer"
                            >
                                🗑️
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="!filteredPlayers.length" class="text-center py-12 text-xs text-gray-400 italic">
                Nema pronađenih igrača.
            </div>
        </div>

        <!-- MODAL: Dodaj Novog Igrača -->
        <div v-if="showCreatePlayerModal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
                <div class="p-5 bg-gray-900 border-b border-gray-700/70 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-xl font-black text-white">Dodaj Novog Igrača</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Unesite lične, fizičke i fudbalske parametre</p>
                    </div>
                    <button @click="showCreatePlayerModal = false" class="text-gray-400 hover:text-white font-bold text-lg cursor-pointer">✕</button>
                </div>

                <form @submit.prevent="handleCreatePlayer" class="p-6 space-y-4 overflow-y-auto">
                    <!-- Upload Slike (Max 500 KB) -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Slika Igrača (Max 500 KB)</label>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-900 border border-gray-700 overflow-hidden flex items-center justify-center shrink-0">
                                <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                                <span v-else class="text-gray-500 text-xs">📷</span>
                            </div>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                @change="handlePhotoSelect"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-gray-300 outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-indigo-950 file:text-indigo-400 hover:file:bg-indigo-900 cursor-pointer"
                            />
                        </div>
                        <p v-if="photoError" class="text-[10px] font-bold text-rose-400 mt-1">{{ photoError }}</p>
                    </div>

                    <!-- Ime i Prezime -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Ime i Prezime *</label>
                        <input v-model="newPlayer.name" type="text" required placeholder="npr. Marko Marković" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500" />
                    </div>

                    <!-- Pozicija i Senioritet -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Pozicija *</label>
                            <select v-model="newPlayer.primary_position" required class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
                                <option value="GK">GK - Golman</option>
                                <option value="CB">CB - Štoper</option>
                                <option value="LB">LB - Levi Bek</option>
                                <option value="RB">RB - Desni Bek</option>
                                <option value="CM">CM - Centralni Vezni</option>
                                <option value="DM">DM - Zadnji Vezni</option>
                                <option value="AM">AM - Prednji Vezni</option>
                                <option value="LW">LW - Levo Krilo</option>
                                <option value="RW">RW - Desno Krilo</option>
                                <option value="ST">ST - Napadač</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Senioritet</label>
                            <select v-model="newPlayer.seniority" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer">
                                <option value="Seniori">Seniori</option>
                                <option value="U19">U19 (Omladinci)</option>
                                <option value="U17">U17 (Kadeti)</option>
                                <option value="U15">U15 (Pioniri)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Visina, Težina i Datum Rođenja -->
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Visina (cm)</label>
                            <input v-model.number="newPlayer.height" type="number" placeholder="185" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Težina (kg)</label>
                            <input v-model.number="newPlayer.weight" type="number" placeholder="78" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Datum Rođenja</label>
                            <input v-model="newPlayer.date_of_birth" type="date" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-2 py-2.5 text-xs text-white outline-none" />
                        </div>
                    </div>

                    <!-- Jača Noga i Broj Dresa -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Jača Noga</label>
                            <select v-model="newPlayer.preferred_foot" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none cursor-pointer">
                                <option value="right">Desna</option>
                                <option value="left">Leva</option>
                                <option value="both">Obe</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Broj Dresa</label>
                            <input v-model.number="newPlayer.jersey_number" type="number" placeholder="10" class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2.5 text-xs text-white outline-none text-center" />
                        </div>
                    </div>

                    <!-- Beleška Trenera -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Beleška Trenera</label>
                        <textarea v-model="newPlayer.coach_notes" rows="2" placeholder="Taktičke opaske..." class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-xs text-white outline-none focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Akcije -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-700/70 shrink-0">
                        <button type="button" @click="showCreatePlayerModal = false" class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white transition cursor-pointer">Odustani</button>
                        <button type="submit" :disabled="isSubmitting" class="px-5 py-2.5 text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg transition border border-indigo-500/30 cursor-pointer disabled:opacity-50">
                            <span v-if="isSubmitting" class="animate-spin">⏳</span>
                            <span>Registruj Igrača</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Potvrda Brisanja -->
        <div v-if="playerToDelete" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5 text-center">
                <div class="w-14 h-14 bg-rose-950/80 border border-rose-800/80 text-rose-400 rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner">
                    🗑️
                </div>
                <div class="space-y-2">
                    <h3 class="text-xl font-black text-white">Brisanje Igrača</h3>
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Da li ste sigurni da želite da obrišete igrača <strong class="text-white font-bold">"{{ playerToDelete.name }}"</strong>?
                    </p>
                </div>
                <div class="flex items-center justify-center gap-3 pt-2">
                    <button @click="playerToDelete = null" class="w-full py-2.5 bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl border border-gray-700 transition cursor-pointer">
                        Odustani
                    </button>
                    <button @click="handleDeletePlayer" :disabled="isDeleting" class="w-full py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                        <span v-if="isDeleting" class="animate-spin">⏳</span>
                        <span>{{ isDeleting ? 'Brisanje...' : 'Da, Obriši' }}</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import api from "../services/api";

const players = ref([]);
const searchQuery = ref("");
const selectedSeniority = ref("all");
const showCreatePlayerModal = ref(false);
const isSubmitting = ref(false);
const isDeleting = ref(false);
const playerToDelete = ref(null);

const selectedPhoto = ref(null);
const photoPreview = ref(null);
const photoError = ref("");

const newPlayer = reactive({
    name: "",
    primary_position: "CM",
    seniority: "Seniori",
    height: null,
    weight: null,
    date_of_birth: "",
    preferred_foot: "right",
    jersey_number: null,
    coach_notes: "",
});

const handlePhotoSelect = (event) => {
    const file = event.target.files[0];
    photoError.value = "";

    if (!file) {
        selectedPhoto.value = null;
        photoPreview.value = null;
        return;
    }

    if (file.size > 500 * 1024) {
        photoError.value = "Slika je prevelika! Maksimalna dozvoljena veličina je 500 KB.";
        event.target.value = "";
        selectedPhoto.value = null;
        photoPreview.value = null;
        return;
    }

    selectedPhoto.value = file;
    photoPreview.value = URL.createObjectURL(file);
};

const fetchPlayers = async () => {
    try {
        const res = await api.get("/players");
        players.value = res.data.data || res.data || [];
    } catch (err) {
        console.error("Greška pri dohvatanju igrača:", err);
    }
};

onMounted(fetchPlayers);

const filteredPlayers = computed(() => {
    return players.value.filter((p) => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch = !q || p.name.toLowerCase().includes(q) || p.primary_position.toLowerCase().includes(q);
        const matchesSeniority = selectedSeniority.value === "all" || p.seniority.toLowerCase() === selectedSeniority.value.toLowerCase();
        return matchesSearch && matchesSeniority;
    });
});

const handleCreatePlayer = async () => {
    if (photoError.value) return;
    isSubmitting.value = true;

    try {
        const formData = new FormData();
        formData.append("name", newPlayer.name);
        formData.append("primary_position", newPlayer.primary_position);
        formData.append("seniority", newPlayer.seniority);

        if (newPlayer.jersey_number) formData.append("jersey_number", newPlayer.jersey_number);
        if (newPlayer.height) formData.append("height", newPlayer.height);
        if (newPlayer.weight) formData.append("weight", newPlayer.weight);
        if (newPlayer.date_of_birth) formData.append("date_of_birth", newPlayer.date_of_birth);
        if (newPlayer.preferred_foot) formData.append("preferred_foot", newPlayer.preferred_foot);
        if (newPlayer.coach_notes) formData.append("coach_notes", newPlayer.coach_notes);
        if (selectedPhoto.value) formData.append("photo", selectedPhoto.value);

        await api.post("/players", formData, {
            headers: { "Content-Type": "multipart/form-data" }
        });

        showCreatePlayerModal.value = false;
        resetForm();
        await fetchPlayers();
    } catch (err) {
        console.error("Greška pri kreiranju igrača:", err);
        alert(err.response?.data?.message || "Došlo je do greške prilikom kreiranja igrača.");
    } finally {
        isSubmitting.value = false;
    }
};

const resetForm = () => {
    newPlayer.name = "";
    newPlayer.primary_position = "CM";
    newPlayer.seniority = "Seniori";
    newPlayer.height = null;
    newPlayer.weight = null;
    newPlayer.date_of_birth = "";
    newPlayer.preferred_foot = "right";
    newPlayer.jersey_number = null;
    newPlayer.coach_notes = "";
    selectedPhoto.value = null;
    photoPreview.value = null;
    photoError.value = "";
};

const confirmDelete = (player) => {
    playerToDelete.value = player;
};

const handleDeletePlayer = async () => {
    if (!playerToDelete.value) return;
    isDeleting.value = true;
    try {
        await api.delete(`/players/${playerToDelete.value.id}`);
        playerToDelete.value = null;
        await fetchPlayers();
    } catch (err) {
        alert(err.response?.data?.message || "Greška pri brisanju igrača");
    } finally {
        isDeleting.value = false;
    }
};
</script>