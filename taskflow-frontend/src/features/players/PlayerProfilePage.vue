<template>
    <main class="mx-auto max-w-[1280px] space-y-5 px-4 py-4 sm:px-6 sm:py-5">
        <div class="flex items-center justify-between">
            <router-link to="/players" class="text-xs font-medium text-slate-600 dark:text-slate-400 transition hover:text-gray-900 dark:hover:text-white">
                ← Nazad na igrače
            </router-link>
        </div>

        <div v-if="!player" class="py-20 text-center text-sm text-slate-600 dark:text-slate-400">
            <span class="mb-2 block animate-spin text-2xl">⏳</span>
            Učitavanje profila igrača...
        </div>

        <template v-else>
            <PlayerHeader :player="player" :format-foot="formatFoot" @edit="openEditModal" />
            <PlayerQuickStats :player="player" />

            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(16rem,1fr)]">
                <div class="min-w-0 space-y-4">
                    <PlayerStatistics :player="player" />
                    <PlayerRecentMatches :matches="recentMatches" :error="matchesError" />
                    <PlayerNotes :notes="player.coach_notes" />
                </div>

                <aside class="min-w-0 space-y-4">
                    <PlayerPositions :player="player" />
                </aside>
            </div>
        </template>

        <EditPlayerModal
            v-if="showEditModal && player"
            :form="editForm"
            :current-photo-url="player.photo_url"
            :is-submitting="isSubmitting"
            @close="showEditModal = false"
            @submit="onUpdatePlayer"
        />
    </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { fetchMatches } from '../../services/matchesService'
import EditPlayerModal from './components/EditPlayerModal.vue'
import PlayerHeader from './components/player/PlayerHeader.vue'
import PlayerNotes from './components/player/PlayerNotes.vue'
import PlayerInformation from './components/player/PlayerInformation.vue'
import PlayerPositions from './components/player/PlayerPositions.vue'
import PlayerQuickStats from './components/player/PlayerQuickStats.vue'
import PlayerRecentMatches from './components/player/PlayerRecentMatches.vue'
import PlayerStatistics from './components/player/PlayerStatistics.vue'
import { usePlayerProfile } from './composables/usePlayerProfile'

const route = useRoute()
const {
    player,
    showEditModal,
    isSubmitting,
    editForm,
    fetchPlayerProfile,
    openEditModal,
    handleUpdatePlayer,
    formatFoot
} = usePlayerProfile(route.params.id)

const matches = ref([])
const matchesError = ref('')

const recentMatches = computed(() => matches.value
    .filter((match) => match.status === 'completed')
    .map((match) => ({
        ...match,
        player_stats: (match.players || []).find((matchPlayer) =>
            String(matchPlayer.id) === String(player.value?.id) &&
            [true, 1, '1'].includes(matchPlayer.pivot?.attended)
        )?.pivot
    }))
    .filter((match) => match.player_stats)
    .sort((a, b) => new Date(b.scheduled_at) - new Date(a.scheduled_at))
    .slice(0, 8)
)

const loadRecentMatches = async () => {
    try {
        const response = await fetchMatches()
        matches.value = response.data?.data || []
    } catch (error) {
        console.error('Greška pri učitavanju utakmica igrača:', error)
        matchesError.value = error.response?.data?.message || 'Utakmice trenutno nisu dostupne.'
    }
}

onMounted(() => {
    fetchPlayerProfile()
    loadRecentMatches()
})

const onUpdatePlayer = (photoFile) => handleUpdatePlayer(photoFile, {
    onSuccess: () => { showEditModal.value = false }
})
</script>
