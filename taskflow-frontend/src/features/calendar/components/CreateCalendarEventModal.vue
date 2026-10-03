<!-- Modal za zakazivanje novog događaja (trening ili utakmica) sa selekcijom igrača -->
<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 overflow-y-auto"
    >
        <div
            class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5 my-8 max-h-[90vh] overflow-y-auto text-gray-900 dark:text-white"
        >
            <!-- Zaglavlje Modala -->
            <div
                class="flex justify-between items-center pb-3 border-b border-slate-200 dark:border-slate-800"
            >
                <h3 class="text-sm font-bold text-gray-900 dark:text-white tracking-wide">
                    Zakaži Novi Događaj
                </h3>
                <button
                    type="button"
                    @click="$emit('close')"
                    class="text-slate-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                >
                    ✕
                </button>
            </div>

            <form @submit.prevent="$emit('submit')" class="space-y-4">
                <!-- Tip Događaja (Trening / Utakmica) -->
                <div>
                    <label
                        class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5"
                        >Tip Događaja</label
                    >
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            @click="form.eventType = 'training'"
                            :class="
                                form.eventType === 'training'
                                    ? 'bg-indigo-600 text-white border-indigo-500 font-bold shadow-lg shadow-indigo-600/20'
                                    : 'bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:text-gray-900 dark:hover:text-white'
                            "
                            class="py-2.5 text-xs rounded-xl border transition cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            ⚽ Trening
                        </button>
                        <button
                            type="button"
                            @click="form.eventType = 'match'"
                            :class="
                                form.eventType === 'match'
                                    ? 'bg-emerald-600 text-white border-emerald-500 font-bold shadow-lg shadow-emerald-600/20'
                                    : 'bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:text-gray-900 dark:hover:text-white'
                            "
                            class="py-2.5 text-xs rounded-xl border transition cursor-pointer flex items-center justify-center gap-1.5"
                        >
                            🏆 Utakmica
                        </button>
                    </div>
                </div>

                <!-- Ekipa -->
                <div>
                    <label
                        class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5"
                        >Ekipa</label
                    >
                    <AppSelect
                        v-model="form.team_id"
                        @change="$emit('team-change')"
                        required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 dark:text-white outline-none focus:border-indigo-500 transition cursor-pointer"
                    >
                        <option value="" disabled>Izaberite ekipu...</option>
                        <option
                            v-for="team in teams"
                            :key="team.id"
                            :value="team.id"
                        >
                            {{ team.name }}
                        </option>
                    </AppSelect>
                </div>

                <!-- Dinamička Polja za Utakmicu -->
                <template v-if="form.eventType === 'match'">
                    <div>
                        <label
                            class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5"
                            >Protivnik</label
                        >
                        <input
                            v-model="form.opponent"
                            type="text"
                            placeholder="npr. FK Napredak"
                            required
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 dark:text-white outline-none focus:border-indigo-500 transition"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5"
                            >Teren</label
                        >
                        <AppSelect
                            v-model="form.is_home"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 dark:text-white outline-none focus:border-indigo-500 transition cursor-pointer"
                        >
                            <option :value="true">Domaćin</option>
                            <option :value="false">Gost</option>
                        </AppSelect>
                    </div>
                </template>

                <!-- Dinamička Polja za Trening -->
                <template v-else>
                    <div>
                        <label
                            class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5"
                            >Naziv / Trenažni Cilj</label
                        >
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="npr. Taktička priprema i šut"
                            required
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 dark:text-white outline-none focus:border-indigo-500 transition"
                        />
                    </div>
                </template>

                <!-- DATUM I VREME (Custom Srpska Latinica + 24h Format) -->
                <div class="space-y-1.5">
                    <label
                        class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400"
                    >
                        Datum i Vreme
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Custom Date Picker na Srpskom -->
                        <div class="relative">
                            <CustomDatePicker v-model="form.date" />
                        </div>

                        <!-- Izbor Vremena u 24h Format (svakih 15 min) -->
                        <div class="relative">
                            <AppSelect
                                v-model="form.time"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs font-medium text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 transition appearance-none cursor-pointer"
                                required
                            >
                                <option value="" disabled>
                                    Sat (24h)...
                                </option>
                                <option
                                    v-for="timeSlot in timeSlots24h"
                                    :key="timeSlot"
                                    :value="timeSlot"
                                >
                                    🕒 {{ timeSlot }} h
                                </option>
                            </AppSelect>
                            <div
                                class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-600 dark:text-slate-400 text-[10px]"
                            >
                                ▼
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lokacija -->
                <div>
                    <label
                        class="block text-xs font-bold uppercase text-slate-600 dark:text-slate-400 mb-1.5"
                        >Lokacija</label
                    >
                    <input
                        v-model="form.location"
                        type="text"
                        placeholder="npr. Glavni teren"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-gray-900 dark:text-white outline-none focus:border-indigo-500 transition"
                    />
                </div>

                <!-- Lista Igrača za Sastav -->
                <PlayerSelector
                    :players="players"
                    :selected-count="selectedPlayersCount"
                    :all-selected="allSelected"
                    :team-selected="form.team_id"
                    @toggle-select-all="$emit('toggle-select-all')"
                />

                <!-- Podnožje sa Akcijama -->
                <div
                    class="flex justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800"
                >
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition"
                    >
                        Odustani
                    </button>
                    <button
                        type="submit"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/20"
                    >
                        Sačuvaj
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, watch } from "vue";
import PlayerSelector from "./PlayerSelector.vue";
import CustomDatePicker from "../../../components/CustomDatePicker.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    teams: {
        type: Array,
        default: () => [],
    },
    players: {
        type: Array,
        default: () => [],
    },
    selectedPlayersCount: {
        type: Number,
        required: true,
    },
    allSelected: {
        type: Boolean,
        required: true,
    },
});

defineEmits(["close", "submit", "team-change", "toggle-select-all"]);

// Osiguravamo podrazumevane vrednosti ako u formi nisu prosleđeni date i time
if (!props.form.date) {
    props.form.date = new Date().toISOString().slice(0, 10);
}
if (!props.form.time) {
    props.form.time = "18:00";
}

// Generiše termine na svakih 15 minuta u 24-časovnom formatu (07:00 - 22:45)
const timeSlots24h = computed(() => {
    const slots = [];
    for (let hour = 7; hour <= 22; hour++) {
        for (let min = 0; min < 60; min += 15) {
            const h = hour.toString().padStart(2, "0");
            const m = min.toString().padStart(2, "0");
            slots.push(`${h}:${m}`);
        }
    }
    return slots;
});

// Automatski spaja date i time u scheduled_at format pre slanja na backend
watch(
    () => [props.form.date, props.form.time],
    ([newDate, newTime]) => {
        if (newDate && newTime) {
            props.form.scheduled_at = `${newDate} ${newTime}:00`;
        }
    },
    { immediate: true }
);
</script>