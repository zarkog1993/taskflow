<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 p-5 rounded-2xl shadow-xl flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Ukupno Ekipe</div>
                <div class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ teamsCount }}</div>
                <div class="text-[11px] text-indigo-600 dark:text-indigo-400 mt-1">Aktivne selekcije</div>
            </div>
            <div class="p-3 bg-indigo-600/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-2xl text-xl">
                🛡️
            </div>
        </div>

        <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 p-5 rounded-2xl shadow-xl flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Registrovani Igrači</div>
                <div class="text-3xl font-black text-emerald-700 dark:text-emerald-400 mt-1">{{ usersCount }}</div>
                <div class="text-[11px] text-emerald-700 dark:text-emerald-500 mt-1">Članovi akademije</div>
            </div>
            <div class="p-3 bg-emerald-600/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-2xl text-xl">
                🏃‍♂️
            </div>
        </div>

        <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 p-5 rounded-2xl shadow-xl flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Predstojeći Treninzi</div>
                <div class="text-3xl font-black text-yellow-700 dark:text-yellow-400 mt-1">{{ upcomingSessionsCount }}</div>
                <div class="text-[11px] text-yellow-700 dark:text-yellow-500 mt-1">Ove nedelje</div>
            </div>
            <div class="p-3 bg-yellow-600/10 border border-yellow-500/20 text-yellow-700 dark:text-yellow-400 rounded-2xl text-xl">
                📅
            </div>
        </div>

        <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 p-5 rounded-2xl shadow-xl flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Prosečni Golovi</div>
                <div class="text-3xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ totalGoalsCount }}</div>
                <div class="text-[11px] text-purple-600 dark:text-purple-400 mt-1">Ukupno u sezoni</div>
            </div>
            <div class="p-3 bg-purple-600/10 border border-purple-500/20 text-purple-600 dark:text-purple-400 rounded-2xl text-xl">
                ⚽
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    teamsCount: {
        type: Number,
        default: 0
    },
    usersCount: {
        type: Number,
        default: 0
    },
    upcomingSessionsCount: {
        type: Number,
        default: 0
    },
    totalGoalsCount: {
        type: Number,
        default: 0
    }
})
</script>
