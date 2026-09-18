<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- Zaglavlje -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-white">Kalendar Aktivnosti</h1>
                <p class="text-xs text-gray-400">Pregled svih zakazanih treninga i utakmica</p>
            </div>
            <button
                @click="openCreateModal"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/30 cursor-pointer"
            >
                + Zakaži Događaj
            </button>
        </div>

        <!-- Navigacija Kroz Mesec -->
        <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 flex justify-between items-center">
            <button
                @click="prevMonth"
                class="bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs px-3 py-1.5 rounded-xl border border-gray-700 transition cursor-pointer"
            >
                ← Prethodni
            </button>
            <h2 class="text-base font-bold text-white uppercase tracking-wider capitalize">
                {{ currentMonthName }} {{ currentYear }}
            </h2>
            <button
                @click="nextMonth"
                class="bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs px-3 py-1.5 rounded-xl border border-gray-700 transition cursor-pointer"
            >
                Sledeći →
            </button>
        </div>

        <!-- Legenda -->
        <div class="flex gap-4 text-xs">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-indigo-500 border border-indigo-400"></span>
                <span class="text-gray-300">Trening</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 border border-emerald-400"></span>
                <span class="text-gray-300">Utakmica</span>
            </div>
        </div>

        <!-- Mesečni Grid -->
        <div class="bg-gray-800/60 border border-gray-700/80 rounded-2xl overflow-hidden shadow-xl">
            <!-- Dani u Nedelji -->
            <div class="grid grid-cols-7 bg-gray-900/90 text-center border-b border-gray-700/80 py-2.5 text-xs font-bold text-gray-400 uppercase">
                <div>Pon</div><div>Uto</div><div>Sre</div><div>Čet</div><div>Pet</div><div>Sub</div><div>Ned</div>
            </div>

            <!-- Mreža Dana -->
            <div class="grid grid-cols-7 auto-rows-fr gap-px bg-gray-700/40">
                <div
                    v-for="(day, index) in calendarDays"
                    :key="index"
                    :class="[
                        'min-h-[110px] p-2 bg-gray-900/90 transition',
                        day.isCurrentMonth ? 'text-white' : 'text-gray-600 bg-gray-950/40',
                        day.isToday ? 'ring-2 ring-indigo-500/50 bg-indigo-950/20' : '',
                    ]"
                >
                    <div class="flex justify-between items-center mb-1">
                        <span
                            class="text-xs font-bold font-mono"
                            :class="day.isToday ? 'bg-indigo-600 text-white w-5 h-5 rounded-full flex items-center justify-center' : ''"
                        >
                            {{ day.date.getDate() }}
                        </span>
                    </div>

                    <!-- Događaji za Taj Dan (Klik otvara detalje) -->
                    <div class="space-y-1">
                        <div
                            v-for="event in day.events"
                            :key="event.id"
                            @click="openEventDetails(event)"
                            :class="[
                                'text-[10px] p-1.5 rounded-lg border truncate cursor-pointer transition font-medium',
                                event.type === 'training'
                                    ? 'bg-indigo-950/80 text-indigo-200 border-indigo-700/60 hover:bg-indigo-900/90'
                                    : 'bg-emerald-950/80 text-emerald-200 border-emerald-700/60 hover:bg-emerald-900/90',
                            ]"
                        >
                            <div class="font-bold flex items-center gap-1">
                                <span>{{ event.type === "training" ? "⚽" : "🏆" }}</span>
                                <span>{{ event.time }}</span>
                            </div>
                            <div class="truncate">{{ event.title }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL ZA ZAKAZIVANJE -->
        <div
            v-if="showCreateModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
        >
            <div class="bg-gray-800 border border-gray-700 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <h3 class="text-sm font-bold text-white">Zakaži Novi Događaj</h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-white">✕</button>
                </div>

                <form @submit.prevent="handleCreateEvent" class="space-y-4">
                    <!-- Tip Događaja -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Tip Događaja</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                @click="eventForm.eventType = 'training'"
                                :class="eventForm.eventType === 'training' ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-gray-900 text-gray-400 border-gray-700'"
                                class="py-2 text-xs font-bold rounded-xl border transition cursor-pointer"
                            >
                                ⚽ Trening
                            </button>
                            <button
                                type="button"
                                @click="eventForm.eventType = 'match'"
                                :class="eventForm.eventType === 'match' ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-gray-900 text-gray-400 border-gray-700'"
                                class="py-2 text-xs font-bold rounded-xl border transition cursor-pointer"
                            >
                                🏆 Utakmica
                            </button>
                        </div>
                    </div>

                    <!-- Ekipa -->
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Ekipa</label>
                        <select
                            v-model="eventForm.team_id"
                            @change="onTeamChange"
                            required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer"
                        >
                            <option value="" disabled>Izaberite ekipu...</option>
                            <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                        </select>
                    </div>

                    <!-- Polja za Utakmicu -->
                    <template v-if="eventForm.eventType === 'match'">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Protivnik</label>
                            <input
                                v-model="eventForm.opponent"
                                type="text"
                                placeholder="npr. FK Napredak"
                                required
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Teren</label>
                            <select
                                v-model="eventForm.is_home"
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                            >
                                <option :value="true">Domaćin</option>
                                <option :value="false">Gost</option>
                            </select>
                        </div>
                    </template>

                    <!-- Polja za Trening -->
                    <template v-else>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Naziv / Trenažni Cilj</label>
                            <input
                                v-model="eventForm.title"
                                type="text"
                                placeholder="npr. Taktička priprema i šut"
                                required
                                class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                            />
                        </div>
                    </template>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Datum i Vreme</label>
                        <input
                            v-model="eventForm.scheduled_at"
                            type="datetime-local"
                            required
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Lokacija</label>
                        <input
                            v-model="eventForm.location"
                            type="text"
                            placeholder="npr. Glavni teren"
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3.5 py-2.5 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <!-- Lista Igrača za Sastav -->
                    <div v-if="availablePlayers.length" class="space-y-1.5 pt-2">
                        <div class="flex justify-between items-center">
                            <label class="block text-xs font-bold uppercase text-gray-400">
                                Pozvani Igrači ({{ selectedPlayersCount }})
                            </label>
                            <button
                                type="button"
                                @click="toggleSelectAll"
                                class="text-[10px] text-indigo-400 hover:text-indigo-300 font-bold cursor-pointer"
                            >
                                {{ allSelected ? "Poništi sve" : "Označi sve" }}
                            </button>
                        </div>

                        <div class="max-h-36 overflow-y-auto space-y-1.5 pr-1 bg-gray-900/80 p-2 rounded-xl border border-gray-700/60">
                            <div
                                v-for="player in availablePlayers"
                                :key="player.id"
                                @click="player.selected = !player.selected"
                                class="flex items-center justify-between p-2 rounded-lg cursor-pointer transition select-none"
                                :class="player.selected ? 'bg-indigo-950/80 border border-indigo-700/60' : 'bg-gray-800/40 border border-transparent'"
                            >
                                <div class="flex items-center gap-2.5">
                                    <input
                                        type="checkbox"
                                        v-model="player.selected"
                                        class="w-4 h-4 text-indigo-600 rounded bg-gray-800 border-gray-600"
                                        @click.stop
                                    />
                                    <span class="text-xs font-bold text-white">{{ player.name }}</span>
                                </div>
                                <span class="text-[10px] text-gray-400 font-mono">
                                    #{{ player.player_profile?.jersey_number || "-" }} • {{ player.player_profile?.primary_position || "N/A" }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="eventForm.team_id" class="text-xs text-gray-500 italic py-2 text-center">
                        Nema registrovanih igrača u ovoj ekipi.
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">
                            Odustani
                        </button>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                            Sačuvaj
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL ZA DETALJE DOGAĐAJA I ODZIV IGRAČA -->
        <div v-if="selectedEvent" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl space-y-6">
                <!-- Zaglavlje Modala -->
                <div class="flex justify-between items-start border-b border-gray-800 pb-4">
                    <div>
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-indigo-400 bg-indigo-950 px-2.5 py-1 rounded-md border border-indigo-800">
                            {{ selectedEvent.team?.name || 'Svi timovi' }}
                        </span>
                        <h3 class="text-2xl font-black text-white mt-2">{{ selectedEvent.title }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            📍 {{ selectedEvent.location || 'Glavni teren' }} | ⏰ {{ formatDate(selectedEvent.scheduled_at) }}
                        </p>
                    </div>
                    <button @click="selectedEvent = null" class="text-gray-400 hover:text-white text-lg font-bold">✕</button>
                </div>

                <!-- Lista Odziva Igrača -->
                <div class="space-y-4">
                    <h4 class="text-xs font-semibold uppercase text-gray-400 tracking-wider">
                        Status Prisustva ({{ selectedEvent.users?.length || 0 }})
                    </h4>

                    <div v-if="selectedEvent.users && selectedEvent.users.length > 0" class="max-h-60 overflow-y-auto space-y-2 pr-1">
                        <div
                            v-for="player in selectedEvent.users"
                            :key="player.id"
                            class="flex items-center justify-between p-3 bg-gray-800/60 rounded-xl border border-gray-700/50"
                        >
                            <div class="flex items-center space-x-3">
                                <img
                                    :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(player.name)}&background=312e81&color=c7d2fe&size=64`"
                                    class="w-8 h-8 rounded-full border border-indigo-500/30"
                                />
                                <span class="text-sm font-semibold text-white">{{ player.name }}</span>
                            </div>

                            <!-- Status Bedž na osnovu pivot.attended -->
                            <span
                                v-if="player.pivot?.attended === 1 || player.pivot?.attended === true"
                                class="text-[11px] font-bold text-emerald-400 bg-emerald-950/80 px-2.5 py-1 rounded-lg border border-emerald-800 flex items-center gap-1"
                            >
                                ✅ Dolazi
                            </span>
                            <span
                                v-else-if="player.pivot?.attended === 0 || player.pivot?.attended === false"
                                class="text-[11px] font-bold text-rose-400 bg-rose-950/80 px-2.5 py-1 rounded-lg border border-rose-800 flex items-center gap-1"
                            >
                                ❌ Otkazao
                            </span>
                            <span
                                v-else
                                class="text-[11px] font-bold text-amber-400 bg-amber-950/80 px-2.5 py-1 rounded-lg border border-amber-800 flex items-center gap-1"
                            >
                                ⏳ Čeka se odgovor
                            </span>
                        </div>
                    </div>

                    <div v-else class="text-center py-6 bg-gray-800/30 rounded-xl border border-dashed border-gray-700 text-xs text-gray-500 italic">
                        Nema pozvanih igrača za ovaj događaj.
                    </div>
                </div>

                <!-- Dugme za Zatvaranje -->
                <div class="pt-2 border-t border-gray-800 flex justify-end">
                    <button
                        @click="selectedEvent = null"
                        class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl transition"
                    >
                        Zatvori
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "../services/api";

const currentDate = ref(new Date());
const events = ref([]);
const teams = ref([]);
const availablePlayers = ref([]);
const showCreateModal = ref(false);
const selectedEvent = ref(null);

const eventForm = ref({
    eventType: "match",
    team_id: "",
    title: "",
    opponent: "",
    is_home: true,
    scheduled_at: "",
    location: "",
});

const currentYear = computed(() => currentDate.value.getFullYear());
const currentMonthName = computed(() =>
    currentDate.value.toLocaleString("sr-RS", { month: "long" })
);

const prevMonth = () => {
    currentDate.value = new Date(
        currentDate.value.getFullYear(),
        currentDate.value.getMonth() - 1,
        1
    );
};

const nextMonth = () => {
    currentDate.value = new Date(
        currentDate.value.getFullYear(),
        currentDate.value.getMonth() + 1,
        1
    );
};

const fetchAllData = async () => {
    try {
        const [trainingsRes, matchesRes, teamsRes] = await Promise.all([
            api.get("/training-sessions"),
            api.get("/matches"),
            api.get("/teams"),
        ]);

        teams.value = teamsRes.data.data;

        // Mapiramo i čuvamo kompletne objekte uključujući users i team relacije
        const formattedTrainings = (trainingsRes.data.data || []).map((t) => ({
            ...t,
            type: "training",
            title: t.title,
            location: t.location,
            team: t.team,
            users: t.users || t.attendees || [],
            scheduled_at: new Date(t.scheduled_at),
            time: new Date(t.scheduled_at).toLocaleTimeString("sr-RS", {
                hour: "2-digit",
                minute: "2-digit",
            }),
        }));

        const formattedMatches = (matchesRes.data.data || []).map((m) => ({
            ...m,
            type: "match",
            title: `vs ${m.opponent}`,
            location: m.location,
            team: m.team,
            users: m.users || m.players || [],
            scheduled_at: new Date(m.scheduled_at),
            time: new Date(m.scheduled_at).toLocaleTimeString("sr-RS", {
                hour: "2-digit",
                minute: "2-digit",
            }),
        }));

        events.value = [...formattedTrainings, ...formattedMatches];
    } catch (err) {
        console.error("Greška pri učitavanju kalendara:", err);
    }
};

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);

    let startingDayOfWeek = firstDayOfMonth.getDay() - 1;
    if (startingDayOfWeek === -1) startingDayOfWeek = 6;

    const days = [];
    const today = new Date();

    for (let i = startingDayOfWeek; i > 0; i--) {
        const d = new Date(year, month, 1 - i);
        days.push({
            date: d,
            isCurrentMonth: false,
            isToday: false,
            events: [],
        });
    }

    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const d = new Date(year, month, i);
        const dayEvents = events.value.filter((e) => {
            return (
                e.scheduled_at.getDate() === d.getDate() &&
                e.scheduled_at.getMonth() === d.getMonth() &&
                e.scheduled_at.getFullYear() === d.getFullYear()
            );
        });

        const isToday =
            d.getDate() === today.getDate() &&
            d.getMonth() === today.getMonth() &&
            d.getFullYear() === today.getFullYear();

        days.push({
            date: d,
            isCurrentMonth: true,
            isToday,
            events: dayEvents,
        });
    }

    return days;
});

const loadPlayersForTeam = (teamId) => {
    if (!teamId) {
        availablePlayers.value = [];
        return;
    }

    const selectedTeam = teams.value.find(
        (t) => t.id === Number(teamId) || t.id === teamId
    );
    const teamUsers = selectedTeam?.users || selectedTeam?.players || [];

    availablePlayers.value = teamUsers.map((user) => ({
        ...user,
        selected: true,
    }));
};

const onTeamChange = () => {
    loadPlayersForTeam(eventForm.value.team_id);
};

const openCreateModal = () => {
    const defaultTeamId = teams.value[0]?.id || "";
    eventForm.value = {
        eventType: "match",
        team_id: defaultTeamId,
        title: "",
        opponent: "",
        is_home: true,
        scheduled_at: "",
        location: "",
    };

    loadPlayersForTeam(defaultTeamId);
    showCreateModal.value = true;
};

const selectedPlayersCount = computed(
    () => availablePlayers.value.filter((p) => p.selected).length
);
const allSelected = computed(
    () =>
        availablePlayers.value.length > 0 &&
        availablePlayers.value.every((p) => p.selected)
);

const toggleSelectAll = () => {
    const targetState = !allSelected.value;
    availablePlayers.value.forEach((p) => (p.selected = targetState));
};

const handleCreateEvent = async () => {
    try {
        const selectedUserIds = availablePlayers.value
            .filter((p) => p.selected)
            .map((p) => p.id);

        if (eventForm.value.eventType === "training") {
            await api.post("/training-sessions", {
                team_id: eventForm.value.team_id,
                title: eventForm.value.title,
                scheduled_at: eventForm.value.scheduled_at,
                location: eventForm.value.location,
                attendees: selectedUserIds,
            });
        } else {
            const matchRes = await api.post("/matches", {
                team_id: eventForm.value.team_id,
                opponent: eventForm.value.opponent,
                is_home: eventForm.value.is_home,
                scheduled_at: eventForm.value.scheduled_at,
                location: eventForm.value.location,
            });

            const matchId = matchRes.data.data.id;

            const matchPlayers = availablePlayers.value.map((p) => ({
                id: p.id,
                attended: p.selected,
                goals: 0,
                assists: 0,
            }));

            await api.put(`/matches/${matchId}/stats`, {
                status: "scheduled",
                home_score: null,
                away_score: null,
                players: matchPlayers,
            });
        }

        showCreateModal.value = false;
        await fetchAllData();
    } catch (err) {
        alert(err.response?.data?.message || "Greška pri kreiranju događaja");
    }
};

const openEventDetails = (event) => {
    selectedEvent.value = event;
};

const formatDate = (dateObj) => {
    if (!dateObj) return "";
    const d = new Date(dateObj);
    return d.toLocaleString("sr-RS", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

onMounted(() => {
    fetchAllData();
});
</script>