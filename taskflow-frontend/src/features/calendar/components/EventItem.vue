<!-- Prikaz jednog događaja (trening/utakmica) unutar dana kalendara. -->
<template>
    <div
        @click="$emit('open')"
        :class="[
            'text-[10px] p-1.5 rounded-lg border truncate cursor-pointer transition font-medium',
            event.type === 'training'
                ? 'bg-indigo-950/80 text-indigo-200 border-indigo-700/60 hover:bg-indigo-900/90'
                : 'bg-emerald-950/80 text-emerald-200 border-emerald-700/60 hover:bg-emerald-900/90',
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
