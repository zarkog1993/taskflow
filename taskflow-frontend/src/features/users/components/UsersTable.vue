<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <div v-if="loading" class="text-center py-8 text-xs text-gray-400 italic">
            Učitavanje korisnika...
        </div>

        <div v-else-if="users.length" class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="border-b border-gray-700/60 text-[10px] font-bold uppercase text-gray-400">
                    <th class="pb-3 px-2">Ime i Prezime</th>
                    <th class="pb-3 px-2">Email</th>
                    <th class="pb-3 px-2">Dodeljeni Klub</th>
                    <th class="pb-3 px-2">Uloga</th>
                    <th class="pb-3 px-2 text-right">Akcije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/40 text-xs">
                <tr v-for="user in users" :key="user.id" class="hover:bg-gray-900/50 transition">
                    <td class="py-3 px-2 font-bold text-white">{{ user.name }}</td>
                    <td class="py-3 px-2 text-gray-300 font-mono">{{ user.email }}</td>
                    <td class="py-3 px-2">
                            <span v-if="user.club" class="px-2.5 py-0.5 rounded bg-indigo-950 text-indigo-400 border border-indigo-800 font-semibold">
                                {{ user.club.name }}
                            </span>
                        <span v-else class="text-gray-500 italic">Bez kluba</span>
                    </td>
                    <td class="py-3 px-2">
                            <span class="px-2.5 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 font-mono font-bold uppercase text-[10px]">
                                {{ user.roles?.[0]?.name || 'Player' }}
                            </span>
                    </td>
                    <td class="py-3 px-2 text-right">
                        <button @click="$emit('delete', user.id)" class="text-rose-400 hover:text-rose-300 font-bold px-2 py-1 rounded bg-rose-950/40 border border-rose-800/60 cursor-pointer">
                            Ukloni
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="text-center py-8 text-xs text-gray-500 italic">
            Nema registrovanih korisnika.
        </div>
    </div>
</template>

<script setup>
defineProps({
    users: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
})

defineEmits(['delete'])
</script>
