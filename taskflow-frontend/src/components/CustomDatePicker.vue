<template>
    <div ref="root" class="relative w-full">
        <!-- Prikaz i gumb za otvaranje kalendara -->
        <button
            type="button"
            @click="toggle"
            :aria-expanded="isOpen"
            aria-haspopup="dialog"
            :aria-label="`Datum: ${formattedDisplayDate}`"
            class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-4 py-2.5 text-sm font-medium text-white flex justify-between items-center focus:outline-none focus:border-indigo-500 transition shadow-inner"
        >
            <span class="font-mono">{{ formattedDisplayDate }}</span>
            <span class="text-slate-400 text-xs">📅</span>
        </button>

        <!-- Pop-up Kalendar na Srpskoj Latinici -->
        <Teleport to="body">
        <div
            v-if="isOpen"
            ref="popup"
            role="dialog"
            aria-label="Izaberite datum"
            :style="popupStyle"
            class="fixed z-100 overflow-y-auto p-4 bg-slate-900 border border-slate-700/80 rounded-lg shadow-2xl w-72 text-white font-sans"
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
                <div class="flex items-center gap-2 text-sm text-indigo-400">
                    <span class="font-bold">{{ monthNames[currentMonth] }}</span>
                    <input v-model.number="currentYear" type="number" min="1" max="9999" aria-label="Godina" class="w-16 rounded border border-slate-700 bg-slate-950 px-1 py-1 text-center text-white focus:outline-none focus:border-indigo-500" />
                </div>
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
        </Teleport>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted, onUnmounted, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);

const isOpen = ref(false);
const root = ref(null);
const popup = ref(null);
const popupStyle = ref({});

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

watch(() => props.modelValue, (value) => {
    const match = /^(\d{4})-(\d{2})-\d{2}$/.exec(value || "");
    if (match) {
        currentYear.value = Number(match[1]);
        currentMonth.value = Number(match[2]) - 1;
    }
}, { immediate: true });

const toggle = () => {
    if (!isOpen.value) {
        const rect = root.value.getBoundingClientRect();
        const above = rect.top > window.innerHeight - rect.bottom && rect.top > 260;
        const availableHeight = above ? rect.top - 16 : window.innerHeight - rect.bottom - 16;
        popupStyle.value = {
            left: `${Math.max(8, Math.min(rect.left, window.innerWidth - 296))}px`,
            ...(above ? { bottom: `${window.innerHeight - rect.top + 8}px` } : { top: `${rect.bottom + 8}px` }),
            maxHeight: `${Math.max(100, availableHeight)}px`,
        };
    }
    isOpen.value = !isOpen.value;
};

const onPointerDown = (event) => {
    if (isOpen.value && !root.value?.contains(event.target) && !popup.value?.contains(event.target)) isOpen.value = false;
};

const onKeyDown = (event) => {
    if (event.key === "Escape" && isOpen.value) {
        isOpen.value = false;
        root.value?.querySelector("button")?.focus();
    }
};

onMounted(() => {
    document.addEventListener("pointerdown", onPointerDown);
    document.addEventListener("keydown", onKeyDown);
    window.addEventListener("scroll", closeOnScroll, true);
    window.addEventListener("resize", closeOnScroll);
});

onUnmounted(() => {
    document.removeEventListener("pointerdown", onPointerDown);
    document.removeEventListener("keydown", onKeyDown);
    window.removeEventListener("scroll", closeOnScroll, true);
    window.removeEventListener("resize", closeOnScroll);
});

const closeOnScroll = (event) => {
    if (!popup.value?.contains(event.target)) isOpen.value = false;
};

const daysInMonth = computed(() => {
    return new Date(Number(currentYear.value) || today.getFullYear(), currentMonth.value + 1, 0).getDate();
});

const firstDayOfWeek = computed(() => {
    let day = new Date(Number(currentYear.value) || today.getFullYear(), currentMonth.value, 1).getDay();
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
    if (!Number.isInteger(currentYear.value) || currentYear.value < 1 || currentYear.value > 9999) return;
    const formattedDay = String(day).padStart(2, "0");
    const formattedMonth = String(currentMonth.value + 1).padStart(2, "0");
    emit(
        "update:modelValue",
        `${String(currentYear.value).padStart(4, "0")}-${formattedMonth}-${formattedDay}`,
    );
    isOpen.value = false;
};

const selectToday = () => {
    const now = new Date();
    currentMonth.value = now.getMonth();
    currentYear.value = now.getFullYear();
    selectDay(now.getDate());
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
