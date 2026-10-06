<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
        @click.self="$emit('close')"
    >
        <section
            role="dialog"
            aria-modal="true"
            aria-labelledby="attendance-overview-title"
            class="flex max-h-[calc(100dvh-2rem)] w-full max-w-xl flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900"
        >
            <header class="flex items-start justify-between gap-4 border-b border-gray-200 p-5 dark:border-gray-800">
                <div>
                    <h2 id="attendance-overview-title" class="text-lg font-black text-gray-900 dark:text-white">
                        Odziv igrača
                    </h2>
                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                        {{ monthName }} {{ year }} · pregled odgovora na poziv
                    </p>
                </div>
                <button
                    type="button"
                    aria-label="Zatvori pregled odziva"
                    title="Zatvori"
                    class="rounded-lg px-2 py-1 text-lg text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white"
                    @click="$emit('close')"
                >
                    ×
                </button>
            </header>

            <div class="min-h-0 space-y-4 overflow-y-auto p-5">
                <label v-if="sessions.length > 1" class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                    Trening
                    <select
                        v-model="selectedSessionId"
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >
                        <option v-for="session in sessions" :key="session.id" :value="session.id">
                            {{ session.title }} · {{ formatDate(session.scheduled_at) }}
                        </option>
                    </select>
                </label>

                <div v-else-if="selectedSession" class="text-sm font-bold text-gray-900 dark:text-white">
                    {{ selectedSession.title }}
                    <span class="ml-1 text-xs font-normal text-gray-500">{{ formatDate(selectedSession.scheduled_at) }}</span>
                </div>

                <p class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs text-sky-900 dark:border-sky-900 dark:bg-sky-950/50 dark:text-sky-200">
                    Status prikazuje odgovor igrača na pozivnicu; stvarno prisustvo se ne evidentira odvojeno.
                </p>

                <div v-if="!selectedSession" class="py-8 text-center text-sm text-gray-500">
                    Nema treninga za izabrani mesec.
                </div>
                <div v-else-if="!players.length" class="py-8 text-center text-sm text-gray-500">
                    Za ovaj trening nema pozvanih igrača.
                </div>

                <section
                    v-for="group in groups"
                    v-else
                    :key="group.key"
                    class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800"
                >
                    <h3 class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-3 py-2 text-xs font-bold uppercase text-gray-700 dark:border-gray-800 dark:bg-gray-800/60 dark:text-gray-300">
                        <span>{{ group.label }}</span>
                        <span>{{ group.players.length }}</span>
                    </h3>
                    <ul v-if="group.players.length" class="divide-y divide-gray-100 dark:divide-gray-800">
                        <li
                            v-for="player in group.players"
                            :key="player.id"
                            class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-900 dark:text-white"
                        >
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                {{ initials(player.name) }}
                            </span>
                            {{ player.name }}
                        </li>
                    </ul>
                    <p v-else class="px-3 py-3 text-xs text-gray-500">Nema igrača u ovoj grupi.</p>
                </section>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { getInvitedPlayers, getRsvpStatus } from '../utils/trainingFormatters'

const props = defineProps({
    sessions: { type: Array, required: true },
    monthName: { type: String, required: true },
    year: { type: Number, required: true }
})

defineEmits(['close'])

const selectedSessionId = ref(props.sessions[0]?.id ?? null)

watch(() => props.sessions, (sessions) => {
    if (!sessions.some(session => session.id === selectedSessionId.value)) {
        selectedSessionId.value = sessions[0]?.id ?? null
    }
})

const selectedSession = computed(() =>
    props.sessions.find(session => session.id === selectedSessionId.value) ?? null
)

const players = computed(() => getInvitedPlayers(selectedSession.value))

const groups = computed(() => {
    const statusGroups = [
        { key: 'accepted', label: 'Potvrdili dolazak' },
        { key: 'declined', label: 'Odbili dolazak' },
        { key: 'pending', label: 'Bez odgovora' }
    ]

    return statusGroups.map(group => ({
        ...group,
        players: players.value
            .filter(player => getRsvpStatus(player) === group.key)
            .sort((first, second) => first.name.localeCompare(second.name, 'sr'))
    }))
})

function formatDate(date) {
    if (!date) return ''
    return new Date(date).toLocaleDateString('sr-Latn-RS', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

function initials(name = '') {
    return name.split(/\s+/).filter(Boolean).slice(0, 2).map(part => part[0]).join('').toLocaleUpperCase('sr-Latn-RS')
}
</script>