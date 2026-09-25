<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="bg-gray-900/80 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700/80">
                    <th class="p-4">Slika</th>
                    <th class="p-4">Ime i Prezime</th>
                    <th class="p-4">Pozicija</th>
                    <th class="p-4">Senioritet</th>
                    <th class="p-4">Beleška Trenera</th>
                    <th class="p-4 text-right">Akcije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/60 text-xs">
                <tr v-for="player in players" :key="player.id" class="hover:bg-gray-700/30 transition">
                    <td class="p-4">
                        <router-link :to="`/players/${player.id}`" class="block w-10 h-10 rounded-full bg-indigo-950 border border-indigo-800 overflow-hidden flex items-center justify-center font-bold text-indigo-300 text-xs shadow-md hover:border-indigo-500 transition">
                            <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover" />
                            <span v-else>#{{ player.jersey_number || '-' }}</span>
                        </router-link>
                    </td>

                    <!-- Poveznica za profil igrača -->
                    <td class="p-4 font-bold text-white">
                        <router-link :to="`/players/${player.id}`" class="hover:text-indigo-400 transition underline-offset-2 hover:underline">
                            {{ player.name }}
                        </router-link>
                    </td>

                    <td class="p-4">
            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-950 text-indigo-400 border border-indigo-800">
              {{ player.primary_position }}
            </span>
                    </td>
                    <td class="p-4 text-gray-300">{{ player.seniority || 'Seniori' }}</td>
                    <td class="p-4 text-gray-400 italic max-w-xs truncate">
                        {{ player.coach_notes || '-' }}
                    </td>
                    <td class="p-4 text-right">
                        <button
                            @click="$emit('delete', player)"
                            title="Obriši igrača"
                            class="p-2 bg-rose-950/60 hover:bg-rose-900 border border-rose-800/80 text-rose-300 hover:text-white rounded-xl transition cursor-pointer"
                        >
                            🗑️
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-if="!players.length" class="text-center py-12 text-xs text-gray-400 italic">
            Nema pronađenih igrača.
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

defineEmits(['delete'])
</script>
