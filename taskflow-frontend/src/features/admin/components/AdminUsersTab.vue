<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-2xl p-4 overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
            <tr class="border-b border-gray-700 text-gray-400 uppercase font-bold text-[10px]">
                <th class="p-2">Ime</th>
                <th class="p-2">Email</th>
                <th class="p-2">Klub</th>
                <th class="p-2">Uloga</th>
                <th class="p-2">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
            <tr v-for="u in users" :key="u.id" class="hover:bg-gray-900/40">
                <td class="p-2 font-bold text-white">{{ u.name }}</td>
                <td class="p-2 text-gray-300 font-mono">{{ u.email }}</td>
                <td class="p-2 text-indigo-400">{{ u.club?.name || 'Bez kluba' }}</td>
                <td class="p-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 font-bold uppercase text-[10px]">
                            {{ u.roles?.[0]?.name || 'Player' }}
                        </span>
                </td>
                <td class="p-2">
                    <button
                        @click="$emit('delete', u)"
                        class="rounded border border-red-800 bg-red-950 px-2 py-1 font-bold text-red-300"
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
