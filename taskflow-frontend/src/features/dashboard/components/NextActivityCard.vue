<template>
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Sledeća aktivnost</h3>
            </div>
            <router-link to="/trainings" class="text-xs text-indigo-700 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-semibold">
                Pogledaj sve →
            </router-link>
        </div>

        <div v-if="session" class="grid gap-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-950/40 p-4 sm:p-5 lg:grid-cols-[minmax(145px,0.85fr)_minmax(0,1.2fr)_minmax(190px,1fr)] lg:items-center">
            <div class="flex items-center gap-3 lg:block">
                <span :class="session.type === 'match' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-300' : 'border-indigo-200 bg-indigo-50 text-indigo-800 dark:border-indigo-900 dark:bg-indigo-950/60 dark:text-indigo-300'" class="inline-flex shrink-0 rounded-lg border px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide">
                    {{ session.type === 'match' ? 'Utakmica' : 'Trening' }}
                </span>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lg:mt-3">{{ formatDate(session.scheduled_at) }}</p>
            </div>

            <div class="min-w-0">
                <h4 class="break-words text-lg font-bold text-slate-900 dark:text-white">{{ session.title }}</h4>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">📍 {{ session.location || 'Glavni Teren' }}</p>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200/80 pt-3 dark:border-slate-800 lg:border-t-0 lg:pt-0">
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ rsvp.accepted }}/{{ rsvp.total }}</span>
                    igrača potvrdilo ✅
                </p>
                <router-link to="/trainings" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-950">
                    Evidencija prisustva
                </router-link>
            </div>
        </div>

        <div v-else class="rounded-2xl border border-dashed border-slate-200/80 py-8 text-center text-sm italic text-slate-500 dark:border-slate-800 dark:text-slate-400">
            Nema zakazanih aktivacija za naredne dane.
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { getRsvpCounts } from '../../trainings/utils/trainingFormatters'

const props = defineProps({
    session: {
        type: Object,
        default: null
    },
    formatDate: {
        type: Function,
        required: true
    }
})

const rsvp = computed(() => getRsvpCounts(props.session))
</script>
