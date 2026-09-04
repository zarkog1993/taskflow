<template>
  <div class="bg-gray-800 border border-gray-700/80 rounded-xl p-4 shadow-md hover:border-indigo-500/50 transition">
    <div class="flex justify-between items-start mb-2">
      <div>
        <!-- Bedž za tip (Trening / Utakmica / Taktika) -->
        <span
          :class="[
            'text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-full mb-1.5 inline-block',
            session.type === 'match' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' :
            session.type === 'tactical_analysis' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' :
            session.type === 'fitness' ? 'bg-orange-500/20 text-orange-400 border border-orange-500/30' :
            'bg-blue-500/20 text-blue-400 border border-blue-500/30'
          ]"
        >
          {{ sessionTypeLabel(session.type) }}
        </span>
        <h4 class="font-bold text-sm text-white leading-snug">{{ session.title }}</h4>
      </div>
    </div>

    <p v-if="session.description" class="text-xs text-gray-400 mb-3 line-clamp-2">
      {{ session.description }}
    </p>

    <!-- Lokacija i Vreme -->
    <div class="space-y-1 text-xs text-gray-400 mb-3 bg-gray-900/60 p-2.5 rounded-lg border border-gray-700/50">
      <div v-if="session.scheduled_at" class="flex items-center gap-1.5 text-indigo-300">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 9 0 0118 0z"></path></svg>
        <span>{{ formatDate(session.scheduled_at) }}</span>
      </div>
      <div v-if="session.location" class="flex items-center gap-1.5 text-gray-300">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
        <span>{{ session.location }}</span>
      </div>
    </div>

    <!-- Prisustvo i Status -->
    <div class="flex justify-between items-center pt-2 border-t border-gray-700/50 text-xs">
      <span class="text-[11px] text-gray-400 flex items-center gap-1">
        👥 <strong>{{ session.attendances?.length || 0 }}</strong> igrač(a)
      </span>

      <select 
        :value="session.status" 
        @change="$emit('change-status', session.id, $event.target.value)"
        class="bg-gray-900 border border-gray-700 text-gray-200 text-[11px] rounded px-2 py-1 outline-none focus:border-indigo-500 font-medium"
      >
        <option value="planned">Planirano</option>
        <option value="in_progress">U Toku</option>
        <option value="completed">Završeno</option>
      </select>
    </div>
  </div>
</template>

<script setup>
defineProps({
  session: {
    type: Object,
    required: true
  }
})

defineEmits(['change-status'])

const sessionTypeLabel = (type) => {
  const labels = {
    training: 'Trening',
    match: 'Utakmica',
    tactical_analysis: 'Taktika',
    fitness: 'Kondicija'
  }
  return labels[type] || 'Trening'
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  return date.toLocaleString('sr-RS', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>