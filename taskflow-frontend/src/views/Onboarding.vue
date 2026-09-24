<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const step = ref(1)
const loading = ref(false)
const errorMessage = ref('')

const form = ref({
    club_name: '',
    plan_type: 'basic'
})

const plans = [
    {
        id: 'basic',
        name: 'Basic',
        price: '20€',
        period: '/ mesečno',
        teams: '1 Tim',
        players: 'Do 25 igrača',
        features: ['Osnovna analitika', 'Evidencija treninga', 'Prikaz utakmica'],
        recommended: false
    },
    {
        id: 'pro',
        name: 'Pro Academy',
        price: '50€',
        period: '/ mesečno',
        teams: 'Do 5 Timova',
        players: 'Do 150 igrača',
        features: ['Napredna statistika igrača', 'Prisustvo na treninzima', 'Izveštaji za roditelje', 'Prioritetna podrška'],
        recommended: true
    },
    {
        id: 'unlimited',
        name: 'Unlimited',
        price: '100€',
        period: '/ mesečno',
        teams: 'Neograničeno timova',
        players: 'Neograničeno igrača',
        features: ['Sve iz Pro paketa', 'Više klupskih admina', 'Custom domen', '24/7 Podrška'],
        recommended: false
    }
]

const submitOnboarding = async (planId) => {
    form.value.plan_type = planId
    loading.value = true
    errorMessage.value = ''

    // Preuzimanje tokena iz localStorage
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token')

    try {
        const response = await axios.post(
            '/api/onboarding/complete',
            {
                club_name: form.value.club_name,
                plan_type: form.value.plan_type
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: 'application/json'
                }
            }
        )

        if (response.status === 201 || response.status === 200) {
            // Uspešno kreirano, preusmeravanje na upravljanje ekipama
            router.push('/teams')
        }
    } catch (error) {
        if (error.response?.status === 401) {
            errorMessage.value = 'Sesija je istekla ili niste ulogovani. Molimo vas prijavite se ponovo.'
        } else {
            errorMessage.value = error.response?.data?.message || 'Došlo je do greške pri kreiranju kluba.'
        }
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="min-h-screen bg-slate-900 text-white flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-emerald-400">TaskFlow Sport</h1>
            <p class="mt-2 text-sm text-slate-400">Podesite vaš klub i izaberite paket za početak</p>
        </div>

        <!-- KORAK 1: Unos naziva kluba -->
        <div v-if="step === 1" class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-slate-800 py-8 px-6 shadow-xl rounded-xl border border-slate-700">
                <form @submit.prevent="step = 2" class="space-y-6">
                    <div>
                        <label for="club_name" class="block text-sm font-medium text-slate-200">Naziv vašeg kluba / akademije</label>
                        <div class="mt-2">
                            <input
                                id="club_name"
                                v-model="form.club_name"
                                type="text"
                                required
                                placeholder="npr. FK Rudar"
                                class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="!form.club_name.trim()"
                        class="w-full flex justify-center py-3 px-4 rounded-lg text-sm font-semibold text-slate-900 bg-emerald-400 hover:bg-emerald-300 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Nastavi na izbor paketa &rarr;
                    </button>
                </form>
            </div>
        </div>

        <!-- KORAK 2: Odabir paketa -->
        <div v-else-if="step === 2" class="max-w-6xl mx-auto w-full">
            <div class="flex items-center justify-between mb-8">
                <button @click="step = 1" class="text-sm text-emerald-400 hover:underline flex items-center gap-1">
                    &larr; Nazad na naziv kluba ({{ form.club_name }})
                </button>
                <span class="text-xs font-semibold px-3 py-1 bg-slate-800 border border-slate-700 rounded-full text-slate-300">Korak 2 od 2</span>
            </div>

            <div v-if="errorMessage" class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-xl">
                {{ errorMessage }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    :class="[
            'bg-slate-800 rounded-2xl p-6 border transition flex flex-col justify-between relative',
            plan.recommended ? 'border-emerald-500 shadow-lg shadow-emerald-500/10' : 'border-slate-700 hover:border-slate-600'
          ]"
                >
                    <div v-if="plan.recommended" class="absolute -top-3 right-6 bg-emerald-500 text-slate-950 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                        Najpopularnije
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-white">{{ plan.name }}</h3>
                        <div class="mt-4 flex items-baseline">
                            <span class="text-4xl font-extrabold text-white">{{ plan.price }}</span>
                            <span class="ml-1 text-slate-400 text-sm">{{ plan.period }}</span>
                        </div>

                        <div class="mt-6 space-y-2 border-t border-b border-slate-700 py-4">
                            <div class="flex items-center text-sm text-slate-200 font-medium">
                                <svg class="w-4 h-4 text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="5 13l4 4L19 7"></path></svg>
                                {{ plan.teams }}
                            </div>
                            <div class="flex items-center text-sm text-slate-200 font-medium">
                                <svg class="w-4 h-4 text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="5 13l4 4L19 7"></path></svg>
                                {{ plan.players }}
                            </div>
                        </div>

                        <ul class="mt-4 space-y-2">
                            <li v-for="feature in plan.features" :key="feature" class="text-xs text-slate-400 flex items-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-2"></span>
                                {{ feature }}
                            </li>
                        </ul>
                    </div>

                    <button
                        @click="submitOnboarding(plan.id)"
                        :disabled="loading"
                        class="mt-8 w-full py-3 px-4 rounded-xl text-sm font-semibold transition bg-emerald-400 hover:bg-emerald-300 text-slate-950 disabled:opacity-50"
                    >
                        {{ loading ? 'Aktivacija...' : 'Izaberi paket i aktiviraj' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>