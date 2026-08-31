<template>
  <header class="bg-gray-800 border-b border-gray-700 px-6 py-4 flex items-center justify-between">
    <!-- Logo i naziv -->
    <div class="flex items-center space-x-3">
      <router-link to="/" class="flex items-center space-x-2">
        <span class="text-2xl font-bold text-indigo-400 tracking-tight">TaskFlow</span>
      </router-link>
      <span 
        v-if="authStore.isAuthenticated" 
        class="text-xs bg-indigo-500/20 text-indigo-300 px-2.5 py-1 rounded-full border border-indigo-500/30 font-medium"
      >
        Kanban Board
      </span>
      <router-link 
        to="/users" 
        class="text-sm font-medium text-gray-300 hover:text-white transition px-2 py-1 rounded"
        active-class="text-indigo-400 font-semibold"
      >
        Korisnici
    </router-link>
    </div>

    <!-- Meni za ULOGOVANE korisnike -->
    <div v-if="authStore.isAuthenticated" class="flex items-center space-x-4">
      <!-- Notifikacije -->
      <NotificationDropdown />

      <!-- Profil i Odjava -->
      <div class="flex items-center space-x-3 pl-4 border-l border-gray-700">
        <div class="flex flex-col text-right">
          <span class="text-sm font-semibold text-gray-200 leading-tight">
            {{ authStore.user?.name || 'Korisnik' }}
          </span>
          <span class="text-[11px] text-gray-400">
            {{ authStore.user?.email }}
          </span>
        </div>

        <button 
          @click="handleLogout"
          class="p-2 text-gray-400 hover:text-red-400 hover:bg-gray-700/50 rounded-lg transition"
          title="Odjavi se"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Meni za NE ULOGOVANE korisnike -->
    <div v-else class="flex items-center space-x-3">
      <router-link 
        to="/login" 
        class="text-sm font-medium text-gray-300 hover:text-white px-3 py-2 rounded-lg transition"
      >
        Prijava
      </router-link>
      <router-link 
        to="/register" 
        class="text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg shadow-md transition"
      >
        Registracija
      </router-link>
    </div>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import NotificationDropdown from './NotificationDropdown.vue'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>