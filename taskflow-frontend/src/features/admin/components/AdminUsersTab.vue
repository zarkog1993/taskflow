<template>
    <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 rounded-2xl p-4 overflow-x-auto">
        <table class="min-w-[760px] w-full text-left border-collapse text-xs">
            <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 uppercase font-bold text-[10px]">
                <th class="p-2">Ime</th>
                <th class="p-2">Email</th>
                <th class="p-2">Klub</th>
                <th class="p-2">Uloga</th>
                <th class="p-2">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200/50 dark:divide-gray-700/50">
            <tr v-for="u in users" :key="u.id" class="hover:bg-white/40 dark:hover:bg-gray-900/40">
                <td class="p-2 font-bold text-gray-900 dark:text-white">{{ u.name }}</td>
                <td class="p-2 text-gray-700 dark:text-gray-300 font-mono">{{ u.email }}</td>
                <td class="p-2 text-indigo-600 dark:text-indigo-400">{{ u.club?.name || 'Bez kluba' }}</td>
                <td class="p-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 font-bold uppercase text-[10px]">
                            {{ u.roles?.[0]?.name || 'Player' }}
                        </span>
                </td>
                <td class="p-2">
                    <button
                        @click="$emit('delete', u)"
                        class="rounded border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950 px-2 py-1 font-bold text-red-700 dark:text-red-300"
                    >
                        Delete
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    users: {
        type: Array,
        default: () => []
    }
})

defineEmits(['delete'])
</script>
