<template>
    <div class="min-h-screen bg-gray-950 flex items-center justify-center p-4">
        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-8 max-w-md w-full text-center space-y-6 shadow-2xl">
            <div
                class="w-20 h-20 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner"
                :class="isAttending ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-800' : 'bg-rose-950/80 text-rose-400 border border-rose-800'"
            >
                {{ isAttending ? '✅' : '❌' }}
            </div>

            <div>
                <h2 class="text-2xl font-black text-white">
                    {{ isAttending ? 'Uspešno ste potvrdili dolazak!' : 'Zabeleženo je da ne dolazite.' }}
                </h2>
                <p class="text-xs text-gray-400 mt-2">
                    Vaš status za <strong class="text-indigo-400">{{ eventName }}</strong> je ažuriran u sistemu.
                </p>
            </div>

            <div class="pt-4 border-t border-gray-800 space-y-3">
                <p class="text-xs text-gray-500">
                    Proverite e-mail sanduče i otvorite priloženi <strong>.ics</strong> fajl da automatski dodate događaj u kalendar telefona.
                </p>
                <router-link
                    to="/calendar"
                    class="inline-block w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition shadow-lg"
                >
                    Otvorite Kalendar Aplikacije
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isAttending = computed(() => route.query.status === 'attended')
const eventName = computed(() => route.query.event || 'Događaj')
</script>
