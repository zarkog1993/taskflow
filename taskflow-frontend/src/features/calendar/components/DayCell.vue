<!-- Ćelija jednog dana u mesečnom gridu kalendara, sa listom događaja tog dana. -->
<template>
    <div
        :class="[
            'min-h-[110px] p-2 bg-gray-900/90 transition',
            day.isCurrentMonth ? 'text-white' : 'text-gray-600 bg-gray-950/40',
            day.isToday ? 'ring-2 ring-indigo-500/50 bg-indigo-950/20' : '',
        ]"
    >
        <div class="flex justify-between items-center mb-1">
            <span
                class="text-xs font-bold font-mono"
                :class="day.isToday ? 'bg-indigo-600 text-white w-5 h-5 rounded-full flex items-center justify-center' : ''"
            >
                {{ day.date.getDate() }}
            </span>
        </div>

        <!-- Događaji za Taj Dan (Klik otvara detalje) -->
        <div class="space-y-1">
            <EventItem
                v-for="event in day.events"
                :key="event.id"
                :event="event"
                @open="$emit('open-event', event)"
            />
        </div>
    </div>
</template>

<script setup>
import EventItem from './EventItem.vue'

defineProps({
    day: {
        type: Object,
        required: true
    }
})

defineEmits(['open-event'])
</script>
