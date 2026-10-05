<template>
    <div class="grid grid-cols-1 gap-2 border-b border-gray-200/80 dark:border-gray-700/80 pb-2 sm:grid-cols-3">
        <button
            @click="$emit('update:activeTab', 'clubs')"
            :class="[
                'min-h-[44px] px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer',
                activeTab === 'clubs' ? 'bg-emerald-500 text-gray-950 shadow-lg' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
            ]"
        >
            🏢 Klubovi ({{ clubsCount }})
        </button>
        <button
            @click="$emit('update:activeTab', 'users')"
            :class="[
                'min-h-[44px] px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer',
                activeTab === 'users' ? 'bg-emerald-500 text-gray-950 shadow-lg' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
            ]"
        >
            👥 Korisnici ({{ usersCount }})
        </button>
        <button
            @click="$emit('update:activeTab', 'subscriptions')"
            :class="[
                'min-h-[44px] px-3 sm:px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer',
                activeTab === 'subscriptions' ? 'bg-emerald-500 text-gray-950 shadow-lg' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
            ]"
        >
            💳 Pretplate ({{ subscriptionsCount }})
        </button>
    </div>
</template>

<script setup>
defineProps({
    activeTab: {
        type: String,
        required: true
    },
    clubsCount: {
        type: Number,
        default: 0
    },
    usersCount: {
        type: Number,
        default: 0
    },
    subscriptionsCount: {
        type: Number,
        default: 0
    }
})

defineEmits(['update:activeTab'])
</script>
