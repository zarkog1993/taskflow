<template>
    <section class="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between gap-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Nedavne aktivnosti</h3>
            <router-link to="/trainings" class="text-xs font-semibold text-indigo-700 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                Sve aktivnosti →
            </router-link>
        </div>

        <ul v-if="activities.length" class="divide-y divide-slate-100 dark:divide-slate-800">
            <li v-for="activity in activities" :key="activity.id" class="flex gap-3 py-3 first:pt-0 last:pb-0">
                <span :class="activity.kind === 'rsvp' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300'" class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold">
                    {{ activity.kind === 'rsvp' ? '✓' : '＋' }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="wrap-break-word text-xs leading-5 text-slate-800 dark:text-slate-200">
                        <template v-if="activity.kind === 'rsvp'">
                            <span class="font-semibold">{{ activity.playerName }}</span>
                            {{ activity.status === 'accepted' ? 'je potvrdio/la dolazak na' : 'ne dolazi na' }}
                            <span class="font-semibold">{{ activity.title }}</span>
                        </template>
                        <template v-else>
                            {{ activity.eventType }} je zakazan:
                            <span class="font-semibold">{{ activity.title }}</span>
                        </template>
                    </p>
                    <time :datetime="activity.timestamp" class="mt-1 block text-[11px] text-slate-500 dark:text-slate-400">
                        {{ formatTimestamp(activity.timestamp) }}
                    </time>
                </div>
            </li>
        </ul>

        <p v-else class="rounded-xl border border-dashed border-slate-200/80 px-3 py-6 text-center text-xs text-slate-500 dark:border-slate-800 dark:text-slate-400">
            Nema nedavnih aktivnosti.
        </p>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { getInvitedPlayers } from '../../trainings/utils/trainingFormatters'

const props = defineProps({
    sessions: {
        type: Array,
        default: () => []
    }
})

const activities = computed(() => props.sessions.flatMap(session => {
    const items = []

    if (session.created_at) {
        items.push({
            id: `scheduled-${session.id}`,
            kind: 'scheduled',
            title: session.title,
            eventType: session.type === 'match' ? 'Utakmica' : 'Trening',
            timestamp: session.created_at
        })
    }

    for (const player of getInvitedPlayers(session)) {
        const status = player.pivot?.status
        const timestamp = player.pivot?.responded_at

        if (!timestamp || !['accepted', 'declined'].includes(status)) continue

        items.push({
            id: `rsvp-${session.id}-${player.id}-${timestamp}`,
            kind: 'rsvp',
            title: session.title,
            playerName: player.name,
            status,
            timestamp
        })
    }

    return items
}).sort((first, second) => new Date(second.timestamp) - new Date(first.timestamp)).slice(0, 5))

const formatTimestamp = (timestamp) => new Date(timestamp).toLocaleString('sr-RS', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
})
</script>