<!-- Lista igrača za selekciju (poziv na trening/utakmicu) sa opcijom "označi sve". -->
<template>
    <div v-if="players.length" class="space-y-1.5 pt-2">
        <div class="flex justify-between items-center">
            <label class="block text-xs font-bold uppercase text-gray-400">
                Pozvani Igrači ({{ selectedCount }})
            </label>
            <button
                type="button"
                @click="$emit('toggle-select-all')"
                class="text-[10px] text-indigo-400 hover:text-indigo-300 font-bold cursor-pointer"
            >
                {{ allSelected ? "Poništi sve" : "Označi sve" }}
            </button>
        </div>

        <div class="max-h-36 overflow-y-auto space-y-1.5 pr-1 bg-gray-900/80 p-2 rounded-xl border border-gray-700/60">
            <div
                v-for="player in players"
                :key="player.id"
                @click="player.selected = !player.selected"
                class="flex items-center justify-between p-2 rounded-lg cursor-pointer transition select-none"
                :class="player.selected ? 'bg-indigo-950/80 border border-indigo-700/60' : 'bg-gray-800/40 border border-transparent'"
            >
                <div class="flex items-center gap-2.5">
                    <input
                        type="checkbox"
                        v-model="player.selected"
                        class="w-4 h-4 text-indigo-600 rounded bg-gray-800 border-gray-600"
                        @click.stop
                    />
                    <span class="text-xs font-bold text-white">{{ player.name }}</span>
                </div>
                <span class="text-[10px] text-gray-400 font-mono">
                    #{{ player.player_profile?.jersey_number || "-" }} • {{ player.player_profile?.primary_position || "N/A" }}
                </span>
            </div>
        </div>
    </div>

    <div v-else-if="teamSelected" class="text-xs text-gray-500 italic py-2 text-center">
        Nema registrovanih igrača u ovoj ekipi.
    </div>
</template>

<script setup>
defineProps({
    players: {
        type: Array,
        required: true
    },
    selectedCount: {
        type: Number,
        required: true
    },
    allSelected: {
        type: Boolean,
        required: true
    },
    teamSelected: {
        type: [String, Number],
        default: ''
    }
})

defineEmits(['toggle-select-all'])
</script>
