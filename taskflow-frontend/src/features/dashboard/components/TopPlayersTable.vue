<template>
    <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 rounded-2xl p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Top Igrači po Golovima i Učinku</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="bg-white/60 dark:bg-gray-900/60 border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 uppercase text-[10px]">
                    <th class="py-3 px-4">Igrač</th>
                    <th class="py-3 px-4 text-center">Pozicija</th>
                    <th class="py-3 px-4 text-center">Utakmice</th>
                    <th class="py-3 px-4 text-center">Golovi</th>
                    <th class="py-3 px-4 text-center">Asistencije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50 dark:divide-gray-700/50 text-xs">
                <tr v-for="player in players" :key="player.id" class="hover:bg-gray-200/30 dark:hover:bg-gray-700/30 transition">
                    <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-600/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-[10px]">
                            {{ player.name.charAt(0) }}
                        </div>
                        <router-link :to="`/players/${player.id}`" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                            {{ player.name }}
                        </router-link>
                    </td>
                    <td class="py-3 px-4 text-center text-gray-600 dark:text-gray-400 font-mono">
                        {{ player.primary_position || 'N/A' }}
                    </td>
                    <td class="py-3 px-4 text-center font-bold text-emerald-700 dark:text-emerald-400">
                        {{ player.matches_played || 0 }}
                    </td>
                    <td class="py-3 px-4 text-center font-bold text-yellow-700 dark:text-yellow-400">
                        {{ player.goals || 0 }}
                    </td>
                    <td class="py-3 px-4 text-center font-bold text-purple-600 dark:text-purple-400">
                        {{ player.assists || 0 }}
                    </td>
                </tr>

                <tr v-if="players.length === 0">
                    <td colspan="5" class="text-center py-6 text-gray-500 italic">Nema zabeleženih statistika igrača.</td>
                </tr>
                </tbody>
            </table>
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
