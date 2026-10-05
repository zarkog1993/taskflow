<!-- Mesečni grid kalendara: zaglavlje dana u nedelji i mreža ćelija sa događajima. -->
<template>
    <div class="bg-gray-100/60 dark:bg-gray-800/60 border border-gray-200/80 dark:border-gray-700/80 rounded-2xl overflow-hidden shadow-xl">
        <div class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
            <template v-for="(day, index) in mobileDays" :key="index">
                <section v-if="day.isCurrentMonth && day.events.length" class="bg-white/80 dark:bg-gray-900/80 p-3">
                    <h3 class="mb-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                        {{ formatDay(day.date) }}
                        <span v-if="day.isToday" class="ml-1 text-indigo-600 dark:text-indigo-400">· Danas</span>
                    </h3>
                    <div class="space-y-2">
                        <EventItem
                            v-for="event in day.events"
                            :key="event.id"
                            :event="event"
                            @open="$emit('open-event', event)"
                        />
                    </div>
                </section>
            </template>
            <p v-if="!hasMobileEvents" class="p-6 text-center text-sm text-gray-600 dark:text-gray-400">
                Nema zakazanih događaja za ovaj mesec.
            </p>
        </div>

        <!-- Dani u Nedelji -->
        <div class="hidden sm:grid grid-cols-7 bg-white/90 dark:bg-gray-900/90 text-center border-b border-gray-200/80 dark:border-gray-700/80 py-2.5 text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">
            <div>Pon</div><div>Uto</div><div>Sre</div><div>Čet</div><div>Pet</div><div>Sub</div><div>Ned</div>
        </div>

        <!-- Mreža Dana -->
        <div class="hidden sm:grid grid-cols-7 auto-rows-fr gap-px bg-gray-200/40 dark:bg-gray-700/40">
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
import { computed } from 'vue'
import DayCell from './DayCell.vue'
import EventItem from './EventItem.vue'

const props = defineProps({
    days: {
        type: Array,
        required: true
    }
})

const mobileDays = computed(() => props.days)
const hasMobileEvents = computed(() =>
    mobileDays.value.some(day => day.isCurrentMonth && day.events.length > 0),
)
const formatDay = (date) => new Intl.DateTimeFormat('sr-RS', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
}).format(date)

defineEmits(['open-event'])
</script>
