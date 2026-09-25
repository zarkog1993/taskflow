<template>
    <div
        class="bg-gray-800/80 hover:bg-gray-800 border border-gray-700/80 hover:border-indigo-500/50 rounded-2xl p-5 shadow-xl transition-all duration-300 flex flex-col justify-between group relative overflow-hidden"
    >
        <!-- Gornji Akcenat sa Brojem Dresa -->
        <div class="flex justify-between items-start mb-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 bg-indigo-950/80 px-2.5 py-1 rounded-md border border-indigo-800/50">
              {{ player.primary_position || 'CM' }}
            </span>
            <span class="text-xs font-mono font-extrabold text-white bg-indigo-600 px-2.5 py-1 rounded-lg shadow-md">
              #{{ player.jersey_number || '-' }}
            </span>
        </div>

        <!-- Slika, Ime i Klik ka Detaljima -->
        <div
            @click="$router.push(`/players/${player.id}`)"
            class="cursor-pointer text-center space-y-3 my-2 group-hover:transform group-hover:-translate-y-1 transition duration-200"
        >
            <div class="relative w-24 h-24 mx-auto rounded-full bg-indigo-950 border-2 border-indigo-500/30 group-hover:border-indigo-500 overflow-hidden shadow-lg flex items-center justify-center font-bold text-indigo-300 text-xl">
                <img
                    v-if="player.photo_url"
                    :src="player.photo_url"
                    class="w-full h-full object-cover"
                    :alt="player.name"
                />
                <span v-else>#{{ player.jersey_number || '-' }}</span>
            </div>

            <div>
                <h3 class="text-lg font-bold text-white group-hover:text-indigo-300 transition-colors leading-snug">
                    {{ player.name }}
                </h3>
                <p class="text-[11px] text-gray-400 mt-0.5 font-mono">
                    {{ player.seniority || 'Seniori' }}
                </p>
            </div>
        </div>

        <!-- Mini Statistika Igrača na Kartici -->
        <div class="grid grid-cols-3 gap-2 my-4 bg-gray-900/60 p-2.5 rounded-xl border border-gray-700/50 text-center text-xs">
            <div>
                <div class="text-[9px] font-bold uppercase text-gray-400">Utakmice</div>
                <div class="font-bold text-emerald-400 mt-0.5">{{ getStat(player, 'matches_played') }}</div>
            </div>
            <div>
                <div class="text-[9px] font-bold uppercase text-gray-400">Golovi</div>
                <div class="font-bold text-yellow-400 mt-0.5">{{ getStat(player, 'goals') }}</div>
            </div>
            <div>
                <div class="text-[9px] font-bold uppercase text-gray-400">Asist.</div>
                <div class="font-bold text-purple-400 mt-0.5">{{ getStat(player, 'assists') }}</div>
            </div>
        </div>

        <!-- Akcija: Izmena Učinka -->
        <button
            @click="$emit('edit', player)"
            class="w-full py-2 px-3 bg-gray-700/60 hover:bg-gray-700 text-gray-200 text-xs font-semibold rounded-xl transition border border-gray-600/50 flex items-center justify-center gap-1.5 cursor-pointer"
        >
            ✏️ Izmeni Učinak
        </button>
    </div>
</template>

<script setup>
defineProps({
    player: {
        type: Object,
        required: true
    },
    getStat: {
        type: Function,
        required: true
    }
})

defineEmits(['edit'])
</script>
