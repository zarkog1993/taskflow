<template>
  <button 
    @click="toggleTheme" 
    type="button"
    class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-amber-500 border border-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-amber-400 dark:border-gray-700/60 transition shadow-sm flex items-center justify-center cursor-pointer"
    :title="isDark ? 'Prebaci na Svetlu Temu' : 'Prebaci na Tamnu Temu'"
    aria-label="Promeni temu"
  >
    <!-- Sunce (prikazuje se u tamnoj temi, klik prebacuje na svetlu) -->
    <svg v-if="isDark" class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>

    <!-- Mesec (prikazuje se u svetloj temi, klik prebacuje na tamnu) -->
    <svg v-else class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
    </svg>
  </button>
</template>

<script setup>
import { useTheme } from '../composables/useTheme'

const { isDark, toggleTheme } = useTheme()
</script>