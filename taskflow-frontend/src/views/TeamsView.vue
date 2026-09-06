<template>
  <div class="max-w-7xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-white">Ekipe i Starosne Grupe</h2>
        <p class="text-sm text-gray-400 mt-1">
          Upravljanje selekcijama akademije i dodeljivanje igrača
        </p>
      </div>

      <button
        @click="showCreateModal = true"
        class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2"
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
            d="M12 4v16m8-8H4"
          ></path>
        </svg>
        Nova Ekipa
      </button>
    </div>

    <!-- Prikaz Timova -->
    <div v-if="teamStore.loading" class="text-center py-12 text-gray-400">
      Učitavanje ekipa...
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="team in teamStore.teams"
        :key="team.id"
        class="bg-gray-800 border border-gray-700/80 rounded-xl p-5 shadow-xl flex flex-col justify-between"
      >
        <div>
          <div class="flex justify-between items-start mb-3">
            <h3 class="text-lg font-bold text-white">{{ team.name }}</h3>
            <span
              class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 text-xs font-mono font-bold uppercase px-2.5 py-1 rounded-md"
            >
              {{ team.age_group }}
            </span>
          </div>

          <!-- Spisak igrača u timu -->
          <div class="mt-4">
            <h4
              class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2"
            >
              Sastav ekipe ({{ team.members?.length || 0 }})
            </h4>

            <div
              v-if="team.members && team.members.length > 0"
              class="space-y-1.5 max-h-48 overflow-y-auto pr-1"
            >
              <div
                v-for="member in team.members"
                :key="member.id"
                class="flex justify-between items-center bg-gray-900/60 p-2 rounded-lg text-xs"
              >
                <span class="text-gray-200 font-medium">{{ member.name }}</span>
                <span class="text-indigo-400 font-mono font-bold"
                  >#{{ member.player_profile?.jersey_number || "-" }}</span
                >
              </div>
            </div>
            <p v-else class="text-xs text-gray-500 italic">
              Nema dodeljenih igrača u ovoj ekipi.
            </p>
          </div>
        </div>

        <button
          @click="openAssignModal(team)"
          class="mt-6 w-full py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-semibold rounded-lg transition"
        >
          Upravljaj Sastavom
        </button>
        <router-link 
        :to="`/teams/${team.id}`"
        class="mt-2 text-center block w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg transition"
      >
        Prikaži Detalje i Statistiku
      </router-link>
      </div>
    </div>

    <!-- Modal za Kreiranje Nove Ekipe -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50"
    >
      <div
        class="bg-gray-800 border border-gray-700 rounded-xl p-6 w-full max-w-md shadow-2xl"
      >
        <h3 class="text-xl font-bold text-white mb-4">Kreiraj Novu Ekipu</h3>

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
              placeholder="npr. U15 Pioniri A"
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
            />
          </div>

          <div>
            <label
              class="block text-xs font-semibold uppercase text-gray-400 mb-1"
              >Starosna Kategorija</label
            >
            <select
              v-model="newTeam.age_group"
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none"
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

          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
            <button
              type="button"
              @click="showCreateModal = false"
              class="px-4 py-2 text-sm text-gray-400 hover:text-white"
            >
              Odustani
            </button>
            <button
              type="submit"
              class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded font-medium"
            >
              Sačuvaj
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal za Upravljanje Sastavom (Dodavanje Igrača) -->
    <div
      v-if="showAssignModal && activeTeam"
      class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50"
    >
      <div
        class="bg-gray-800 border border-gray-700 rounded-xl p-6 w-full max-w-lg shadow-2xl"
      >
        <h3 class="text-xl font-bold text-white mb-2">
          Sastav za: {{ activeTeam?.name }}
        </h3>
        <p class="text-xs text-gray-400 mb-4">
          Štiklirajte igrače koje želite da dodelite ovoj ekipi
        </p>

        <div class="space-y-2 max-h-80 overflow-y-auto pr-2 mb-6">
          <div
            v-for="user in userStore.users"
            :key="user.id"
            class="flex items-center justify-between bg-gray-900/80 p-3 rounded-lg border border-gray-700/50 hover:border-indigo-500/50 transition cursor-pointer"
            @click="toggleUserSelection(user.id)"
          >
            <div class="flex items-center space-x-3">
              <input
                type="checkbox"
                :checked="selectedUserIds.includes(user.id)"
                class="rounded bg-gray-800 border-gray-600 text-indigo-600 focus:ring-indigo-500 w-4 h-4"
              />
              <div>
                <div class="text-sm font-semibold text-white">
                  {{ user.name }}
                </div>
                <div class="text-xs text-gray-400">
                  {{ user.player_profile?.primary_position || "N/A" }} | #{{
                    user.player_profile?.jersey_number || "-"
                  }}
                </div>
              </div>
            </div>
            <span class="text-xs font-bold uppercase text-indigo-400">
              {{ user.player_profile?.category || "Seniori" }}
            </span>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
          <button
            type="button"
            @click="showAssignModal = false"
            class="px-4 py-2 text-sm text-gray-400 hover:text-white"
          >
            Odustani
          </button>
          <button
            @click="handleSaveMembers"
            class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-semibold"
          >
            Sačuvaj Sastav
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useTeamStore } from "../stores/team";
import { useUserStore } from "../stores/user";

const teamStore = useTeamStore();
const userStore = useUserStore();

const showCreateModal = ref(false);
const showAssignModal = ref(false);
const activeTeam = ref(null);
const selectedUserIds = ref([]);

const newTeam = reactive({
  name: "",
  age_group: "u15",
});

onMounted(() => {
  teamStore.fetchTeams();
  userStore.fetchUsers();
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
  if (!team) return
  activeTeam.value = team
  // Učitavamo trenutno dodeljene ID-eve uz sigurnosnu proveru
  selectedUserIds.value = Array.isArray(team.members) ? team.members.map(m => m.id) : []
  showAssignModal.value = true
}

const handleSaveMembers = async () => {
  if (!activeTeam.value?.id) return

  const success = await teamStore.assignMembers(activeTeam.value.id, selectedUserIds.value)
  if (success) {
    showAssignModal.value = false
  }
}

const toggleUserSelection = (userId) => {
  const index = selectedUserIds.value.indexOf(userId);
  if (index > -1) {
    selectedUserIds.value.splice(index, 1);
  } else {
    selectedUserIds.value.push(userId);
  }
};
</script>
