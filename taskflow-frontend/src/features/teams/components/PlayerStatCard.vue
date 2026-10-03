<template>
    <article
        class="group flex flex-col rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-4 shadow-md transition-all hover:border-slate-200 dark:hover:border-slate-700"
    >
        <!-- Zaglavlje: avatar, ime, bedževi -->
        <div
            role="link"
            tabindex="0"
            class="flex cursor-pointer items-start gap-3 rounded-lg outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
            @click="openProfile"
            @keydown.enter="openProfile"
        >
            <div class="relative h-12 w-12 shrink-0 overflow-hidden rounded-full border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                <img
                    v-if="player.photo_url"
                    :src="player.photo_url"
                    :alt="player.name"
                    class="h-full w-full object-cover"
                />
                <span v-else class="flex h-full w-full items-center justify-center text-sm font-bold text-slate-700 dark:text-slate-300">
                    {{ initials }}
                </span>
            </div>

            <div class="min-w-0 flex-1">
                <h3 class="truncate text-base font-bold text-gray-900 dark:text-white transition-colors group-hover:text-indigo-700 dark:group-hover:text-indigo-300">
                    {{ player.name }}
                </h3>
                <span class="mt-1 inline-block rounded-full border border-indigo-500/30 bg-indigo-500/20 px-2.5 py-0.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                    {{ player.primary_position || 'CM' }}
                </span>
            </div>

            <span class="shrink-0 rounded-full border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                #{{ player.jersey_number ?? '-' }}
            </span>
        </div>

        <!-- Statistika -->
        <dl class="my-3 grid grid-cols-3 gap-2 rounded-xl border border-slate-200/80 dark:border-slate-800/80 bg-slate-50/60 dark:bg-slate-950/60 p-2.5 text-center">
            <div>
                <dt class="text-[10px] font-semibold uppercase text-slate-600 dark:text-slate-400">Utakmice</dt>
                <dd class="text-sm font-bold text-gray-900 dark:text-white">{{ getStat(player, 'matches_played') }}</dd>
            </div>
            <div>
                <dt class="text-[10px] font-semibold uppercase text-slate-600 dark:text-slate-400">Golovi</dt>
                <dd class="text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ getStat(player, 'goals') }}</dd>
            </div>
            <div>
                <dt class="text-[10px] font-semibold uppercase text-slate-600 dark:text-slate-400">Asistencije</dt>
                <dd class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ getStat(player, 'assists') }}</dd>
            </div>
        </dl>

        <!-- Akcija -->
        <button
            type="button"
            class="mt-auto flex w-full items-center justify-center gap-1.5 rounded-xl bg-slate-100/80 dark:bg-slate-800/80 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 transition hover:bg-slate-100 dark:hover:bg-slate-800"
            @click="$emit('edit', player)"
        >
            ✏️ Izmeni učinak
        </button>
    </article>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { getPlayerCategory } from '../../players/utils/playerFormatters'

const props = defineProps({
    player: {
        type: Object,
        required: true
    },
    getStat: {
        type: Function,
        required: true
    }
})

defineEmits(['edit'])

const router = useRouter()

const initials = computed(() => String(props.player.name || '')
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0).toUpperCase())
    .join('') || '?')

const openProfile = () => router.push(`/players/${props.player.id}`)
</script>
