<!-- Prikaz jednog događaja (trening/utakmica) unutar dana kalendara. -->
<template>
    <div
        @click="$emit('open')"
        :class="[
            'text-[10px] p-1.5 rounded-lg border truncate cursor-pointer transition font-medium',
            event.type === 'training'
                ? 'bg-indigo-50/80 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-200 border-indigo-200/60 dark:border-indigo-700/60 hover:bg-indigo-100/90 dark:hover:bg-indigo-900/90'
                : 'bg-emerald-50/80 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-200 border-emerald-200/60 dark:border-emerald-700/60 hover:bg-emerald-100/90 dark:hover:bg-emerald-900/90',
        ]"
    >
        <div class="font-bold flex items-center gap-1">
            <span>{{ event.type === "training" ? "⚽" : "🏆" }}</span>
            <span>{{ event.time }}</span>
        </div>
        <div class="truncate">{{ event.title }}</div>
    </div>
</template>

<script setup>
defineProps({
    event: {
        type: Object,
        required: true
    }
})

defineEmits(['open'])
</script>
