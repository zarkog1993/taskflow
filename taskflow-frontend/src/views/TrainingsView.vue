<template>
  <div class="p-6 max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h2 class="text-2xl font-bold text-white">Raspored Treninga i Utakmica</h2>
        <p class="text-sm text-gray-400 mt-1">Upravljanje nedeljnim trenažnim mikrociklusima</p>
      </div>
      <button 
        @click="showModal = true"
        class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Zakaži Trening / Utakmicu
      </button>
    </div>

    <!-- Prikaz greške -->
    <div v-if="trainingStore.error" class="mb-4 p-3 bg-red-500/20 border border-red-500 text-red-300 rounded-lg text-sm">
      {{ trainingStore.error }}
    </div>

    <!-- Loader -->
    <div v-if="trainingStore.loading" class="text-center py-12 text-gray-400">
      Učitavanje rasporeda...
    </div>

    <!-- Kanban Tabla -->
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Kolona: Planirano -->
      <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-4 flex flex-col h-[calc(100vh-180px)]">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-700">
          <h3 class="font-bold text-gray-300 uppercase text-xs tracking-wider flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
            Planirano
          </h3>
          <span class="bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded-full font-semibold">{{ trainingStore.plannedSessions.length }}</span>
        </div>
        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
          <TrainingCard 
            v-for="session in trainingStore.plannedSessions" 
            :key="session.id" 
            :session="session" 
            @change-status="trainingStore.updateSessionStatus"
          />
        </div>
      </div>

      <!-- Kolona: U Toku -->
      <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-4 flex flex-col h-[calc(100vh-180px)]">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-700">
          <h3 class="font-bold text-yellow-400 uppercase text-xs tracking-wider flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
            U Toku / Današnji Trening
          </h3>
          <span class="bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded-full font-semibold">{{ trainingStore.inProgressSessions.length }}</span>
        </div>
        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
          <TrainingCard 
            v-for="session in trainingStore.inProgressSessions" 
            :key="session.id" 
            :session="session" 
            @change-status="trainingStore.updateSessionStatus"
          />
        </div>
      </div>

      <!-- Kolona: Završeno -->
      <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-4 flex flex-col h-[calc(100vh-180px)]">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-700">
          <h3 class="font-bold text-emerald-400 uppercase text-xs tracking-wider flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            Završeno / Analizirano
          </h3>
          <span class="bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded-full font-semibold">{{ trainingStore.completedSessions.length }}</span>
        </div>
        <div class="flex-1 overflow-y-auto space-y-3 pr-1">
          <TrainingCard 
            v-for="session in trainingStore.completedSessions" 
            :key="session.id" 
            :session="session" 
            @change-status="trainingStore.updateSessionStatus"
          />
        </div>
      </div>
    </div>

    <!-- Modal za Zakazivanje Treninga -->
    <div v-if="showModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
      <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-bold text-white mb-4">Zakaži Trening ili Utakmicu</h3>

        <form @submit.prevent="handleCreateSession" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Tema / Naslov</label>
            <input 
              v-model="newSession.title" 
              type="text" 
              required 
              placeholder="npr. Tranzicija po izgubljenoj lopti"
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500" 
            />
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Tip Događaja</label>
            <select v-model="newSession.type" class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none">
              <option value="training">Trening</option>
              <option value="match">Utakmica</option>
              <option value="tactical_analysis">Taktička Analiza</option>
              <option value="fitness">Kondicija / Oporavak</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Datum i Vreme</label>
            <input 
              v-model="newSession.scheduled_at" 
              type="datetime-local" 
              required 
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500" 
            />
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Lokacija / Teren</label>
            <input 
              v-model="newSession.location" 
              type="text" 
              placeholder="npr. Teren sa veštačkom travom A"
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500" 
            />
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase text-gray-400 mb-1">Opis / Taktičke Napomene</label>
            <textarea 
              v-model="newSession.description" 
              rows="3" 
              placeholder="Fokus na visok pritisak na zadnju liniju..."
              class="w-full bg-gray-900 border border-gray-700 rounded p-2.5 text-white outline-none focus:border-indigo-500"
            ></textarea>
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
            <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Odustani</button>
            <button type="submit" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded font-medium">Sačuvaj</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useTrainingStore } from '../stores/training'
import TrainingCard from '../components/TrainingCard.vue'

const trainingStore = useTrainingStore()
const showModal = ref(false)

const newSession = reactive({
  title: '',
  type: 'training',
  scheduled_at: '',
  location: '',
  description: '',
  status: 'planned'
})

onMounted(() => {
  trainingStore.fetchSessions()
})

const handleCreateSession = async () => {
  const success = await trainingStore.createSession(newSession)
  if (success) {
    showModal.value = false
    newSession.title = ''
    newSession.type = 'training'
    newSession.scheduled_at = ''
    newSession.location = ''
    newSession.description = ''
  }
}
</script>