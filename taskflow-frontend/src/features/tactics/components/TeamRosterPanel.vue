<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-3xl p-5 shadow-xl space-y-4 flex flex-col">
        <h3 class="text-xs font-bold uppercase text-gray-400 tracking-wider">SASTAV EKIPE ({{ players.length }})</h3>

        <div class="space-y-2 flex-1 overflow-y-auto max-h-[500px] pr-1">
            <div
                v-for="player in players"
                :key="player.id"
                class="p-3 bg-gray-900/80 border border-gray-700/60 rounded-xl flex items-center justify-between"
            >
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-950 border border-indigo-800 text-indigo-300 font-bold text-xs flex items-center justify-center">
                        #{{ player.player_profile?.jersey_number || '-' }}
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">{{ player.name }}</div>
                        <div class="text-[10px] text-gray-400 font-mono">{{ player.player_profile?.primary_position || 'N/A' }}</div>
                    </div>
                </div>
                <span class="text-[10px] font-bold px-2 py-1 rounded bg-gray-800 text-gray-400">Dostupan</span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    players: {
        type: Array,
        default: () => []
    }
})
</script>
