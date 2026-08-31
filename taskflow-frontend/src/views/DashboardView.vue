<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Navbar sa notifikacijama i korisničkim menijem -->

    <!-- Glavni sadržaj -->
    <main class="p-6 max-w-7xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-200">Zadaci Projekta</h2>
        <button 
          @click="showModal = true"
          class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-md transition flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
          Novi Zadatak
        </button>
      </div>

      <!-- Kanban kolone idu ovde -->
      <!-- ... -->
    </main>
  </div>
</template>

<script setup>
  // Ostali uvozi...
  import { ref } from 'vue'

  const showModal = ref(false)
</script>