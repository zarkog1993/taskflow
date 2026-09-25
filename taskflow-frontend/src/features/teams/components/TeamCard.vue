<template>
    <div class="bg-gray-800/80 border border-gray-700/80 rounded-3xl p-6 shadow-xl space-y-4 flex flex-col justify-between">
        <div class="space-y-4">
            <!-- Header Ekipe -->
            <div class="flex justify-between items-start border-b border-gray-700/60 pb-3">
                <div>
          <span class="text-[10px] font-bold uppercase text-indigo-400 bg-indigo-950 px-2.5 py-0.5 rounded border border-indigo-800">
            SELEKCIJA EKIPE
          </span>
                    <h3 class="text-xl font-black text-white mt-1">{{ team.name }}</h3>
                    <p class="text-xs text-gray-400">Ukupno {{ players.length }} dodeljenih igrač(a)</p>
                </div>
                <span class="text-xs font-black text-gray-300 bg-gray-900 border border-gray-700 px-3 py-1 rounded-xl uppercase">
          {{ team.category || 'Seniori' }}
        </span>
            </div>

            <!-- IGRAČKI KADAR EKIPE -->
            <div class="space-y-2">
                <div class="flex justify-between text-[10px] font-bold uppercase text-gray-400 px-1">
                    <span>IGRAČKI KADAR</span>
                    <span>DRES / POZICIJA</span>
                </div>

                <div v-if="players.length" class="space-y-2 max-h-64 overflow-y-auto pr-1">
                    <div
                        v-for="player in players"
                        :key="player.id"
                        class="p-3 bg-gray-900/80 border border-gray-700/60 rounded-xl flex items-center justify-between hover:border-gray-600 transition"
                    >
                        <!-- Slika i Ime -->
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-950 border border-indigo-800 overflow-hidden flex items-center justify-center font-bold text-indigo-300 text-xs shrink-0">
                                <img v-if="player.photo_url" :src="player.photo_url" :alt="player.name" class="w-full h-full object-cover" />
                                <span v-else>#{{ player.jersey_number || '-' }}</span>
                            </div>
                            <div>
                                <router-link :to="`/players/${player.id}`" class="text-xs font-bold text-white hover:text-indigo-400 transition">
                                    {{ player.name }}
                                </router-link>
                                <div class="text-[10px] text-gray-400 font-mono">{{ player.email || 'Nema email' }}</div>
                            </div>
                        </div>

                        <!-- Pozicija i DRES -->
                        <div class="flex items-center gap-2">
              <span class="text-[10px] font-black px-2 py-0.5 rounded bg-indigo-950 text-indigo-400 border border-indigo-800">
                {{ player.primary_position }}
              </span>
                            <span class="text-[10px] font-mono font-bold text-gray-300 bg-gray-800 px-2 py-0.5 rounded border border-gray-700">
                #{{ player.jersey_number || '-' }}
              </span>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-8 text-xs text-gray-500 italic bg-gray-900/40 rounded-xl border border-gray-800">
                    Nema dodeljenih igrača za ovu ekipu.
                </div>
            </div>
        </div>

        <!-- Akcije na dnu kartice -->
        <div class="flex items-center gap-3 pt-3 border-t border-gray-700/60">
            <button @click="$emit('manage', team)" class="flex-1 py-2.5 bg-gray-900 hover:bg-gray-700 text-gray-300 text-xs font-bold rounded-xl border border-gray-700 transition cursor-pointer flex items-center justify-center gap-1.5">
                ⚙️ Upravljaj Sastavom
            </button>
            <router-link :to="`/teams/${team.id}`" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl text-center transition shadow-lg border border-indigo-500/30">
                Prikaži Detalje →
            </router-link>
        </div>
    </div>
</template>

<script setup>
defineProps({
    team: {
        type: Object,
        required: true
    },
    players: {
        type: Array,
        default: () => []
    }
})

defineEmits(['manage'])
</script>
