<!-- Tabovi za prebacivanje između predstojećih i odigranih utakmica. -->
<template>
    <div class="flex space-x-2 border-b border-gray-700/60">
        <button
            @click="activeTab = 'upcoming'"
            :class="activeTab === 'upcoming' ? 'border-indigo-500 text-indigo-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200'"
            class="py-2.5 px-4 border-b-2 text-xs transition cursor-pointer"
        >
            ⚡ Predstojeće Utakmice ({{ upcomingCount }})
        </button>
        <button
            @click="activeTab = 'completed'"
            :class="activeTab === 'completed' ? 'border-indigo-500 text-indigo-400 font-bold' : 'border-transparent text-gray-400 hover:text-gray-200'"
            class="py-2.5 px-4 border-b-2 text-xs transition cursor-pointer"
        >
            ✅ Odigrane Utakmice ({{ completedCount }})
        </button>
    </div>
</template>

<script setup>
defineProps({
    upcomingCount: {
        type: Number,
        default: 0
    },
    completedCount: {
        type: Number,
        default: 0
    }
})

const activeTab = defineModel('activeTab', { default: 'upcoming' })
</script>
