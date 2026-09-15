<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
        <!-- Gornje Zaglavlje -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-800/60 p-6 rounded-2xl border border-gray-700/60 backdrop-blur-md shadow-xl"
        >
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span
                        class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"
                    ></span>
                    <span
                        class="text-xs font-bold uppercase tracking-wider text-indigo-400"
                        >Akademija & Selekcije</span
                    >
                </div>
                <h2 class="text-3xl font-black text-white tracking-tight">
                    Upravljanje Ekipe
                </h2>
                <p class="text-sm text-gray-400 mt-1">
                    Pregled svih timova, starosnih kategorija i sastava ekipa
                </p>
            </div>

            <button
                @click="showCreateModal = true"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold uppercase tracking-wider px-6 py-3.5 rounded-xl shadow-lg hover:shadow-indigo-500/25 transition flex items-center gap-2 border border-indigo-500/30"
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
                Nova Ekipa
            </button>
        </div>

        <!-- Brza Statistika -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div
                class="bg-gray-800/50 border border-gray-700/70 rounded-2xl p-5 flex items-center justify-between shadow-lg"
            >
                <div>
                    <div
                        class="text-xs font-bold uppercase text-gray-400 tracking-wider"
                    >
                        Ukupno Ekipa
                    </div>
                    <div class="text-3xl font-black text-white mt-1">
                        {{ teamStore.teams.length }}
                    </div>
                </div>
                <div
                    class="p-3.5 bg-indigo-600/10 border border-indigo-500/20 text-indigo-400 rounded-2xl text-xl"
                >
                    🛡️
                </div>
            </div>

            <div
                class="bg-gray-800/50 border border-gray-700/70 rounded-2xl p-5 flex items-center justify-between shadow-lg"
            >
                <div>
                    <div
                        class="text-xs font-bold uppercase text-gray-400 tracking-wider"
                    >
                        Registrovani Igrači
                    </div>
                    <div class="text-3xl font-black text-emerald-400 mt-1">
                        {{ totalPlayersCount }}
                    </div>
                </div>
                <div
                    class="p-3.5 bg-emerald-600/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-xl"
                >
                    🏃‍♂️
                </div>
            </div>

            <div
                class="bg-gray-800/50 border border-gray-700/70 rounded-2xl p-5 flex items-center justify-between shadow-lg"
            >
                <div>
                    <div
                        class="text-xs font-bold uppercase text-gray-400 tracking-wider"
                    >
                        Slobodni Igrači
                    </div>
                    <div class="text-3xl font-black text-yellow-400 mt-1">
                        {{ unassignedPlayersCount }}
                    </div>
                </div>
                <div
                    class="p-3.5 bg-yellow-600/10 border border-yellow-500/20 text-yellow-400 rounded-2xl text-xl"
                >
                    📋
                </div>
            </div>
        </div>

        <!-- Učitavanje -->
        <div
            v-if="teamStore.loading"
            class="text-center py-20 text-gray-400 font-medium"
        >
            Učitavanje ekipa...
        </div>

        <!-- Prostrana Mreža Kartica (Max 2 u redu za bolju čitljivost) -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div
                v-for="team in teamStore.teams"
                :key="team.id"
                class="bg-gray-800/90 hover:bg-gray-800 border border-gray-700/80 hover:border-indigo-500/50 rounded-3xl p-7 shadow-2xl transition-all duration-300 flex flex-col justify-between group relative"
            >
                <div>
                    <!-- Zaglavlje Kartice -->
                    <div
                        class="flex justify-between items-start pb-5 mb-5 border-b border-gray-700/60"
                    >
                        <div>
                            <span
                                class="text-xs font-bold uppercase tracking-wider text-indigo-400"
                                >Selekcija Ekipe</span
                            >
                            <h3
                                class="text-2xl font-black text-white group-hover:text-indigo-300 transition-colors mt-0.5"
                            >
                                {{ team.name }}
                            </h3>
                            <p class="text-xs text-gray-400 mt-1">
                                Ukupno
                                {{ team.members?.length || 0 }} dodeljenih
                                igrač(a)
                            </p>
                        </div>

                        <span
                            class="bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 text-xs font-mono font-extrabold uppercase px-4 py-1.5 rounded-xl shadow-sm"
                        >
                            {{ team.age_group }}
                        </span>
                    </div>

                    <!-- Spisak Igrača -->
                    <div class="space-y-4 mb-6">
                        <div
                            class="flex justify-between items-center text-xs font-extrabold uppercase tracking-wider text-gray-400 px-1"
                        >
                            <span>Igrački Kadar</span>
                            <span>Dres / Pozicija</span>
                        </div>

                        <div
                            v-if="team.members && team.members.length > 0"
                            class="space-y-2.5 max-h-80 overflow-y-auto pr-2 custom-scroll"
                        >
                            <div
                                v-for="member in team.members"
                                :key="member.id"
                                class="flex items-center justify-between bg-gray-900/80 hover:bg-gray-900 p-3.5 rounded-2xl border border-gray-700/60 transition"
                            >
                                <div class="flex items-center space-x-3.5">
                                    <img
                                        :src="
                                            member.player_profile?.photo_url ||
                                            `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                                member.name,
                                            )}&background=312e81&color=c7d2fe&size=96`
                                        "
                                        class="w-10 h-10 rounded-full object-cover border border-gray-600 shadow-sm"
                                        :alt="member.name"
                                    />
                                    <div>
                                        <router-link
                                            :to="`/players/${member.id}`"
                                            class="text-sm font-bold text-white hover:text-indigo-300 transition leading-tight block"
                                        >
                                            {{ member.name }}
                                        </router-link>
                                        <span class="text-xs text-gray-400">
                                            {{ member.email }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <span
                                        class="text-xs font-mono font-bold text-indigo-400 bg-indigo-950/80 px-2.5 py-1 rounded-lg border border-indigo-800/60"
                                    >
                                        {{
                                            member.player_profile
                                                ?.primary_position || "CM"
                                        }}
                                    </span>
                                    <span
                                        class="bg-gray-800 text-white border border-gray-700 text-xs font-mono font-bold px-3 py-1 rounded-lg shadow-sm"
                                    >
                                        #{{
                                            member.player_profile
                                                ?.jersey_number || "-"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div
                            v-else
                            class="text-center py-10 bg-gray-900/40 rounded-2xl border border-dashed border-gray-700/60"
                        >
                            <p class="text-sm text-gray-400 italic">
                                Ova ekipa trenutno nema dodeljenih igrača.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Akciona Dugmad -->
                <div
                    class="grid grid-cols-2 gap-4 pt-5 border-t border-gray-700/60"
                >
                    <button
                        @click="openAssignModal(team)"
                        class="py-3 px-4 bg-gray-700/60 hover:bg-gray-700 text-gray-200 text-xs font-bold rounded-xl transition border border-gray-600/50 flex items-center justify-center gap-2"
                    >
                        <span>⚙️ Upravljaj Sastavom</span>
                    </button>

                    <router-link
                        :to="`/teams/${team.id}`"
                        class="py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow-lg flex items-center justify-center gap-2 border border-indigo-500/30"
                    >
                        <span>Prikazi Detalje i Stats</span> →
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Modal za Kreiranje Nove Ekipe -->
        <div
            v-if="showCreateModal"
            class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50"
        >
            <div
                class="bg-gray-800 border border-gray-700 rounded-2xl p-6 w-full max-w-md shadow-2xl"
            >
                <h3 class="text-xl font-bold text-white mb-4">
                    Dodaj Novu Ekipu
                </h3>

                <form @submit.prevent="handleCreateTeam" class="space-y-4">
                    <div>
                        <label
                            class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                            >Naziv Ekipe</label
                        >
                        <input
                            v-model="newTeam.name"
                            type="text"
                            required
                            placeholder="npr. Rudar U17"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white text-sm outline-none focus:border-indigo-500 transition"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold uppercase text-gray-400 mb-1"
                            >Starosna Kategorija</label
                        >
                        <select
                            v-model="newTeam.age_group"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl p-3 text-white text-sm outline-none"
                        >
                            <option value="u9">U9 (Mlađi petlići)</option>
                            <option value="u11">U11 (Petlići)</option>
                            <option value="u13">U13 (Mlađi pioniri)</option>
                            <option value="u15">U15 (Pioniri)</option>
                            <option value="u17">U17 (Kadeti)</option>
                            <option value="u19">U19 (Omladinci)</option>
                            <option value="senior">Seniori (Prvi Tim)</option>
                        </select>
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
                            Sačuvaj Ekipu
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal za Upravljanje Sastavom -->
        <div
            v-if="showAssignModal && activeTeam"
            class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50"
        >
            <div
                class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]"
            >
                <div
                    class="p-5 bg-gradient-to-r from-gray-900 via-gray-900/90 to-gray-800 border-b border-gray-700/70 flex justify-between items-center"
                >
                    <div>
                        <span
                            class="text-[10px] font-bold uppercase tracking-wider text-indigo-400"
                            >Izbor Sastava</span
                        >
                        <h3 class="text-xl font-black text-white">
                            {{ activeTeam.name }}
                        </h3>
                    </div>
                    <span
                        class="text-xs font-mono font-bold text-indigo-300 bg-indigo-600/20 px-3 py-1 rounded-xl border border-indigo-500/30"
                    >
                        {{ activeTeam.age_group }}
                    </span>
                </div>

                <div class="p-4 bg-gray-900/60 border-b border-gray-700/60">
                    <input
                        v-model="modalSearch"
                        type="text"
                        placeholder="Pretraži igrački kadar po imenu..."
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-gray-500 outline-none focus:border-indigo-500 transition"
                    />
                </div>

                <div
                    class="p-4 overflow-y-auto space-y-2.5 flex-1 custom-scroll"
                >
                    <div
                        v-for="user in modalFilteredUsers"
                        :key="user.id"
                        @click="toggleUserSelection(user.id)"
                        :class="[
                            'flex items-center justify-between p-3.5 rounded-2xl border transition cursor-pointer select-none',
                            selectedUserIds.includes(user.id)
                                ? 'bg-indigo-950/40 border-indigo-500/60 hover:bg-indigo-950/60'
                                : 'bg-gray-900/50 border-gray-700/50 hover:bg-gray-900',
                        ]"
                    >
                        <div class="flex items-center space-x-3.5">
                            <input
                                type="checkbox"
                                :checked="selectedUserIds.includes(user.id)"
                                class="rounded bg-gray-800 border-gray-600 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer"
                            />
                            <img
                                :src="
                                    user.player_profile?.photo_url ||
                                    `https://ui-avatars.com/api/?name=${encodeURIComponent(
                                        user.name,
                                    )}&background=312e81&color=c7d2fe&size=64`
                                "
                                class="w-9 h-9 rounded-full object-cover border border-gray-600 shadow-sm"
                                :alt="user.name"
                            />
                            <div>
                                <div class="text-xs font-bold text-white">
                                    {{ user.name }}
                                </div>
                                <div
                                    class="text-[10px] text-gray-400 font-mono"
                                >
                                    {{
                                        user.player_profile?.primary_position ||
                                        "CM"
                                    }}
                                    • #{{
                                        user.player_profile?.jersey_number ||
                                        "-"
                                    }}
                                </div>
                            </div>
                        </div>

                        <span
                            :class="[
                                'text-[10px] font-bold uppercase px-3 py-1 rounded-xl border',
                                selectedUserIds.includes(user.id)
                                    ? 'bg-indigo-600/20 text-indigo-400 border-indigo-500/40'
                                    : 'bg-gray-800 text-gray-500 border-gray-700',
                            ]"
                        >
                            {{
                                selectedUserIds.includes(user.id)
                                    ? "U Sastavu"
                                    : "Van Tima"
                            }}
                        </span>
                    </div>

                    <div
                        v-if="modalFilteredUsers.length === 0"
                        class="text-center py-8 text-gray-500 italic text-xs"
                    >
                        Nema pronađenih igrača.
                    </div>
                </div>

                <div
                    class="p-4 bg-gray-900/80 border-t border-gray-700/80 flex justify-between items-center"
                >
                    <span class="text-xs text-gray-400">
                        Izabrano:
                        <strong class="text-white">{{
                            selectedUserIds.length
                        }}</strong>
                        igrača
                    </span>
                    <div class="flex space-x-3">
                        <button
                            type="button"
                            @click="showAssignModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-400 hover:text-white"
                        >
                            Odustani
                        </button>
                        <button
                            @click="handleSaveMembers"
                            class="px-5 py-2 text-xs bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold shadow-lg transition"
                        >
                            Sačuvaj Sastav
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useTeamStore } from "../stores/team";
import { useUserStore } from "../stores/user";

const teamStore = useTeamStore();
const userStore = useUserStore();

const showCreateModal = ref(false);
const showAssignModal = ref(false);
const activeTeam = ref(null);
const selectedUserIds = ref([]);
const modalSearch = ref("");

const newTeam = reactive({
    name: "",
    age_group: "u15",
});

onMounted(() => {
    teamStore.fetchTeams();
    userStore.fetchUsers();
});

const totalPlayersCount = computed(() => {
    return teamStore.teams.reduce(
        (acc, t) => acc + (t.members?.length || 0),
        0,
    );
});

const unassignedPlayersCount = computed(() => {
    const assignedIds = new Set(
        teamStore.teams.flatMap((t) => (t.members || []).map((m) => m.id)),
    );
    return userStore.users.filter((u) => !assignedIds.has(u.id)).length;
});

const modalFilteredUsers = computed(() => {
    if (!modalSearch.value) return userStore.users;
    const q = modalSearch.value.toLowerCase();
    return userStore.users.filter((u) => u.name.toLowerCase().includes(q));
});

const handleCreateTeam = async () => {
    const success = await teamStore.createTeam(newTeam);
    if (success) {
        showCreateModal.value = false;
        newTeam.name = "";
        newTeam.age_group = "u15";
    }
};

const openAssignModal = (team) => {
    if (!team) return;
    activeTeam.value = team;
    selectedUserIds.value = Array.isArray(team.members)
        ? team.members.map((m) => m.id)
        : [];
    modalSearch.value = "";
    showAssignModal.value = true;
};

const toggleUserSelection = (userId) => {
    const index = selectedUserIds.value.indexOf(userId);
    if (index > -1) {
        selectedUserIds.value.splice(index, 1);
    } else {
        selectedUserIds.value.push(userId);
    }
};

const handleSaveMembers = async () => {
    if (!activeTeam.value?.id) return;

    const success = await teamStore.assignMembers(
        activeTeam.value.id,
        selectedUserIds.value,
    );
    if (success) {
        showAssignModal.value = false;
    }
};
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}
.custom-scroll::-webkit-scrollbar-track {
    background: rgba(15, 23, 42, 0.4);
    border-radius: 8px;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.4);
    border-radius: 8px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.8);
}
</style>
