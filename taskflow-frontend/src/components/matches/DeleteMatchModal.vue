<template>
    <div
        class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50 animate-in fade-in duration-200"
    >
        <div
            class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5 text-center"
        >
            <!-- Ikonica Upozorenja -->
            <div
                class="w-14 h-14 bg-rose-950/80 border border-rose-800/80 text-rose-400 rounded-2xl flex items-center justify-center mx-auto text-2xl shadow-inner"
            >
                🗑️
            </div>

            <!-- Naslov i Poruka -->
            <div class="space-y-2">
                <h3 class="text-xl font-black text-white">Brisanje Utakmice</h3>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Da li ste sigurni da želite da obrišete utakmicu
                    <strong class="text-white font-bold"
                    >"{{ matchTitle || 'Zakazana utakmica' }}"</strong
                    >? Ova akcija je trajna i obrisaće sve podatke o prisustvu.
                </p>
            </div>

            <!-- Dugmad za Akciju -->
            <div class="flex items-center justify-center gap-3 pt-2">
                <button
                    type="button"
                    @click="$emit('close')"
                    class="w-full py-2.5 bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl border border-gray-700 transition cursor-pointer"
                >
                    Odustani
                </button>

                <button
                    type="button"
                    @click="$emit('confirm')"
                    :disabled="isDeleting"
                    class="w-full py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                >
                    <span v-if="isDeleting" class="animate-spin">⏳</span>
                    <span>{{ isDeleting ? "Brisanje..." : "Da, Obriši" }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    matchTitle: {
        type: String,
        default: "",
    },
    isDeleting: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["close", "confirm"]);
</script>