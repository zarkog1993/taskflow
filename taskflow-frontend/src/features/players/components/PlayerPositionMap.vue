<template>
    <div class="lg:col-span-2 bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold uppercase text-gray-400 tracking-wider">Pozicija na Terenu</h3>
                <span class="text-xs font-black text-emerald-400 bg-emerald-950 px-2.5 py-1 rounded border border-emerald-800">
      {{ position }}
    </span>
            </div>

            <div class="relative w-full aspect-[1.5/1] bg-emerald-950/40 rounded-2xl border-2 border-emerald-800/60 overflow-hidden select-none">
                <svg class="absolute inset-0 w-full h-full stroke-emerald-500/30 fill-none pointer-events-none" stroke-width="2">
                    <rect x="2" y="2" width="99%" height="96%" rx="10" />
                    <line x1="50%" y1="0" x2="50%" y2="100%" />
                    <circle cx="50%" cy="50%" r="12%" />
                    <rect x="2" y="22%" width="16%" height="56%" />
                    <rect x="82%" y="22%" width="16%" height="56%" />
                </svg>

                <div
                    class="absolute -translate-x-1/2 -translate-y-1/2 flex flex-col items-center transition-all duration-300"
                    :style="getPositionStyle(position)"
                >
                    <div class="w-12 h-12 bg-emerald-500 text-gray-950 font-black rounded-full flex items-center justify-center border-2 border-white shadow-2xl animate-pulse">
                        {{ position }}
                    </div>
                    <span class="text-[10px] font-bold text-white bg-gray-950/90 px-2 py-0.5 rounded mt-1 border border-gray-800 shadow">
        Primarna Pozicija
      </span>
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
