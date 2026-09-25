<template>
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div class="bg-gray-900 border border-gray-800 rounded-3xl w-full max-w-lg p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b border-gray-800 pb-3">
                <div>
            <span class="text-[10px] font-mono font-bold uppercase text-indigo-400 bg-indigo-950 px-2 py-0.5 rounded border border-indigo-800">
              {{ spot.position }} - {{ spot.roleName }}
            </span>
                    <h3 class="text-xl font-black text-white mt-1">Izaberi Igrača za Poziciju</h3>
                </div>
                <button @click="$emit('close')" class="text-gray-400 hover:text-white font-bold text-lg">✕</button>
            </div>

            <div class="space-y-2">
                <input v-model="searchQuery" type="text" placeholder="Pretraži po imenu..." class="w-full bg-gray-950 border border-gray-800 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500" />
            </div>

            <div class="max-h-64 overflow-y-auto space-y-2 pr-1">
                <div
                    v-for="user in filteredUsers"
                    :key="user.id"
                    @click="$emit('assign', user)"
                    class="p-3 bg-gray-800/60 hover:bg-indigo-950/60 border border-gray-700/50 rounded-xl flex items-center justify-between cursor-pointer transition"
                >
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-950 border border-indigo-800 flex items-center justify-center font-bold text-xs text-indigo-300">
                            #{{ user.player_profile?.jersey_number || '-' }}
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">{{ user.name }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">{{ user.player_profile?.primary_position || 'N/A' }}</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-400 bg-indigo-950 px-2 py-1 rounded border border-indigo-800">Postavi</span>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-800 flex justify-between items-center">
                <button v-if="spot.player" @click="$emit('remove')" class="text-xs font-bold text-rose-400">🗑️ Ukloni sa pozicije</button>
                <div v-else></div>
                <button @click="$emit('close')" class="px-4 py-2 bg-gray-800 text-gray-300 text-xs font-bold rounded-xl">Zatvori</button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    spot: {
        type: Object,
        required: true
    },
    filteredUsers: {
        type: Array,
        default: () => []
    }
})

const searchQuery = defineModel('searchQuery')

defineEmits(['close', 'assign', 'remove'])
</script>
