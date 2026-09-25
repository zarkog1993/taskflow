<template>
    <div class="lg:col-span-3 bg-gray-900/90 border border-gray-800 rounded-3xl p-4 sm:p-6 shadow-2xl relative overflow-hidden">
        <div @dragover.prevent @drop="$emit('drop', $event)" class="relative w-full aspect-[1.5/1] bg-emerald-950/50 rounded-2xl border-2 border-emerald-800/60 overflow-hidden select-none">

            <svg class="absolute inset-0 w-full h-full stroke-emerald-500/30 fill-none pointer-events-none" stroke-width="2">
                <rect x="2" y="2" width="99%" height="96%" rx="10" />
                <line x1="50%" y1="0" x2="50%" y2="100%" />
                <circle cx="50%" cy="50%" r="12%" />
                <rect x="2" y="22%" width="16%" height="56%" />
                <rect x="82%" y="22%" width="16%" height="56%" />
                <rect x="2" y="36%" width="6%" height="28%" />
                <rect x="92%" y="36%" width="6%" height="28%" />
            </svg>

            <!-- Igrači na Terenu -->
            <div
                v-for="spot in fieldSpots"
                :key="spot.id"
                draggable="true"
                @dragstart="$emit('drag-start', $event, spot)"
                @click="$emit('spot-click', spot)"
                class="absolute -translate-x-1/2 -translate-y-1/2 cursor-pointer transition-transform hover:scale-110 z-10 group"
                :style="{ left: spot.x + '%', top: spot.y + '%' }"
            >
                <div class="flex flex-col items-center">
                    <div
                        class="w-11 h-11 border-2 text-white font-black text-xs rounded-full flex items-center justify-center shadow-2xl relative transition"
                        :class="spot.player ? 'bg-indigo-600 border-white' : 'bg-gray-800/90 border-dashed border-gray-500 text-gray-400'"
                    >
                        #{{ spot.player?.player_profile?.jersey_number || spot.defaultNumber || '?' }}
                        <span class="absolute -bottom-1 -right-1 bg-emerald-500 text-gray-950 text-[9px] font-extrabold px-1 rounded border border-white">
                  {{ spot.position }}
                </span>
                    </div>
                    <span class="text-[10px] font-bold text-white bg-gray-950/90 px-2 py-0.5 rounded-md border border-gray-800 mt-1 shadow-md whitespace-nowrap">
                {{ spot.player ? spot.player.name : spot.roleName }}
              </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    fieldSpots: {
        type: Array,
        default: () => []
    }
})

defineEmits(['drop', 'drag-start', 'spot-click'])
</script>
