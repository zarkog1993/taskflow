<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-900 px-4 text-center text-white">
        <div class="max-w-lg rounded-2xl border border-slate-700 bg-slate-800 p-8">
            <h1 class="text-2xl font-bold text-amber-400">Subscription approval pending</h1>
            <p class="mt-3 text-sm text-slate-300">
                Your package selection was received. Club management will be available after an administrator approves your subscription.
            </p>
        </div>
    </div>
</template>

<script setup>
import { useSubscriptionStatusRefresh } from './composables/useSubscriptionStatusRefresh'

useSubscriptionStatusRefresh()
</script>
