<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 space-y-8">
        <!-- Zaglavlje i Globalni Filteri -->
        <div class="bg-gray-800/60 p-6 rounded-2xl border border-gray-700/60 backdrop-blur-md shadow-xl space-y-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                        <span>💳</span> Finansije i Članarine
                    </h1>
                    <p class="text-xs text-gray-400 mt-1">
                        Objedinjena evidencija mesečnih članarina akademije i isplata prvotimcima.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                    <label class="flex items-center gap-2 text-xs">
                        <span class="text-gray-400 font-medium whitespace-nowrap">Ekipa:</span>
                        <select
                            v-model="selectedTeamId"
                            class="bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500 w-full sm:w-56"
                        >
                            <option :value="null">Sve ekipe</option>
                            <option v-for="team in teams" :key="team.id" :value="team.id">
                                {{ team.name }}
                            </option>
                        </select>
                    </label>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-400 font-medium whitespace-nowrap">Mesec:</span>
                        <MonthPicker v-model="selectedPeriod" />
                    </div>
                </div>
            </div>

            <!-- Podrazumevani Mesečni Iznos -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-gray-900/60 border border-gray-700/60 rounded-xl p-3">
                <label class="flex items-center gap-2 text-xs flex-1">
                    <span class="text-gray-400 font-medium whitespace-nowrap">
                        {{ activeType === 'membership' ? 'Mesečna članarina:' : 'Mesečni honorar:' }}
                    </span>
                    <input
                        type="number"
                        min="0"
                        step="100"
                        v-model.number="monthlyAmount"
                        class="w-32 bg-gray-950 border border-gray-700 rounded-lg px-2.5 py-1.5 text-sm font-semibold text-emerald-400 focus:outline-none focus:border-indigo-500"
                    />
                    <span class="text-[11px] text-gray-500">
                        Primenjuje se na igrače bez evidentirane uplate ({{ unsavedCount }}).
                    </span>
                </label>

                <button
                    @click="applyAmountToAll"
                    :disabled="bulkSaving || rows.length === 0"
                    class="px-4 py-2 text-xs font-semibold rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition disabled:opacity-50 whitespace-nowrap"
                >
                    {{ bulkSaving ? 'Primenjujem...' : 'Primeni na sve igrače' }}
                </button>
            </div>

            <!-- KPI Kartice -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-900/60 border border-gray-700/60 rounded-xl p-4">
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold">Ukupno očekivano</p>
                    <p class="text-2xl font-black text-white mt-1">{{ formatCurrency(summary.expected) }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        {{ summary.players_count }} igrača × {{ formatCurrency(monthlyAmount) }}
                    </p>
                </div>
                <div class="bg-gray-900/60 border border-emerald-500/30 rounded-xl p-4">
                    <p class="text-[11px] uppercase tracking-wider text-emerald-400 font-semibold">Naplaćeno / Isplaćeno</p>
                    <p class="text-2xl font-black text-emerald-400 mt-1">{{ formatCurrency(summary.collected) }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">{{ summary.paid_count }} evidentiranih uplata</p>
                </div>
                <div class="bg-gray-900/60 border border-amber-500/30 rounded-xl p-4">
                    <p class="text-[11px] uppercase tracking-wider text-amber-400 font-semibold">Na čekanju / Kasni</p>
                    <p class="text-2xl font-black text-amber-400 mt-1">{{ formatCurrency(summary.outstanding) }}</p>
                    <p class="text-[11px] text-gray-500 mt-1">
                        Kasni: <span class="text-rose-400 font-semibold">{{ formatCurrency(summary.overdue) }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Glavni Tabovi -->
        <div class="flex bg-gray-900/80 p-1 rounded-xl border border-gray-700 text-xs w-full sm:w-fit">
            <button
                @click="activeType = 'membership'"
                :class="activeType === 'membership' ? 'bg-indigo-600 text-white font-bold shadow-md' : 'text-gray-400 hover:text-white'"
                class="px-4 py-2.5 rounded-lg transition flex-1 sm:flex-none flex items-center justify-center gap-2"
            >
                <span>🧾</span> Mesečne Članarine (Akademija)
            </button>
            <button
                @click="activeType = 'stipend'"
                :class="activeType === 'stipend' ? 'bg-indigo-600 text-white font-bold shadow-md' : 'text-gray-400 hover:text-white'"
                class="px-4 py-2.5 rounded-lg transition flex-1 sm:flex-none flex items-center justify-center gap-2"
            >
                <span>💰</span> Isplate Prvotimcima
            </button>
        </div>

        <!-- Greška -->
        <div
            v-if="error"
            class="bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm rounded-xl px-4 py-3"
        >
            {{ error }}
        </div>

        <!-- Učitavanje -->
        <div v-if="loading" class="text-center py-20 text-gray-400 font-medium flex justify-center items-center gap-3">
            <svg class="animate-spin h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Učitavanje finansijskih podataka...
        </div>

        <!-- Tabela -->
        <div v-else-if="rows.length > 0" class="overflow-x-auto rounded-2xl border border-gray-700/60 bg-gray-800/40">
            <table class="w-full text-left text-sm text-gray-300">
                <thead class="bg-gray-900/80 text-xs uppercase text-gray-400 border-b border-gray-700">
                    <tr>
                        <th class="py-3 px-4">Igrač</th>
                        <th class="py-3 px-4">Dres / Pozicija</th>
                        <th class="py-3 px-4">Iznos (RSD)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Akcija</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60">
                    <tr v-for="row in rows" :key="row.player_id" class="hover:bg-gray-800/60 transition">
                        <td class="py-3 px-4">
                            <p class="font-semibold text-white">{{ row.player_name }}</p>
                            <p v-if="!selectedTeamId" class="text-[11px] text-gray-500">{{ row.team_name }}</p>
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-400 font-mono">
                            #{{ row.jersey_number ?? '-' }} | {{ row.position || 'Igrač' }}
                        </td>
                        <td class="py-3 px-4">
                            <input
                                type="number"
                                min="0"
                                step="100"
                                v-model.number="row.amount"
                                @change="persistRow(row)"
                                :disabled="savingIds.has(row.player_id)"
                                class="w-28 bg-gray-950 border border-gray-700 rounded-lg px-2.5 py-1 text-sm font-semibold text-emerald-400 focus:outline-none focus:border-indigo-500 disabled:opacity-50"
                            />
                        </td>
                        <td class="py-3 px-4">
                            <span
                                :class="statusBadgeClass(row.status)"
                                class="px-2.5 py-1 rounded-full text-xs font-bold border uppercase tracking-wider inline-block"
                            >
                                {{ STATUS_LABELS[row.status] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button
                                    v-for="action in STATUS_ACTIONS"
                                    :key="action.value"
                                    @click="setStatus(row, action.value)"
                                    :disabled="savingIds.has(row.player_id)"
                                    :class="row.status === action.value ? action.activeClass : action.idleClass"
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-700 transition disabled:opacity-50"
                                >
                                    {{ action.label }}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Prazno stanje -->
        <div v-else class="text-center py-16 bg-gray-800/40 rounded-2xl border border-dashed border-gray-700/60">
            <p class="text-sm text-gray-400 italic">
                Nema igrača za izabranu ekipu i period.
            </p>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import MonthPicker from '../components/MonthPicker.vue'
import { fetchTeams } from '../services/teamsService'
import { bulkSavePayments, fetchFinancesOverview, savePayment } from '../services/paymentsService'

const DEFAULT_AMOUNTS = {
    membership: 4000,
    stipend: 15000
}

const AMOUNT_STORAGE_KEY = 'finances.monthlyAmount'

const STATUS_LABELS = {
    paid: 'Plaćeno',
    pending: 'Na čekanju',
    overdue: 'Kasni'
}

const STATUS_ACTIONS = [
    {
        value: 'paid',
        label: '✓ Plaćeno',
        activeClass: 'bg-emerald-600 text-white',
        idleClass: 'bg-gray-800 text-gray-300 hover:bg-emerald-600/30'
    },
    {
        value: 'pending',
        label: '⏳ Čeka',
        activeClass: 'bg-amber-600 text-white',
        idleClass: 'bg-gray-800 text-gray-300 hover:bg-amber-600/30'
    },
    {
        value: 'overdue',
        label: '✕ Kasni',
        activeClass: 'bg-rose-600 text-white',
        idleClass: 'bg-gray-800 text-gray-300 hover:bg-rose-600/30'
    }
]

const currencyFormatter = new Intl.NumberFormat('sr-RS', {
    style: 'currency',
    currency: 'RSD',
    maximumFractionDigits: 0
})

const teams = ref([])
const rows = ref([])
const activeType = ref('membership')
const selectedTeamId = ref(null)
const selectedPeriod = ref(new Date().toISOString().slice(0, 7))
const monthlyAmount = ref(readStoredAmount('membership'))
const loading = ref(false)
const bulkSaving = ref(false)
const error = ref('')
const savingIds = reactive(new Set())

const unsavedCount = computed(() => rows.value.filter((row) => !row.has_payment).length)

const summary = computed(() => {
    const totals = { expected: 0, paid: 0, pending: 0, overdue: 0, paid_count: 0 }

    for (const row of rows.value) {
        const amount = Number(row.amount) || 0
        totals.expected += amount
        totals[row.status] += amount
        if (row.status === 'paid') totals.paid_count += 1
    }

    return {
        expected: totals.expected,
        collected: totals.paid,
        pending: totals.pending,
        overdue: totals.overdue,
        outstanding: totals.pending + totals.overdue,
        paid_count: totals.paid_count,
        players_count: rows.value.length
    }
})

const formatCurrency = (value) => currencyFormatter.format(Number(value) || 0)

function readStoredAmount(type) {
    const stored = Number(localStorage.getItem(`${AMOUNT_STORAGE_KEY}.${type}`))
    return Number.isFinite(stored) && stored > 0 ? stored : DEFAULT_AMOUNTS[type]
}

const statusBadgeClass = (status) => ({
    'bg-emerald-500/10 text-emerald-400 border-emerald-500/30': status === 'paid',
    'bg-amber-500/10 text-amber-400 border-amber-500/30': status === 'pending',
    'bg-rose-500/10 text-rose-400 border-rose-500/30': status === 'overdue'
})

const loadTeams = async () => {
    try {
        teams.value = await fetchTeams()
    } catch (err) {
        console.error('Greška pri učitavanju ekipa:', err)
    }
}

const loadOverview = async () => {
    loading.value = true
    error.value = ''
    try {
        const overview = await fetchFinancesOverview({
            teamId: selectedTeamId.value,
            period: selectedPeriod.value,
            type: activeType.value
        })
        // Igrači bez evidentirane uplate dobijaju podrazumevani mesečni iznos.
        rows.value = (overview.rows ?? []).map((row) => ({
            ...row,
            amount: row.has_payment ? Number(row.amount) : monthlyAmount.value
        }))
    } catch (err) {
        error.value = err.response?.data?.message || 'Neuspešno učitavanje finansijskih podataka.'
        rows.value = []
    } finally {
        loading.value = false
    }
}

// Šalje izmenu na backend bez ponovnog učitavanja cele tabele.
const persistRow = async (row) => {
    savingIds.add(row.player_id)
    error.value = ''
    try {
        const saved = await savePayment({
            team_id: row.team_id,
            player_id: row.player_id,
            type: activeType.value,
            period: selectedPeriod.value,
            amount: Number(row.amount) || 0,
            status: row.status
        })
        row.payment_id = saved.id
        row.paid_at = saved.paid_at
        row.has_payment = true
        return true
    } catch (err) {
        error.value = err.response?.data?.message || 'Neuspešno čuvanje uplate.'
        return false
    } finally {
        savingIds.delete(row.player_id)
    }
}

const setStatus = async (row, status) => {
    if (row.status === status) return

    const previousStatus = row.status
    row.status = status

    if (!(await persistRow(row))) {
        row.status = previousStatus
    }
}

// Upisuje trenutni mesečni iznos svim igračima u pregledu i snima ga na backend.
const applyAmountToAll = async () => {
    if (rows.value.length === 0) return

    bulkSaving.value = true
    error.value = ''
    try {
        await bulkSavePayments({
            type: activeType.value,
            period: selectedPeriod.value,
            items: rows.value.map((row) => ({
                team_id: row.team_id,
                player_id: row.player_id,
                amount: Number(monthlyAmount.value) || 0,
                status: row.status
            }))
        })

        for (const row of rows.value) {
            row.amount = Number(monthlyAmount.value) || 0
            row.has_payment = true
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Neuspešna primena iznosa na sve igrače.'
    } finally {
        bulkSaving.value = false
    }
}

watch(activeType, (type) => {
    monthlyAmount.value = readStoredAmount(type)
})

watch(monthlyAmount, (amount) => {
    localStorage.setItem(`${AMOUNT_STORAGE_KEY}.${activeType.value}`, String(amount ?? ''))

    for (const row of rows.value) {
        if (!row.has_payment) {
            row.amount = Number(amount) || 0
        }
    }
})

watch([activeType, selectedTeamId, selectedPeriod], loadOverview)

onMounted(async () => {
    await loadTeams()
    await loadOverview()
})
</script>
