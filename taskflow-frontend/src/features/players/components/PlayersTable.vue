<template>
    <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 rounded-2xl shadow-xl overflow-hidden">
        <div class="space-y-3 p-3 sm:hidden">
            <article
                v-for="player in players"
                :key="player.id"
                class="flex min-w-0 items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/70 p-3"
            >
                <router-link :to="`/players/${player.id}`" class="block w-11 h-11 shrink-0 rounded-full bg-indigo-50 dark:bg-indigo-950 border border-indigo-200 dark:border-indigo-800 overflow-hidden flex items-center justify-center font-bold text-indigo-700 dark:text-indigo-300 text-xs">
                    <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover" />
                    <span v-else>#{{ player.jersey_number || '-' }}</span>
                </router-link>
                <div class="min-w-0 flex-1">
                    <router-link :to="`/players/${player.id}`" class="block truncate text-sm font-bold text-gray-900 dark:text-white">
                        {{ player.name }}
                    </router-link>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                        {{ player.primary_position || 'Bez pozicije' }} · {{ getPlayerCategory(player) }}
                    </p>
                    <p v-if="player.team?.name" class="mt-0.5 truncate text-[11px] text-gray-500">{{ player.team.name }}</p>
                    <p v-if="player.coach_notes" class="mt-1 line-clamp-2 text-xs text-gray-600 dark:text-gray-400">{{ player.coach_notes }}</p>
                </div>
                <button
                    type="button"
                    @click="$emit('delete', player)"
                    :aria-label="`Obriši igrača ${player.name}`"
                    class="min-h-10 min-w-10 shrink-0 rounded-lg border border-rose-200 dark:border-rose-800 bg-rose-50/60 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300"
                >
                    🗑️
                </button>
            </article>
            <p v-if="!players.length" class="py-8 text-center text-xs text-gray-600 dark:text-gray-400 italic">
                Nema pronađenih igrača.
            </p>
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="min-w-[720px] w-full text-left border-collapse">
                <thead>
                <tr class="bg-white/80 dark:bg-gray-900/80 text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200/80 dark:border-gray-700/80">
                    <th class="p-4">Slika</th>
                    <th class="p-4">Ime i Prezime</th>
                    <th class="p-4">Pozicija</th>
                    <th class="p-4">Kategorija</th>
                    <th class="p-4">Beleška Trenera</th>
                    <th class="p-4 text-right">Akcije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/60 dark:divide-gray-700/60 text-xs">
                <tr v-for="player in players" :key="player.id" class="hover:bg-gray-200/30 dark:hover:bg-gray-700/30 transition">
                    <td class="p-4">
                        <router-link :to="`/players/${player.id}`" class="block w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-950 border border-indigo-200 dark:border-indigo-800 overflow-hidden flex items-center justify-center font-bold text-indigo-700 dark:text-indigo-300 text-xs shadow-md hover:border-indigo-500 transition">
                            <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover" />
                            <span v-else>#{{ player.jersey_number || '-' }}</span>
                        </router-link>
                    </td>

                    <!-- Poveznica za profil igrača -->
                    <td class="p-4 font-bold text-gray-900 dark:text-white">
                        <router-link :to="`/players/${player.id}`" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition underline-offset-2 hover:underline">
                            {{ player.name }}
                        </router-link>
                    </td>

                    <td class="p-4">
            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
              {{ player.primary_position }}
            </span>
                    </td>
                    <td class="p-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            {{ getPlayerCategory(player) }}
                        </span>
                        <div v-if="player.team?.name" class="text-[10px] text-gray-500 mt-1">{{ player.team.name }}</div>
                    </td>
                    <td class="p-4 text-gray-600 dark:text-gray-400 italic max-w-xs truncate">
                        {{ player.coach_notes || '-' }}
                    </td>
                    <td class="p-4 text-right">
                        <button
                            @click="$emit('delete', player)"
                            title="Obriši igrača"
                            class="p-2 bg-rose-50/60 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900 border border-rose-200/80 dark:border-rose-800/80 text-rose-700 dark:text-rose-300 hover:text-gray-900 dark:hover:text-white rounded-xl transition cursor-pointer"
                        >
                            🗑️
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-if="!players.length" class="text-center py-12 text-xs text-gray-600 dark:text-gray-400 italic">
            Nema pronađenih igrača.
        </div>
    </div>
</template>

<script setup>
import { getPlayerCategory } from '../utils/playerFormatters'

defineProps({
    players: {
        type: Array,
        default: () => []
    }
})

defineEmits(['delete'])
</script>
