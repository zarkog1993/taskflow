<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <router-link to="/matches" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                    ← Nazad na utakmice
                </router-link>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white mt-2">Planer sastava</h1>
                <p v-if="match" class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                    {{ match.team?.name }} · {{ match.is_home ? 'Domaćin' : 'Gost' }} protiv {{ match.opponent }}
                </p>
            </div>

            <div class="grid w-full grid-cols-2 gap-2 sm:flex sm:w-auto sm:items-center sm:gap-3">
                <button
                    type="button"
                    :disabled="isLoading || isSaving || !confirmedPlayers.length"
                    @click="suggestStartingLineup"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white text-xs font-bold px-3 sm:px-4 py-2.5 rounded-xl transition"
                >
                    Predloži sastav
                </button>
                <AppSelect
                    v-model="selectedFormation"
                    :disabled="isLoading || isSaving"
                    @change="applyFormation"
                    aria-label="Formacija"
                    class="w-full sm:w-auto bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white text-xs font-bold rounded-xl px-3 py-2.5 outline-none cursor-pointer"
                >
                    <option value="4-3-3">Formacija 4-3-3</option>
                    <option value="4-4-2">Formacija 4-4-2</option>
                    <option value="4-2-3-1">Formacija 4-2-3-1</option>
                    <option value="3-5-2">Formacija 3-5-2</option>
                </AppSelect>
                <button
                    type="button"
                    :disabled="isLoading || isSaving"
                    @click="saveLineup"
                    class="col-span-2 w-full sm:col-span-1 sm:w-auto bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white text-xs font-bold px-5 py-2.5 rounded-xl transition"
                >
                    {{ isSaving ? 'Čuvanje...' : 'Sačuvaj predlog' }}
                </button>
            </div>
        </div>

        <p v-if="errorMessage || successMessage" role="status" aria-live="polite" class="text-sm font-semibold" :class="errorMessage ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-700 dark:text-emerald-400'">
            {{ errorMessage || successMessage }}
        </p>

        <div
            v-if="selectedPlayer"
            class="flex items-center justify-between gap-3 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-xs text-indigo-900 dark:border-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-100"
        >
            <span class="min-w-0 break-words">Izabran je <strong>{{ selectedPlayer.name }}</strong>. Dodirni poziciju na terenu za postavljanje ili zamenu.</span>
            <button type="button" class="min-h-10 shrink-0 font-bold underline" @click="selectedPlayerId = null">
                Otkaži
            </button>
        </div>

        <div v-if="isLoading" class="rounded-2xl bg-gray-100 dark:bg-gray-800 p-8 text-center text-sm text-gray-600 dark:text-gray-400">
            Učitavanje sastava i potvrđenih igrača...
        </div>

        <div v-else-if="match" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <TacticsPitch
                :field-spots="fieldSpots"
                :selected-player="selectedPlayer"
                @drop="onDropOnPitch"
                @drag-start="onDragStart"
                @spot-click="handleSpotClick"
            />

            <aside class="bg-gray-100/80 dark:bg-gray-800/80 border border-gray-200/80 dark:border-gray-700/80 rounded-3xl p-5 shadow-xl space-y-4">
                <div>
                    <h2 class="text-xs font-bold uppercase text-gray-700 dark:text-gray-300 tracking-wider">
                        Potvrdili dolazak ({{ confirmedPlayers.length }})
                    </h2>
                    <p class="text-[11px] text-gray-600 dark:text-gray-400 mt-1">
                        Izaberi igrača, pa dodirni poziciju na terenu. Izaberi startera i drugu poziciju da zameniš mesta.
                    </p>
                </div>

                <div v-if="!confirmedPlayers.length" class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-4 text-xs text-gray-600 dark:text-gray-400">
                    Još nema igrača koji su potvrdili dolazak.
                </div>
                <div v-else class="space-y-2 max-h-none overflow-visible pr-1 lg:max-h-[540px] lg:overflow-y-auto">
                    <div
                        v-for="player in confirmedPlayers"
                        :key="player.id"
                        class="flex items-center justify-between gap-2 rounded-xl border p-3"
                        :class="[playerStatusClass(player), selectedPlayerId === Number(player.id) ? 'ring-2 ring-indigo-500' : '']"
                    >
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-bold text-gray-900 dark:text-white">{{ player.name }}</p>
                            <p class="text-[10px] text-gray-600 dark:text-gray-400">
                                #{{ player.jersey_number || '—' }} · {{ player.primary_position || 'Bez pozicije' }} · {{ playerStatus(player) }}
                            </p>
                        </div>
                        <button
                            type="button"
                            :disabled="isSaving"
                            @click="handlePlayerAction(player)"
                            class="min-h-10 shrink-0 rounded-lg border border-gray-300 dark:border-gray-600 px-2 py-1 text-[10px] font-bold text-gray-700 dark:text-gray-300 disabled:opacity-50"
                        >
                            {{ playerActionLabel(player) }}
                        </button>
                        <button
                            v-if="assignedPlayerIds.has(Number(player.id)) || !isOnBench(player)"
                            type="button"
                            :disabled="isSaving"
                            @click="addToBench(player)"
                            class="min-h-10 shrink-0 rounded-lg border border-amber-300 dark:border-amber-800 px-2 py-1 text-[10px] font-bold text-amber-700 dark:text-amber-300 disabled:opacity-50"
                        >
                            Rezerva
                        </button>
                    </div>
                </div>

                <p class="border-t border-gray-200 dark:border-gray-700 pt-3 text-[10px] text-gray-600 dark:text-gray-400">
                    Ovo je predlog sastava. Čuvanje ne menja zapisnik niti statistiku utakmice.
                </p>
            </aside>
        </div>

        <PlayerPickerModal
            v-if="activeSpotForSelection"
            :spot="activeSpotForSelection"
            :filtered-users="filteredConfirmedPlayers"
            v-model:search-query="searchQuery"
            @close="closePlayerPicker"
            @assign="assignPlayerToSpot"
            @remove="removePlayerFromSpot"
        />
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import api from '../../services/api'
import PlayerPickerModal from '../tactics/components/PlayerPickerModal.vue'
import TacticsPitch from '../tactics/components/TacticsPitch.vue'
import { createDefaultFieldSpots, FORMATION_LAYOUTS } from '../tactics/constants/formations'

const route = useRoute()
const match = ref(null)
const confirmedPlayers = ref([])
const selectedFormation = ref('4-3-3')
const fieldSpots = ref(createDefaultFieldSpots())
const benchPlayerIds = ref([])
const activeSpotForSelection = ref(null)
const selectedPlayerId = ref(null)
const searchQuery = ref('')
const draggedSpot = ref(null)
const isLoading = ref(true)
const isSaving = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const filteredConfirmedPlayers = computed(() => {
    const query = searchQuery.value.trim().toLocaleLowerCase()
    return confirmedPlayers.value.filter(player =>
        !query || player.name.toLocaleLowerCase().includes(query),
    )
})

const assignedPlayerIds = computed(
    () => new Set(fieldSpots.value.map(spot => Number(spot.player?.id)).filter(Boolean)),
)
const selectedPlayer = computed(() =>
    confirmedPlayers.value.find(player => Number(player.id) === Number(selectedPlayerId.value)) || null,
)

const fetchLineup = async () => {
    try {
        const { data } = await api.get(`/matches/${route.params.id}/lineup`)
        match.value = data.data.match
        confirmedPlayers.value = data.data.confirmed_players
        selectedFormation.value = data.data.lineup.formation || '4-3-3'
        const playersById = new Map(confirmedPlayers.value.map(player => [Number(player.id), player]))
        const savedPositions = new Map(
            (data.data.lineup.positions || []).map(position => [Number(position.spot_id), position]),
        )
        fieldSpots.value = createDefaultFieldSpots().map(spot => {
            const savedPosition = savedPositions.get(spot.id)
            return savedPosition
                ? {
                    ...spot,
                    x: Number(savedPosition.x),
                    y: Number(savedPosition.y),
                    player: playersById.get(Number(savedPosition.player_id)) || null,
                }
                : spot
        })
        benchPlayerIds.value = (data.data.lineup.bench_player_ids || [])
            .map(Number)
            .filter(playerId => playersById.has(playerId))

        if (!data.data.has_saved_lineup) {
            suggestStartingLineup()
        }
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Nije moguće učitati predlog sastava.'
    } finally {
        isLoading.value = false
    }
}

const applyFormation = () => {
    const layout = FORMATION_LAYOUTS[selectedFormation.value]
    if (!layout) return

    layout.forEach((coordinates, index) => {
        const spot = fieldSpots.value[index + 1]
        if (spot) Object.assign(spot, coordinates)
    })
}

const openPlayerPicker = (spot) => {
    activeSpotForSelection.value = spot
    searchQuery.value = ''
}

const handleSpotClick = (spot) => {
    if (!selectedPlayer.value) {
        openPlayerPicker(spot)
        return
    }

    assignPlayerToSpotAt(selectedPlayer.value, spot)
    selectedPlayerId.value = null
    errorMessage.value = ''
}

const closePlayerPicker = () => {
    activeSpotForSelection.value = null
}

const assignPlayerToSpot = (player) => {
    if (!activeSpotForSelection.value) return
    assignPlayerToSpotAt(player, activeSpotForSelection.value)
    selectedPlayerId.value = null
    activeSpotForSelection.value = null
}

const assignPlayerToSpotAt = (player, spot) => {
    const playerId = Number(player.id)
    const sourceSpot = fieldSpots.value.find(fieldSpot =>
        fieldSpot !== spot && Number(fieldSpot.player?.id) === playerId,
    )

    if (sourceSpot) {
        const displacedPlayer = spot.player
        spot.player = player
        sourceSpot.player = displacedPlayer || null
        if (displacedPlayer) {
            benchPlayerIds.value = benchPlayerIds.value.filter(id => Number(id) !== Number(displacedPlayer.id))
        }
    } else {
        const displacedPlayer = spot.player
        if (displacedPlayer && Number(displacedPlayer.id) !== playerId) {
            benchPlayerIds.value = [...new Set([...benchPlayerIds.value, Number(displacedPlayer.id)])]
        }
        spot.player = player
    }

    benchPlayerIds.value = benchPlayerIds.value.filter(id => Number(id) !== playerId)
}

const suggestStartingLineup = () => {
    const available = [...confirmedPlayers.value]
    const chosenIds = new Set()

    fieldSpots.value.forEach(spot => {
        const player = spot.player
            ? available.find(candidate => Number(candidate.id) === Number(spot.player.id))
            : null

        if (!player) {
            spot.player = null
            return
        }

        chosenIds.add(Number(player.id))
    })

    const positions = {
        GK: ['GK', 'G'],
        RB: ['RB', 'RWB', 'DEF'],
        CB: ['CB', 'DEF'],
        LB: ['LB', 'LWB', 'DEF'],
        CM: ['CM', 'CDM', 'CAM', 'LM', 'RM', 'MID'],
        RW: ['RW', 'RM', 'W'],
        ST: ['ST', 'CF', 'FW'],
        LW: ['LW', 'LM', 'W'],
    }

    fieldSpots.value.forEach(spot => {
        if (spot.player) return

        const preferredPositions = positions[spot.position] || []
        const availablePlayers = available.filter(player => !chosenIds.has(Number(player.id)))
        const player = availablePlayers.find(candidate =>
            preferredPositions.includes(String(candidate.primary_position || '').toUpperCase()),
        ) || availablePlayers[0]

        if (player) {
            spot.player = player
            chosenIds.add(Number(player.id))
        }
    })

    benchPlayerIds.value = available
        .map(player => Number(player.id))
        .filter(playerId => !chosenIds.has(playerId))
    errorMessage.value = ''
    successMessage.value = ''
}

const removePlayerFromSpot = () => {
    if (!activeSpotForSelection.value) return
    activeSpotForSelection.value.player = null
    activeSpotForSelection.value = null
}

const onDragStart = (event, spot) => {
    draggedSpot.value = spot
}

const onDropOnPitch = (event) => {
    if (!draggedSpot.value) return
    const rect = event.currentTarget.getBoundingClientRect()
    draggedSpot.value.x = Math.round(Math.min(Math.max(((event.clientX - rect.left) / rect.width) * 100, 5), 95))
    draggedSpot.value.y = Math.round(Math.min(Math.max(((event.clientY - rect.top) / rect.height) * 100, 5), 95))
    draggedSpot.value = null
}

const isOnBench = (player) => benchPlayerIds.value.includes(Number(player.id))

const playerStatus = (player) => {
    if (assignedPlayerIds.value.has(Number(player.id))) return 'Početni sastav'
    return isOnBench(player) ? 'Rezerva' : 'Dostupan'
}

const playerStatusClass = (player) => {
    if (assignedPlayerIds.value.has(Number(player.id))) {
        return 'border-indigo-300 bg-indigo-50/70 dark:border-indigo-800 dark:bg-indigo-950/40'
    }
    return isOnBench(player)
        ? 'border-amber-300 bg-amber-50/70 dark:border-amber-800 dark:bg-amber-950/40'
        : 'border-gray-200 bg-white/70 dark:border-gray-700 dark:bg-gray-900/60'
}

const toggleBench = (player) => {
    const playerId = Number(player.id)
    if (Number(selectedPlayerId.value) === playerId) selectedPlayerId.value = null
    if (assignedPlayerIds.value.has(playerId)) {
        fieldSpots.value.forEach(spot => {
            if (Number(spot.player?.id) === playerId) spot.player = null
        })
        benchPlayerIds.value = [...benchPlayerIds.value.filter(id => Number(id) !== playerId), playerId]
        return
    }

    benchPlayerIds.value = isOnBench(player)
        ? benchPlayerIds.value.filter(id => id !== playerId)
        : [...benchPlayerIds.value, playerId]
}

const addToBench = (player) => {
    const playerId = Number(player.id)
    if (assignedPlayerIds.value.has(playerId)) {
        toggleBench(player)
        return
    }
    if (isOnBench(player)) return

    benchPlayerIds.value = [...benchPlayerIds.value, playerId]
    if (Number(selectedPlayerId.value) === playerId) selectedPlayerId.value = null
}

const handlePlayerAction = (player) => {
    const playerId = Number(player.id)
    selectedPlayerId.value = Number(selectedPlayerId.value) === playerId ? null : playerId
    errorMessage.value = ''
}

const playerActionLabel = (player) => {
    return Number(selectedPlayerId.value) === Number(player.id) ? 'Otkaži izbor' : 'Izaberi'
}

const saveLineup = async () => {
    if (isSaving.value) return

    isSaving.value = true
    errorMessage.value = ''
    successMessage.value = ''
    try {
        await api.put(`/matches/${route.params.id}/lineup`, {
            formation: selectedFormation.value,
            positions: fieldSpots.value.map(spot => ({
                spot_id: spot.id,
                x: spot.x,
                y: spot.y,
                player_id: spot.player?.id ?? null,
            })),
            bench_player_ids: benchPlayerIds.value,
        })
        successMessage.value = 'Predlog sastava je sačuvan.'
    } catch (error) {
        const validationError = Object.values(error.response?.data?.errors || {}).flat()[0]
        errorMessage.value = validationError || error.response?.data?.message || 'Nije moguće sačuvati predlog sastava.'
    } finally {
        isSaving.value = false
    }
}

onMounted(fetchLineup)
</script>
