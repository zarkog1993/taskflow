<template>
    <div class="grid gap-6 md:grid-cols-3">
        <button
            v-for="plan in plans"
            :key="plan.slug"
            @click="$emit('update:selectedPlan', plan.slug)"
            :class="selectedPlan === plan.slug ? 'border-emerald-400 ring-2 ring-emerald-400/30' : 'border-slate-700'"
            class="rounded-2xl border bg-slate-800 p-6 text-left transition"
        >
            <h2 class="text-xl font-bold">{{ plan.name }}</h2>
            <p class="mt-2 text-2xl font-black text-emerald-400">{{ plan.price }} € <span class="text-xs font-normal text-slate-400">/ month</span></p>
            <ul class="mt-5 space-y-2 text-sm text-slate-300">
                <li v-for="feature in plan.features" :key="feature">✓ {{ featureLabel(feature) }}</li>
            </ul>
        </button>
    </div>
</template>

<script setup>
const featureLabels = {
    club_profile: 'Club profile',
    players: 'Players',
    teams: 'Teams',
    matches: 'Matches',
    news: 'News',
    advanced_stats: 'Advanced statistics',
    tactics: 'Tactics'
}

const featureLabel = (feature) => featureLabels[feature] || feature.replaceAll('_', ' ')

defineProps({
    plans: {
        type: Array,
        default: () => []
    },
    selectedPlan: {
        type: String,
        default: ''
    }
})

defineEmits(['update:selectedPlan'])
</script>
