<template>
    <div class="relative w-full">
        <!-- Prikaz i gumb za otvaranje kalendara -->
        <button
            type="button"
            @click="isOpen = !isOpen"
            class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-4 py-2.5 text-sm font-medium text-white flex justify-between items-center focus:outline-none focus:border-indigo-500 transition shadow-inner"
        >
            <span class="font-mono">{{ formattedDisplayDate }}</span>
            <span class="text-slate-400 text-xs">📅</span>
        </button>

        <!-- Pop-up Kalendar na Srpskoj Latinici -->
        <div
            v-if="isOpen"
            class="absolute z-50 mt-2 p-4 bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl w-72 text-white font-sans left-0"
        >
            <!-- Navigacija po mjesecima i godinama -->
            <div
                class="flex justify-between items-center mb-4 pb-2 border-b border-slate-800"
            >
                <button
                    type="button"
                    @click="prevMonth"
                    class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition"
                >
                    ◄
                </button>
                <span class="font-bold text-sm text-indigo-400 tracking-wide">
                    {{ monthNames[currentMonth] }} {{ currentYear }}
                </span>
                <button
                    type="button"
                    @click="nextMonth"
                    class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition"
                >
                    ►
                </button>
            </div>

            <!-- Dani u tjednu -->
            <div
                class="grid grid-cols-7 gap-1 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2"
            >
                <span v-for="day in weekDays" :key="day">{{ day }}</span>
            </div>

            <!-- Mreža dana u mjesecu -->
            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                <button
                    v-for="blank in firstDayOfWeek"
                    :key="'b-' + blank"
                    disabled
                    class="h-8 w-8"
                ></button>

                <button
                    v-for="day in daysInMonth"
                    :key="day"
                    type="button"
                    @click="selectDay(day)"
                    :class="[
                        'h-8 w-8 rounded-xl font-medium transition flex items-center justify-center',
                        isSelected(day)
                            ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30'
                            : 'hover:bg-slate-800 text-slate-300',
                    ]"
                >
                    {{ day }}
                </button>
            </div>

            <!-- Podnožje: "Danas" i "Zatvori" -->
            <div
                class="mt-4 pt-3 border-t border-slate-800 flex justify-between items-center text-xs"
            >
                <button
                    type="button"
                    @click="selectToday"
                    class="text-indigo-400 font-semibold hover:text-indigo-300 transition"
                >
                    Danas
                </button>
                <button
                    type="button"
                    @click="isOpen = false"
                    class="text-slate-400 hover:text-white transition"
                >
                    Zatvori
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: () => new Date().toISOString().slice(0, 10),
    },
});

const emit = defineEmits(["update:modelValue"]);

const isOpen = ref(false);

const monthNames = [
    "Januar",
    "Februar",
    "Mart",
    "April",
    "Maj",
    "Jun",
    "Jul",
    "Avgust",
    "Septembar",
    "Oktobar",
    "Novembar",
    "Decembar",
];

const weekDays = ["Pon", "Uto", "Sre", "Čet", "Pet", "Sub", "Ned"];

const today = new Date();
const currentMonth = ref(today.getMonth());
const currentYear = ref(today.getFullYear());

const daysInMonth = computed(() => {
    return new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
});

const firstDayOfWeek = computed(() => {
    let day = new Date(currentYear.value, currentMonth.value, 1).getDay();
    return day === 0 ? 6 : day - 1; // Ponedjeljak = 0
});

const formattedDisplayDate = computed(() => {
    if (!props.modelValue) return "Izaberite datum";
    const parts = props.modelValue.split("-");
    if (parts.length !== 3) return props.modelValue;
    const [y, m, d] = parts;
    return `${d}.${m}.${y}.`;
});

const isSelected = (day) => {
    const formattedDay = String(day).padStart(2, "0");
    const formattedMonth = String(currentMonth.value + 1).padStart(2, "0");
    const target = `${currentYear.value}-${formattedMonth}-${formattedDay}`;
    return props.modelValue === target;
};

const selectDay = (day) => {
    const formattedDay = String(day).padStart(2, "0");
    const formattedMonth = String(currentMonth.value + 1).padStart(2, "0");
    emit(
        "update:modelValue",
        `${currentYear.value}-${formattedMonth}-${formattedDay}`,
    );
    isOpen.value = false;
};

const selectToday = () => {
    currentMonth.value = today.getMonth();
    currentYear.value = today.getFullYear();
    selectDay(today.getDate());
};

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};
</script>
