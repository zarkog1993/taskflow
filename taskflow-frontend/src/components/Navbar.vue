<template>
  <header class="bg-gray-900 border-b border-gray-800 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
      <div class="flex items-center space-x-6">
        <router-link to="/" class="flex items-center space-x-2">
          <span class="text-2xl font-bold text-indigo-400 tracking-tight">TaskFlow</span>
        </router-link>

        <nav v-if="authStore.isAuthenticated" class="flex items-center space-x-3 pl-4 border-l border-gray-800">
          <router-link 
            to="/" 
            class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg"
            active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30"
          >
            Dashboard
          </router-link>

          <router-link 
            to="/trainings" 
            class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg"
            active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30"
          >
            Trening Sesije
          </router-link>

          <router-link 
            to="/players" 
            class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg"
            active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30"
          >
            Igrači / Registar
          </router-link>

          <router-link 
            to="/users" 
            class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg"
            active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30"
          >
            Korisnici
          </router-link>
        </nav>
      </div>

      <div v-if="authStore.isAuthenticated" class="flex items-center space-x-4">
        <div class="text-right">
          <div class="text-xs font-bold text-white">{{ authStore.user?.name }}</div>
          <div class="text-[10px] text-gray-400">{{ authStore.user?.email }}</div>
        </div>
        <button 
          @click="authStore.logout" 
          class="text-gray-400 hover:text-red-400 p-2 transition"
          title="Odjava"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
</script>