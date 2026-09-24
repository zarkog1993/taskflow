<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- ZAGLAVLJE -->
        <div class="bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Super Admin Control Center</h1>
                <p class="text-xs text-gray-400 mt-0.5">Pregled svih registrovanih klubova, njihovih vlasnika, pretplata i timova</p>
            </div>
            <span class="px-3 py-1 bg-indigo-950 border border-indigo-800 text-indigo-400 text-xs font-mono font-bold rounded-xl">
                SUPER ADMIN
            </span>
        </div>

        <!-- STATISTIKA KLUBOVA -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">UKUPNO KLUBOVA</span>
                    <div class="text-2xl font-black text-white mt-1 font-mono">{{ clubs.length }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-950 border border-indigo-800 text-indigo-400 flex items-center justify-center text-lg">🏢</div>
            </div>

            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">AKTIVNE PRETPLE</span>
                    <div class="text-2xl font-black text-emerald-400 mt-1 font-mono">{{ activeSubscriptionsCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-950 border border-emerald-800 text-emerald-400 flex items-center justify-center text-lg">💳</div>
            </div>

            <div class="bg-gray-800/80 border border-gray-700/80 p-5 rounded-2xl flex justify-between items-center shadow-lg">
                <div>
                    <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">UKUPNO TIMOVA U SISTEMU</span>
                    <div class="text-2xl font-black text-amber-400 mt-1 font-mono">{{ totalSystemTeamsCount }}</div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-950 border border-amber-800 text-amber-400 flex items-center justify-center text-lg">🛡️</div>
            </div>
        </div>

        <!-- LISTA KLUBOVA SA VLASNICIMA I TIMOVIMA -->
        <div v-if="loading" class="text-center py-12 text-gray-400 italic">Učitavanje podataka o klubovima...</div>

        <div v-else-if="clubs.length" class="space-y-6">
            <div v-for="club in clubs" :key="club.id" class="bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl space-y-4">
                <!-- Header Kluba i Vlasnika -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-700/60 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-black text-white">{{ club.name }}</h2>
                            <span class="text-[10px] font-bold uppercase px-2.5 py-0.5 rounded bg-indigo-950 text-indigo-400 border border-indigo-800">
                                ID: #{{ club.id }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            Vlasnik / Admin: <strong class="text-gray-200">{{ club.owner?.name || 'Nije dodijeljen' }}</strong>
                            <span v-if="club.owner?.email" class="text-gray-500">({{ club.owner.email }})</span>
                        </p>
                    </div>

                    <!-- Paket i Status -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase px-3 py-1 rounded-xl bg-emerald-950 text-emerald-400 border border-emerald-800">
                            Paket: {{ club.subscription?.plan_type || 'Nema pretplate' }}
                        </span>
                        <span class="text-xs font-mono font-bold px-3 py-1 rounded-xl bg-gray-900 border border-gray-700 text-gray-300">
                            Max Timova: {{ club.subscription?.max_teams || 0 }}
                        </span>
                    </div>
                </div>

                <!-- Timovi Kluba -->
                <div>
                    <h4 class="text-[10px] font-bold uppercase text-gray-400 tracking-wider mb-3">
                        Kreirani Timovi ({{ club.teams?.length || 0 }})
                    </h4>

                    <div v-if="club.teams && club.teams.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        <div v-for="team in club.teams" :key="team.id" class="p-3 bg-gray-900/80 border border-gray-700/60 rounded-2xl flex justify-between items-center">
                            <div>
                                <p class="text-xs font-bold text-white">{{ team.name }}</p>
                                <span class="text-[10px] font-mono text-indigo-400 uppercase">{{ team.age_group || team.category }}</span>
                            </div>
                            <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-950 border border-emerald-800 px-2 py-0.5 rounded-lg">
                                {{ team.players?.length || 0 }} igrača
                            </span>
                        </div>
                    </div>
                    <p v-else class="text-xs text-gray-500 italic py-3 text-center bg-gray-900/40 rounded-xl border border-gray-800">
                        Ovaj klub još uvek nema kreiranih timova.
                    </p>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-16 bg-gray-800/40 border border-gray-700/50 rounded-2xl text-gray-400 italic">
            Nema pronađenih klubova u sistemu.
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api'

const clubs = ref([])
const loading = ref(true)

const fetchSuperAdminData = async () => {
    try {
        const response = await api.get('/admin/clubs')
        clubs.value = response.data.data || response.data || []
    } catch (err) {
        console.error('Greška pri učitavanju super admin podataka:', err)
    } finally {
        loading.value = false
    }
}

onMounted(fetchSuperAdminData)

const activeSubscriptionsCount = computed(() => {
    return clubs.value.filter(c => c.subscription && c.subscription.status === 'active').length
})

const totalSystemTeamsCount = computed(() => {
    return clubs.value.reduce((acc, club) => acc + (club.teams?.length || 0), 0)
})
</script>