<template>
    <main class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <router-link
            :to="{ name: 'matches' }"
            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400"
        >
            &larr; Nazad na utakmice
        </router-link>

        <p v-if="loading" role="status" class="py-16 text-center text-gray-600 dark:text-gray-400">
            Učitavanje zapisnika...
        </p>
        <div v-else-if="loadError" role="alert" class="rounded-2xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/40 p-6 text-red-700 dark:text-red-300">
            <p>{{ loadError }}</p>
            <button type="button" @click="loadMatch(route.params.id)" class="mt-3 text-sm font-bold underline">
                Pokušaj ponovo
            </button>
        </div>
        <template v-else-if="selectedMatch">
            <p v-if="saveError" role="alert" class="rounded-xl bg-red-50 dark:bg-red-950/40 p-4 text-red-700 dark:text-red-300">
                {{ saveError }}
            </p>
            <p v-if="savedMessage" role="status" class="rounded-xl bg-emerald-50 dark:bg-emerald-950/40 p-4 text-emerald-700 dark:text-emerald-300">
                {{ savedMessage }}
            </p>
            <MatchReport
                :key="selectedMatch.id"
                :match="selectedMatch"
                :stats-form="statsForm"
                :is-saving="isSaving"
                @close="router.push({ name: 'matches' })"
                @save="saveMatchStats"
            />
        </template>
    </main>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { onBeforeRouteLeave, onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'
import { fetchMatches } from '../../services/matchesService'
import { useMatchStats } from '../../composables/useMatchStats'
import MatchReport from './components/MatchReport.vue'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const loadError = ref('')
const {
    selectedMatch,
    statsForm,
    initializeMatch,
    saveMatchStats,
    isSaving,
    saveError,
    savedMessage,
    savedForm
} = useMatchStats()

const hasUnsavedChanges = computed(() =>
    selectedMatch.value !== null && JSON.stringify(statsForm.value) !== savedForm.value
)

const canLeave = () => {
    if (isSaving.value) {
        window.alert('Sačekajte da se zapisnik sačuva.')
        return false
    }
    return !hasUnsavedChanges.value || window.confirm('Imate nesačuvane izmene. Napustiti zapisnik?')
}

onBeforeRouteLeave(canLeave)
onBeforeRouteUpdate((to, from) => to.params.id === from.params.id || canLeave())

let loadVersion = 0
const loadMatch = async (id) => {
    const version = ++loadVersion
    loading.value = true
    loadError.value = ''
    selectedMatch.value = null
    try {
        const response = await fetchMatches()
        if (version !== loadVersion) return
        const match = response.data.data.find((item) => String(item.id) === String(id))
        if (!match) {
            loadError.value = 'Utakmica nije pronađena ili nemate pristup ovom zapisniku.'
            return
        }
        initializeMatch(match)
    } catch (err) {
        if (version === loadVersion) {
            loadError.value = err.response?.data?.message || 'Greška pri učitavanju zapisnika.'
        }
    } finally {
        if (version === loadVersion) loading.value = false
    }
}

watch(() => route.params.id, loadMatch, { immediate: true })
</script>
