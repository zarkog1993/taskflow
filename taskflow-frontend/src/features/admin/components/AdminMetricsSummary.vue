<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 p-4 rounded-2xl flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400 tracking-wider">UKUPNO KLUBOVA</span>
                <div class="text-2xl font-black text-gray-900 dark:text-white mt-1 font-mono">{{ stats.total_clubs || 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg">🏢</div>
        </div>

        <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 p-4 rounded-2xl flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400 tracking-wider">UKUPNO KORISNIKA</span>
                <div class="text-2xl font-black text-gray-900 dark:text-white mt-1 font-mono">{{ stats.total_users || 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg">👥</div>
        </div>

        <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 p-4 rounded-2xl flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400 tracking-wider">AKTIVNE PRETPLE</span>
                <div class="text-2xl font-black text-emerald-700 dark:text-emerald-400 mt-1 font-mono">{{ stats.active_subscriptions || 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-lg">💳</div>
        </div>

        <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 p-4 rounded-2xl flex items-center justify-between shadow-lg">
            <div>
                <span class="text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400 tracking-wider">MESEČNI PRIHOD</span>
                <div class="text-2xl font-black text-amber-700 dark:text-amber-400 mt-1 font-mono">{{ stats.monthly_revenue || 0 }} €</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-400 flex items-center justify-center text-lg">💰</div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    stats: {
        type: Object,
        default: () => ({})
    }
})
</script>
