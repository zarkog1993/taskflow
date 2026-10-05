<template>
    <div class="min-h-screen flex items-center justify-center bg-white dark:bg-slate-900 px-4 text-center text-gray-900 dark:text-white">
        <div class="max-w-lg rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 p-8">
            <h1 class="text-2xl font-bold text-amber-700 dark:text-amber-400">Čeka se odobrenje pretplate</h1>
            <p class="mt-3 text-sm text-slate-700 dark:text-slate-300">
                Vaš izbor paketa je primljen. Upravljanje klubom biće dostupno nakon što administrator odobri pretplatu.
            </p>
        </div>
    </div>
</template>

<script setup>
import { useSubscriptionStatusRefresh } from './composables/useSubscriptionStatusRefresh'

useSubscriptionStatusRefresh()
</script>
