<!-- Vrh stranice kalendara: naslov, dugme za zakazivanje, navigacija kroz mesec i legenda. -->
<template>
    <div class="space-y-6">
        <!-- Zaglavlje -->
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black text-white">Kalendar Aktivnosti</h1>
                <p class="text-xs text-gray-400">Pregled svih zakazanih treninga i utakmica</p>
            </div>
            <button
                @click="$emit('create')"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/30 cursor-pointer"
            >
                + Zakaži Događaj
            </button>
        </div>

        <!-- Navigacija Kroz Mesec -->
        <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 flex justify-between items-center">
            <button
                @click="$emit('prev')"
                class="bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs px-3 py-1.5 rounded-xl border border-gray-700 transition cursor-pointer"
            >
                ← Prethodni
            </button>
            <h2 class="text-base font-bold text-white uppercase tracking-wider capitalize">
                {{ currentMonthName }} {{ currentYear }}
            </h2>
            <button
                @click="$emit('next')"
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
    </div>
</template>

<script setup>
defineProps({
    currentMonthName: {
        type: String,
        required: true
    },
    currentYear: {
        type: Number,
        required: true
    }
})

defineEmits(['create', 'prev', 'next'])
</script>
