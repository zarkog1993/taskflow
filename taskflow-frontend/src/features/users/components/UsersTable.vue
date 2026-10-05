<template>
    <div class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 rounded-3xl p-6 shadow-xl space-y-4">
        <div v-if="loading" class="text-center py-8 text-xs text-gray-600 dark:text-gray-400 italic">
            Učitavanje korisnika...
        </div>

        <div v-else-if="users.length">
        <div class="space-y-3 sm:hidden">
            <article
                v-for="user in users"
                :key="user.id"
                class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/70 p-3"
            >
                <div class="flex min-w-0 items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ user.name }}</h3>
                        <p class="mt-1 break-all text-xs text-gray-600 dark:text-gray-400">{{ user.email }}</p>
                    </div>
                    <button
                        type="button"
                        @click="$emit('delete', user.id)"
                        :aria-label="`Ukloni korisnika ${user.name}`"
                        class="min-h-10 shrink-0 rounded-lg bg-rose-50/40 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-800/60 px-3 text-xs font-bold text-rose-600 dark:text-rose-400"
                    >
                        Ukloni
                    </button>
                </div>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <span class="max-w-full truncate rounded bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 px-2 py-1">
                        {{ user.club?.name || 'Bez kluba' }}
                    </span>
                    <span class="rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-2 py-1">
                        {{ user.roles?.[0]?.name || 'Player' }}
                    </span>
                </div>
            </article>
        </div>

        <div class="hidden overflow-x-auto sm:block">
            <table class="min-w-[680px] w-full text-left border-collapse">
                <thead>
                <tr class="border-b border-gray-200/60 dark:border-gray-700/60 text-[10px] font-bold uppercase text-gray-600 dark:text-gray-400">
                    <th class="pb-3 px-2">Ime i Prezime</th>
                    <th class="pb-3 px-2">Email</th>
                    <th class="pb-3 px-2">Dodeljeni Klub</th>
                    <th class="pb-3 px-2">Uloga</th>
                    <th class="pb-3 px-2 text-right">Akcije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/40 dark:divide-gray-700/40 text-xs">
                <tr v-for="user in users" :key="user.id" class="hover:bg-white/50 dark:hover:bg-gray-900/50 transition">
                    <td class="py-3 px-2 font-bold text-gray-900 dark:text-white">{{ user.name }}</td>
                    <td class="py-3 px-2 text-gray-700 dark:text-gray-300 font-mono">{{ user.email }}</td>
                    <td class="py-3 px-2">
                            <span v-if="user.club" class="px-2.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 font-semibold">
                                {{ user.club.name }}
                            </span>
                        <span v-else class="text-gray-500 italic">Bez kluba</span>
                    </td>
                    <td class="py-3 px-2">
                            <span class="px-2.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 font-mono font-bold uppercase text-[10px]">
                                {{ user.roles?.[0]?.name || 'Player' }}
                            </span>
                    </td>
                    <td class="py-3 px-2 text-right">
                        <button @click="$emit('delete', user.id)" class="text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 font-bold px-2 py-1 rounded bg-rose-50/40 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-800/60 cursor-pointer">
                            Ukloni
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
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
