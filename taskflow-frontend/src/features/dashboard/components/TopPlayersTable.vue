<template>
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <h3 class="mb-4 text-base font-bold text-slate-900 dark:text-white">Top igrači po golovima i učinku</h3>

        <div class="overflow-x-auto">
            <table class="min-w-[560px] w-full text-left border-collapse">
                <thead>
                <tr class="border-b border-slate-200/80 bg-slate-50 text-slate-600 dark:border-slate-800 dark:bg-slate-950/50 dark:text-slate-400 uppercase text-[10px] font-semibold">
                    <th class="py-3 px-4">Igrač</th>
                    <th class="py-3 px-4 text-center">Pozicija</th>
                    <th class="py-3 px-4 text-center">Utakmice</th>
                    <th class="py-3 px-4 text-center">Golovi</th>
                    <th class="py-3 px-4 text-center">Asistencije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs dark:divide-slate-800">
                <tr v-for="player in players" :key="player.id" class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                    <td class="flex items-center gap-2 py-3 px-4 font-semibold text-slate-900 dark:text-white">
                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-800 font-bold text-[10px] dark:bg-indigo-950 dark:text-indigo-300">
                            {{ player.name.charAt(0) }}
                        </div>
                        <router-link :to="`/players/${player.id}`" class="font-semibold text-slate-900 hover:text-indigo-700 dark:text-white dark:hover:text-indigo-300">
                            {{ player.name }}
                        </router-link>
                    </td>
                    <td class="py-3 px-4 text-center font-mono text-slate-600 dark:text-slate-400">
                        {{ player.primary_position || 'N/A' }}
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="inline-flex min-w-8 justify-center rounded-md bg-emerald-50 px-2 py-1 text-sm font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">{{ player.matches_played || 0 }}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="inline-flex min-w-8 justify-center rounded-md bg-amber-50 px-2 py-1 text-sm font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">{{ player.goals || 0 }}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="inline-flex min-w-8 justify-center rounded-md bg-violet-50 px-2 py-1 text-sm font-bold text-violet-800 dark:bg-violet-950/60 dark:text-violet-300">{{ player.assists || 0 }}</span>
                    </td>
                </tr>

                <tr v-if="players.length === 0">
                    <td colspan="5" class="py-6 text-center italic text-slate-500 dark:text-slate-400">Nema zabeleženih statistika igrača.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
defineProps({
    players: {
        type: Array,
        default: () => []
    }
})
</script>
