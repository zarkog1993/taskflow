<template>
    <div class="bg-gray-900/90 border border-gray-800 rounded-2xl p-5 shadow-2xl space-y-3">
        <div class="flex justify-between items-center border-b border-gray-800 pb-2">
            <h4 class="text-xs font-bold uppercase text-gray-400 tracking-wider">Pozicija na Terenu</h4>
            <span class="text-xs font-mono font-black text-emerald-400 bg-emerald-950/80 px-2.5 py-1 rounded-lg border border-emerald-800/80">
        {{ primaryPosition }}
      </span>
        </div>

        <!-- Fudbalski Teren -->
        <div class="relative w-full aspect-[1.7/1] bg-emerald-950/40 rounded-xl border border-emerald-800/50 overflow-hidden p-3">

            <!-- Linije Terena -->
            <svg class="absolute inset-0 w-full h-full stroke-emerald-600/30 fill-none pointer-events-none" stroke-width="1.5">
                <rect x="2" y="2" width="99%" height="96%" rx="6" />
                <line x1="50%" y1="0" x2="50%" y2="100%" />
                <circle cx="50%" cy="50%" r="14%" />
                <rect x="2" y="20%" width="15%" height="60%" />
                <rect x="83%" y="20%" width="15%" height="60%" />
            </svg>

            <!-- Prikaz Jedne Pozicije -->
            <div
                class="absolute -translate-x-1/2 -translate-y-1/2 transition-all duration-500 z-10"
                :style="positionCoordinates"
            >
                <div class="relative flex flex-col items-center">
                    <span class="absolute -inset-1 bg-emerald-500/50 rounded-xl blur-sm animate-pulse"></span>
                    <div class="relative px-3 py-1.5 bg-emerald-500 text-gray-950 font-black text-xs rounded-xl shadow-2xl border-2 border-white tracking-wider">
                        {{ primaryPosition }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    position: {
        type: String,
        default: ''
    }
})

// Ako prop stigne prazan, prikazuje N/A, inače čisti pretiče i stavlja velika slova
const primaryPosition = computed(() => {
    if (!props.position) return 'N/A'
    return props.position.trim().toUpperCase()
})

const positionMap = {
    GK:  { left: '8%',  top: '50%' },
    LB:  { left: '25%', top: '18%' },
    CB:  { left: '25%', top: '50%' },
    RB:  { left: '25%', top: '82%' },
    LWB: { left: '38%', top: '15%' },
    RWB: { left: '38%', top: '85%' },
    DM:  { left: '42%', top: '50%' },
    CDM: { left: '42%', top: '50%' },
    CM:  { left: '55%', top: '50%' },
    LM:  { left: '58%', top: '18%' },
    RM:  { left: '58%', top: '82%' },
    AM:  { left: '70%', top: '50%' },
    CAM: { left: '70%', top: '50%' },
    LW:  { left: '80%', top: '18%' },
    RW:  { left: '80%', top: '82%' },
    SS:  { left: '80%', top: '50%' },
    ST:  { left: '88%', top: '50%' },
    CF:  { left: '88%', top: '50%' }
}

const positionCoordinates = computed(() => {
    const coords = positionMap[primaryPosition.value] || { left: '50%', top: '50%' }
    return {
        left: coords.left,
        top: coords.top
    }
})
</script>