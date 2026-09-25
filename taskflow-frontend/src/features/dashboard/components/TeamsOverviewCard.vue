<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-6 shadow-xl">
        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Aktivne Selekcije</h3>

        <div class="space-y-2 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
            <div
                v-for="team in teams"
                :key="team.id"
                class="flex justify-between items-center p-3 bg-gray-900/60 rounded-xl border border-gray-700/40"
            >
                <div>
                    <div class="text-xs font-bold text-white">{{ team.name }}</div>
                    <div class="text-[10px] text-gray-400">{{ team.members?.length || 0 }} igrača</div>
                </div>

                <span class="text-[10px] font-mono font-bold text-indigo-400 bg-indigo-950 px-2 py-0.5 rounded border border-indigo-800">
                {{ team.age_group }}
              </span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    teams: {
        type: Array,
        default: () => []
    }
})
</script>
