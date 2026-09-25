<template>
    <div class="min-h-screen bg-slate-900 px-4 py-12 text-white">
        <div class="mx-auto max-w-6xl">
            <div v-if="loading" class="text-center text-slate-300">Loading onboarding...</div>
            <div v-else-if="error" class="mx-auto max-w-md rounded-xl border border-red-500/30 bg-red-500/10 p-6 text-red-300">{{ error }}</div>
            <template v-else>
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold text-emerald-400">Complete {{ club.name }} onboarding</h1>
                    <p class="mt-2 text-slate-300">Choose a package. Access starts only after approval.</p>
                </div>
                <div v-if="message" class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-center text-emerald-300">{{ message }}</div>
                <PlanSelector :plans="plans" v-model:selected-plan="selectedPlan" />
                <button @click="submit" :disabled="submitting || !selectedPlan" class="mx-auto mt-8 block rounded-xl bg-emerald-400 px-8 py-3 font-bold text-slate-950 disabled:opacity-50">
                    {{ submitting ? 'Submitting...' : 'Submit package for approval' }}
                </button>
            </template>
        </div>
    </div>
</template>

<script setup>
import { useOnboardingPage } from './composables/useOnboardingPage'
import PlanSelector from './components/PlanSelector.vue'

const {
    club,
    plans,
    selectedPlan,
    loading,
    submitting,
    message,
    error,
    submit
} = useOnboardingPage()
</script>
