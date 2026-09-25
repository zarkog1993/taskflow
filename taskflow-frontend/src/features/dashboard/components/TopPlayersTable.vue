<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-6 shadow-xl">
        <h3 class="text-lg font-bold text-white mb-4">Top Igrači po Golovima i Učinku</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="bg-gray-900/60 border-b border-gray-700 text-gray-400 uppercase text-[10px]">
                    <th class="py-3 px-4">Igrač</th>
                    <th class="py-3 px-4 text-center">Pozicija</th>
                    <th class="py-3 px-4 text-center">Utakmice</th>
                    <th class="py-3 px-4 text-center">Golovi</th>
                    <th class="py-3 px-4 text-center">Asistencije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50 text-xs">
                <tr v-for="user in players" :key="user.id" class="hover:bg-gray-700/30 transition">
                    <td class="py-3 px-4 font-semibold text-white flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-600/20 text-indigo-400 flex items-center justify-center font-bold text-[10px]">
                            {{ user.name.charAt(0) }}
                        </div>
                        <router-link :to="`/players/${user.id}`" class="hover:text-indigo-400">
                            {{ user.name }}
                        </router-link>
                    </td>
                    <td class="py-3 px-4 text-center text-gray-400 font-mono">
                        {{ user.player_profile?.primary_position || 'CM' }}
                    </td>
                    <td class="py-3 px-4 text-center font-bold text-emerald-400">
                        {{ user.player_profile?.matches_played || 0 }}
                    </td>
                    <td class="py-3 px-4 text-center font-bold text-yellow-400">
                        {{ user.player_profile?.goals || 0 }}
                    </td>
                    <td class="py-3 px-4 text-center font-bold text-purple-400">
                        {{ user.player_profile?.assists || 0 }}
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
