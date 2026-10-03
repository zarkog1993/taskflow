<template>
    <div class="w-full">
        <div>
            <div class="relative w-full aspect-[1.85/1] overflow-hidden rounded-lg border border-emerald-900/70 bg-emerald-950/30 select-none">
                <svg class="absolute inset-0 w-full h-full stroke-emerald-500/30 fill-none pointer-events-none" stroke-width="2">
                    <rect x="2" y="2" width="99%" height="96%" rx="8" />
                    <line x1="50%" y1="0" x2="50%" y2="100%" />
                    <circle cx="50%" cy="50%" r="12%" />
                    <rect x="2" y="22%" width="16%" height="56%" />
                    <rect x="82%" y="22%" width="16%" height="56%" />
                </svg>

                <div
                    class="absolute -translate-x-1/2 -translate-y-1/2 flex flex-col items-center transition-all duration-300"
                    :style="getPositionStyle(position)"
                >
                    <div class="flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-emerald-500 text-[10px] font-black text-gray-950">
                        {{ position }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    position: {
        type: String,
        default: ''
    }
})

const getPositionStyle = (pos) => {
    const positions = {
        GK: { left: '8%', top: '50%' },
        LB: { left: '25%', top: '82%' },
        CB: { left: '25%', top: '50%' },
        RB: { left: '25%', top: '18%' },
        DM: { left: '40%', top: '50%' },
        CM: { left: '50%', top: '50%' },
        AM: { left: '65%', top: '50%' },
        LW: { left: '78%', top: '80%' },
        RW: { left: '78%', top: '20%' },
        ST: { left: '85%', top: '50%' },
    }
    return positions[pos] || { left: '50%', top: '50%' }
}
</script>
