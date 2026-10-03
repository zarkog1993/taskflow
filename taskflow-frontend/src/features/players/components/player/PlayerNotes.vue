<template>
    <section v-if="notes" class="rounded-2xl border border-slate-800 bg-slate-900/90 p-5">
        <h2 class="mb-2 text-sm font-bold text-white">Beleška trenera</h2>
        <p class="whitespace-pre-line text-xs leading-relaxed text-slate-300">{{ notes }}</p>
    </section>
</template>

<script setup>
defineProps({
    notes: { type: String, default: '' }
})
</script>
