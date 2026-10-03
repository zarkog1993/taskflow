<template>
    <div class="space-y-4">
        <div v-for="club in clubs" :key="club.id" class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 rounded-2xl p-5 space-y-3">
            <div class="flex justify-between items-center border-b border-gray-200/60 dark:border-gray-700/60 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ club.name }}</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Vlasnik: <span class="text-gray-800 dark:text-gray-200">{{ club.owner?.name || 'Nema vlasnika' }}</span> ({{ club.owner?.email }})</p>
                </div>
                <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950 border border-indigo-200 dark:border-indigo-800 text-indigo-600 dark:text-indigo-400 text-xs font-mono font-bold rounded-xl">
                    Paket: {{ club.subscription?.plan_type || 'Basic' }}
                </span>
                <button
                    @click="$emit('delete', club)"
                    class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950 px-3 py-1 text-xs font-bold text-red-700 dark:text-red-300"
                >
                    Delete club
                </button>
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400 flex gap-4">
                <span>Broj Timova: <strong class="text-gray-900 dark:text-white">{{ club.teams?.length || 0 }}</strong></span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    clubs: {
        type: Array,
        default: () => []
    }
})

defineEmits(['delete'])
</script>
