<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-6 shadow-xl relative overflow-hidden">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <h3 class="text-lg font-bold text-white">Sledeća Aktivnost</h3>
            </div>
            <router-link to="/trainings" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">
                Pogledaj sve →
            </router-link>
        </div>

        <div v-if="session" class="bg-gray-900/80 border border-gray-700/60 p-5 rounded-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded border bg-indigo-500/20 text-indigo-300 border-indigo-500/30">
                {{ session.type === 'match' ? 'Utakmica' : 'Trening' }}
              </span>
                <h4 class="text-xl font-bold text-white mt-2">{{ session.title }}</h4>
                <p class="text-xs text-gray-400 mt-1">📍 {{ session.location || 'Glavni Teren' }} | ⏰ {{ formatDate(session.scheduled_at) }}</p>
            </div>

            <router-link
                to="/trainings"
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg transition"
            >
                Evidencija Prisustva
            </router-link>
        </div>

        <div v-else class="text-center py-8 text-gray-500 italic bg-gray-900/40 rounded-xl border border-dashed border-gray-700">
            Nema zakazanih aktivacija za naredne dane.
        </div>
    </div>
</template>

<script setup>
defineProps({
    session: {
        type: Object,
        default: null
    },
    formatDate: {
        type: Function,
        required: true
    }
})
</script>
