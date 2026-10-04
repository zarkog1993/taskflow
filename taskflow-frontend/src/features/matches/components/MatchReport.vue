<template>
    <form @submit.prevent="handleSave" class="space-y-6">
        <fieldset :disabled="isSaving" class="min-w-0 space-y-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 sm:p-6"
            >
                <div>
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white">
                        Zapisnik Utakmice
                    </h1>
                    <p class="mt-2 text-base text-indigo-600 dark:text-indigo-400 font-semibold">
                        {{ match.is_home ? match.team?.name : match.opponent }}
                        <span class="px-2 font-mono">{{ statsForm.home_score }} : {{ statsForm.away_score }}</span>
                        {{ match.is_home ? match.opponent : match.team?.name }}
                    </p>
                </div>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ formatDate(match.scheduled_at) }}</span>
            </div>

            <nav
                aria-label="Sekcije zapisnika"
                class="flex flex-wrap gap-2 text-sm font-semibold"
            >
                <a href="#match-info" class="rounded-xl bg-white dark:bg-gray-800 px-4 py-2 text-indigo-700 dark:text-indigo-300 border border-gray-200 dark:border-gray-700">Info & Status</a>
                <a href="#match-squad" class="rounded-xl bg-white dark:bg-gray-800 px-4 py-2 text-indigo-700 dark:text-indigo-300 border border-gray-200 dark:border-gray-700">Sastav ({{ attendedCount }})</a>
                <a href="#match-stats" class="rounded-xl bg-white dark:bg-gray-800 px-4 py-2 text-indigo-700 dark:text-indigo-300 border border-gray-200 dark:border-gray-700">Strelci & Asistencije</a>
            </nav>

            <section id="match-info" class="scroll-mt-24 space-y-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Info & Status</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label
                            class="block text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400 mb-1"
                        >Status Utakmice</label
                        >
                        <AppSelect
                            v-model="statsForm.status"
                            class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white outline-none focus:border-indigo-500"
                        >
                            <option value="scheduled">Zakazana</option>
                            <option value="completed">
                                Odigrana (Završena)
                            </option>
                            <option value="canceled">Otkazana</option>
                        </AppSelect>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400 mb-1"
                        >Domaći Golovi</label
                        >
                        <input
                            v-model.number="statsForm.home_score"
                            type="number"
                            min="0"
                            required
                            aria-label="Domaći golovi"
                            class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-mono font-bold text-center"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400 mb-1"
                        >Gostujući Golovi</label
                        >
                        <input
                            v-model.number="statsForm.away_score"
                            type="number"
                            min="0"
                            required
                            aria-label="Gostujući golovi"
                            class="w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 text-xs text-gray-900 dark:text-white font-mono font-bold text-center"
                        />
                    </div>
                </div>

                <div
                    class="p-4 bg-white/60 dark:bg-gray-900/60 rounded-xl border border-gray-200/50 dark:border-gray-700/50 space-y-2 text-xs text-gray-700 dark:text-gray-300"
                >
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Datum i vreme:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{
                                formatDate(match.scheduled_at)
                            }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Teren / Lokacija:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{
                                match.location || "Nije uneto"
                            }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Uloga:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{
                                match.is_home ? "Domaćin" : "Gost"
                            }}</span>
                    </div>
                </div>
            </section>

            <section id="match-squad" class="scroll-mt-24 space-y-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Sastav ({{ attendedCount }})</h2>
                <div class="flex justify-between items-center px-1">
                    <span class="text-xs text-gray-600 dark:text-gray-400"
                    >Štikliraj igrače koji su igrali na ovoj utakmici:</span
                    >
                    <button
                        type="button"
                        @click="toggleSelectAllSquad"
                        class="text-[10px] text-indigo-600 dark:text-indigo-400 hover:underline font-bold cursor-pointer"
                    >
                        {{
                            allSquadSelected ? "Poništi sve" : "Označi sve"
                        }}
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-2 lg:grid-cols-2">
                    <div
                        v-if="!statsForm.players.length"
                        class="text-center py-8 bg-white/40 dark:bg-gray-900/40 rounded-xl border border-gray-200 dark:border-gray-800"
                    >
                        <p class="text-xs text-gray-500 italic">
                            Ekipa nema unetih igrača. Dodajte igrače u sastav ekipe.
                        </p>
                    </div>

                    <div
                        v-for="player in statsForm.players"
                        :key="player.id"
                        @click="player.attended = !player.attended"
                        class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition select-none"
                        :class="
                            player.attended
                                ? 'bg-indigo-50/70 dark:bg-indigo-950/70 border-indigo-200/60 dark:border-indigo-700/60'
                                : 'bg-white/60 dark:bg-gray-900/60 border-gray-200 dark:border-gray-800'
                        "
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                :aria-label="`Igrao: ${player.name}`"
                                v-model="player.attended"
                                class="w-4 h-4 text-indigo-600 rounded bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600"
                                @click.stop
                            />
                            <div>
                                <p class="text-xs font-bold text-gray-900 dark:text-white">
                                    {{ player.name }}
                                </p>
                                <p class="text-[10px] text-gray-600 dark:text-gray-400">
                                    #{{ player.jersey_number || "-" }} •
                                    {{ player.position || "N/A" }}
                                    <span
                                        v-if="player.rsvp_status"
                                        :class="RSVP_LABELS[player.rsvp_status]?.class"
                                        class="ml-1 font-bold"
                                    >
                                        • {{ RSVP_LABELS[player.rsvp_status]?.label }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <span
                            class="text-[10px] font-bold px-2 py-0.5 rounded"
                            :class="
                                player.attended
                                    ? 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800'
                                    : 'bg-gray-100 dark:bg-gray-800 text-gray-500'
                            "
                        >
                            {{ player.attended ? "Igrao" : "Nije igrao" }}
                        </span>
                    </div>
                </div>
            </section>

            <section id="match-stats" class="scroll-mt-24 space-y-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 sm:p-6">
                <div class="flex items-center justify-between px-1">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Učinak na utakmici</h2>
                        <p class="text-[11px] text-gray-600 dark:text-gray-400">Igrači koji su postigli gol ili asistirali</p>
                    </div>
                    <button
                        type="button"
                        @click="isEditingStats = !isEditingStats"
                        class="flex items-center gap-1.5 rounded-lg border border-indigo-500/30 bg-indigo-500/10 px-3 py-2 text-[11px] font-bold text-indigo-700 dark:text-indigo-300 transition hover:border-indigo-400/50 hover:bg-indigo-500/20 cursor-pointer"
                    >
                        <span aria-hidden="true">{{ isEditingStats ? "✓" : "✎" }}</span>
                        {{ isEditingStats ? "Prikaži učinak" : "Izmeni učinak" }}
                    </button>
                </div>

                <div
                    v-if="!isEditingStats"
                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                >
                    <div v-if="scorers.length" class="overflow-hidden rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/[0.08] to-white/80 dark:to-gray-900/80 shadow-lg shadow-black/10">
                        <div class="flex items-center justify-between border-b border-emerald-500/15 px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-emerald-400/20 bg-emerald-400/10 text-lg">⚽</span>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Golovi</h4>
                                    <p class="text-[10px] uppercase tracking-wider text-emerald-700/70 dark:text-emerald-300/70">{{ totalGoals }} gol{{ totalGoals === 1 ? "" : "ova" }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700/70 dark:text-emerald-300/70">Postignuto</span>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-white/[0.06] px-4">
                            <div
                                v-for="player in scorers"
                                :key="player.id"
                                class="flex items-center justify-between gap-3 py-3"
                            >
                                <span class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ player.name }}</span>
                                <span class="shrink-0 rounded-lg border border-emerald-400/20 bg-emerald-400/10 px-2.5 py-1 text-xs font-bold tabular-nums text-emerald-700 dark:text-emerald-300">
                                    {{ player.goals }} {{ player.goals === 1 ? "gol" : "golova" }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-if="assisters.length" class="overflow-hidden rounded-2xl border border-sky-500/20 bg-gradient-to-br from-sky-500/[0.08] to-white/80 dark:to-gray-900/80 shadow-lg shadow-black/10">
                        <div class="flex items-center justify-between border-b border-sky-500/15 px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-sky-400/20 bg-sky-400/10 text-lg">🎯</span>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Asistencije</h4>
                                    <p class="text-[10px] uppercase tracking-wider text-sky-700/70 dark:text-sky-300/70">{{ totalAssists }} asistencij{{ totalAssists === 1 ? "a" : "e" }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700/70 dark:text-sky-300/70">Kreirano</span>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-white/[0.06] px-4">
                            <div
                                v-for="player in assisters"
                                :key="player.id"
                                class="flex items-center justify-between gap-3 py-3"
                            >
                                <span class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">{{ player.name }}</span>
                                <span class="shrink-0 rounded-lg border border-sky-400/20 bg-sky-400/10 px-2.5 py-1 text-xs font-bold tabular-nums text-sky-700 dark:text-sky-300">
                                    {{ player.assists }} {{ player.assists === 1 ? "asistencija" : "asistencije" }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-if="!contributingPlayers.length" class="col-span-full rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 bg-white/50 dark:bg-gray-900/50 px-6 py-10 text-center">
                        <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-xl">⚽</span>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Još nema zabeleženog učinka</p>
                        <p class="mt-1 text-xs text-gray-500">Unesite golove ili asistencije za prikaz igrača.</p>
                    </div>
                </div>

                <div v-else-if="attendedPlayers.length" class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                    <div
                        v-for="player in attendedPlayers"
                        :key="player.id"
                        class="flex flex-wrap gap-3 items-center justify-between bg-white/80 dark:bg-gray-900/80 p-4 rounded-xl border border-gray-200/60 dark:border-gray-700/60"
                    >
                        <div>
                            <p class="text-xs font-bold text-gray-900 dark:text-white">{{ player.name }}</p>
                            <p class="text-[10px] text-gray-600 dark:text-gray-400">#{{ player.jersey_number || "-" }}</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs" title="Postignuti Golovi">⚽</span>
                                <input
                                    type="number"
                                    min="0"
                                    required
                                    :aria-label="`Golovi: ${player.name}`"
                                    v-model.number="player.goals"
                                    class="w-12 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-center text-xs py-1.5 text-gray-900 dark:text-white font-mono font-bold"
                                />
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs" title="Asistencije">🎯</span>
                                <input
                                    type="number"
                                    min="0"
                                    required
                                    :aria-label="`Asistencije: ${player.name}`"
                                    v-model.number="player.assists"
                                    class="w-12 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-center text-xs py-1.5 text-gray-900 dark:text-white font-mono font-bold"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else-if="isEditingStats" class="text-center py-8 bg-white/40 dark:bg-gray-900/40 rounded-xl border border-gray-200 dark:border-gray-800">
                    <p class="text-xs text-gray-500 italic">
                        Nijedan igrač nije označen kao prisutan. Prvo označite igrače u sekciji "Sastav".
                    </p>
                </div>
            </section>

            <div
                class="flex justify-end gap-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4"
            >
                <button
                    type="button"
                    @click="$emit('close')"
                    class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white cursor-pointer"
                >
                    Odustani
                </button>
                <button
                    type="submit"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2 rounded-xl transition cursor-pointer"
                >
                    {{ isSaving ? "Čuvanje..." : "Sačuvaj Zapisnik" }}
                </button>
            </div>
        </fieldset>
    </form>
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
    isSaving: {
        type: Boolean,
        default: false,
    },
});

const isEditingStats = ref(false);

const RSVP_LABELS = {
    accepted: { label: "Potvrdio dolazak", class: "text-emerald-700 dark:text-emerald-400" },
    declined: { label: "Otkazao", class: "text-red-600 dark:text-red-400" },
    pending: { label: "Bez odgovora", class: "text-amber-700 dark:text-amber-400" },
};

const attendedPlayers = computed(() => {
    return props.statsForm.players.filter((p) => p.attended);
});

const contributingPlayers = computed(() =>
    attendedPlayers.value.filter((player) => player.goals > 0 || player.assists > 0),
);

const scorers = computed(() =>
    contributingPlayers.value.filter((player) => player.goals > 0),
);

const assisters = computed(() =>
    contributingPlayers.value.filter((player) => player.assists > 0),
);

const totalGoals = computed(() => scorers.value.reduce((total, player) => total + Number(player.goals), 0));
const totalAssists = computed(() => assisters.value.reduce((total, player) => total + Number(player.assists), 0));

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