<template>
    <header class="bg-white/90 dark:bg-gray-900/90 border-b border-gray-200 dark:border-gray-800 backdrop-blur sticky top-0 z-40 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-4 sm:space-x-6">
                <router-link to="/" class="flex items-center space-x-2">
                    <span class="text-xl sm:text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">Pravi Fudbal</span>
                </router-link>

                <!-- Desktop Navigacija -->
                <nav v-if="authStore.isAuthenticated" class="hidden md:flex items-center space-x-1 sm:space-x-2 pl-4 border-l border-gray-200 dark:border-gray-800">
                    <router-link to="/" class="text-xs font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-50 text-indigo-600 border border-indigo-200 dark:bg-indigo-600/20 dark:text-indigo-400 dark:border-indigo-500/30">Dashboard</router-link>
                    <router-link to="/matches" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" :class="$route.path === '/matches' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white'">Utakmice & Zapisnik</router-link>
                    <router-link to="/trainings" class="text-xs font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-50 text-indigo-600 border border-indigo-200 dark:bg-indigo-600/20 dark:text-indigo-400 dark:border-indigo-500/30">Trening Sesije</router-link>
                    <router-link to="/tactics" class="text-xs font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-50 text-indigo-600 border border-indigo-200 dark:bg-indigo-600/20 dark:text-indigo-400 dark:border-indigo-500/30">Taktika</router-link>
                    <router-link to="/teams" class="text-xs font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-50 text-indigo-600 border border-indigo-200 dark:bg-indigo-600/20 dark:text-indigo-400 dark:border-indigo-500/30">Moj Tim</router-link>
                    <router-link to="/players" class="text-xs font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-50 text-indigo-600 border border-indigo-200 dark:bg-indigo-600/20 dark:text-indigo-400 dark:border-indigo-500/30">Igrači / Registar</router-link>
                    <router-link v-if="authStore.user?.is_admin || authStore.user?.roles?.some(r => r.slug === 'admin')" to="/users" class="text-xs font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition px-3 py-1.5 rounded-lg" active-class="bg-indigo-50 text-indigo-600 border border-indigo-200 dark:bg-indigo-600/20 dark:text-indigo-400 dark:border-indigo-500/30">Korisnici</router-link>
                </nav>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Theme Toggle uvek dostupan na desnoj strani -->
                <router-link to="/calendar" class="text-xs font-bold px-3 py-2 rounded-xl transition" :class="$route.path === '/calendar' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white'">📅 Kalendar</router-link>
                <ThemeToggle />

                <template v-if="authStore.isAuthenticated">
                    <div class="hidden sm:block text-right">
                        <div class="text-xs font-bold text-gray-900 dark:text-white">{{ authStore.user?.name }}</div>
                        <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ authStore.user?.email }}</div>
                    </div>

                    <button @click="authStore.logout" class="text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400 p-2 transition hidden md:block" title="Odjava">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>

                    <!-- Hamburger Dugme za Mobilni -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700 dark:text-gray-300 p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </template>
                <template v-else>
                    <router-link to="/login" class="text-xs font-semibold text-gray-700 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 px-3 py-1.5 transition">Prijava</router-link>
                    <router-link to="/register" class="text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white px-3.5 py-1.5 rounded-xl shadow transition">Registracija</router-link>
                </template>
            </div>
        </div>

        <!-- Mobilni Padajući Meni -->
        <div v-if="mobileMenuOpen && authStore.isAuthenticated" class="md:hidden bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 pt-2 pb-4 space-y-2 shadow-lg transition-colors duration-200">
            <div class="sm:hidden pb-2 border-b border-gray-200 dark:border-gray-800">
                <div class="text-xs font-bold text-gray-900 dark:text-white">{{ authStore.user?.name }}</div>
                <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ authStore.user?.email }}</div>
            </div>
            <router-link @click="mobileMenuOpen = false" to="/" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Dashboard</router-link>
            <router-link
                to="/matches"
                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                :class="$route.path === '/matches' ? 'bg-indigo-600 text-white' : 'text-gray-400 hover:text-white'"
            >
                Utakmice & Zapisnik
            </router-link>
            <router-link @click="mobileMenuOpen = false" to="/trainings" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Trening Sesije</router-link>
            <router-link @click="mobileMenuOpen = false" to="/teams" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Moj Tim</router-link>
            <router-link @click="mobileMenuOpen = false" to="/players" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Igrači / Registar</router-link>
            <router-link v-if="authStore.user?.is_admin || authStore.user?.roles?.some(r => r.slug === 'admin')" @click="mobileMenuOpen = false" to="/users" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 py-2 px-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">Korisnici</router-link>
            <button @click="authStore.logout" class="w-full text-left text-sm font-semibold text-red-600 dark:text-red-400 py-2 px-3 rounded-lg hover:bg-red-50 dark:hover:bg-gray-800 transition">Odjavi se</button>
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import ThemeToggle from '../components/ThemeToggle.vue'

const authStore = useAuthStore()
const mobileMenuOpen = ref(false)
</script>