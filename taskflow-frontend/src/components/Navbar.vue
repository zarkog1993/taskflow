<template>
    <header class="bg-gray-900 border-b border-gray-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-4 sm:space-x-6">
                <router-link to="/" class="flex items-center space-x-2">
                    <span class="text-xl sm:text-2xl font-bold text-indigo-400 tracking-tight">TaskFlow</span>
                </router-link>

                <!-- Desktop Navigacija -->
                <nav v-if="authStore.isAuthenticated" class="hidden md:flex items-center space-x-2 pl-4 border-l border-gray-800">
                    <router-link to="/" class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">Dashboard</router-link>
                    <router-link to="/trainings" class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">Trening Sesije</router-link>
                    <router-link to="/teams" class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">Moj Tim</router-link>
                    <router-link to="/players" class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">Igrači / Registar</router-link>
                    <router-link v-if="authStore.user?.is_admin || authStore.user?.roles?.some(r => r.slug === 'admin')" to="/users" class="text-xs font-semibold text-gray-300 hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">Korisnici</router-link>
                </nav>
            </div>

            <div v-if="authStore.isAuthenticated" class="flex items-center space-x-3">
                <div class="hidden sm:block text-right">
                    <div class="text-xs font-bold text-white">{{ authStore.user?.name }}</div>
                    <div class="text-[10px] text-gray-400">{{ authStore.user?.email }}</div>
                </div>

                <button @click="authStore.logout" class="text-gray-400 hover:text-red-400 p-2 transition hidden md:block" title="Odjava">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>

                <!-- Hamburger Dugme za Mobilni -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-300 p-2 rounded-lg bg-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>

        <!-- Mobilni Padajući Meni -->
        <div v-if="mobileMenuOpen && authStore.isAuthenticated" class="md:hidden bg-gray-900 border-b border-gray-800 px-4 pt-2 pb-4 space-y-2">
            <router-link @click="mobileMenuOpen = false" to="/" class="block text-sm font-semibold text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-800">Dashboard</router-link>
            <router-link @click="mobileMenuOpen = false" to="/trainings" class="block text-sm font-semibold text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-800">Trening Sesije</router-link>
            <router-link @click="mobileMenuOpen = false" to="/teams" class="block text-sm font-semibold text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-800">Moj Tim</router-link>
            <router-link @click="mobileMenuOpen = false" to="/players" class="block text-sm font-semibold text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-800">Igrači / Registar</router-link>
            <router-link v-if="authStore.user?.is_admin || authStore.user?.roles?.some(r => r.slug === 'admin')" @click="mobileMenuOpen = false" to="/users" class="block text-sm font-semibold text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-800">Korisnici</router-link>
            <button @click="authStore.logout" class="w-full text-left text-sm font-semibold text-red-400 py-2 px-3 rounded-lg hover:bg-gray-800">Odjavi se</button>
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const mobileMenuOpen = ref(false)
</script>