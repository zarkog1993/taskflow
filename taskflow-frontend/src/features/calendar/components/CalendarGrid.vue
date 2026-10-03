<!-- Mesečni grid kalendara: zaglavlje dana u nedelji i mreža ćelija sa događajima. -->
<template>
    <div class="bg-gray-100/60 dark:bg-gray-800/60 border border-gray-200/80 dark:border-gray-700/80 rounded-2xl overflow-hidden shadow-xl">
        <!-- Dani u Nedelji -->
        <div class="grid grid-cols-7 bg-white/90 dark:bg-gray-900/90 text-center border-b border-gray-200/80 dark:border-gray-700/80 py-2.5 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">
            <div>Pon</div><div>Uto</div><div>Sre</div><div>Čet</div><div>Pet</div><div>Sub</div><div>Ned</div>
        </div>

        <!-- Mreža Dana -->
        <div class="grid grid-cols-7 auto-rows-fr gap-px bg-gray-200/40 dark:bg-gray-700/40">
            <DayCell
                v-for="(day, index) in days"
                :key="index"
                :day="day"
                @open-event="(event) => $emit('open-event', event)"
            />
        </div>
    </div>
</template>

<script setup>
import DayCell from './DayCell.vue'

defineProps({
    days: {
        type: Array,
        required: true
    }
})

defineEmits(['open-event'])
</script>
