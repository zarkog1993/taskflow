<template>
    <header class="bg-slate-900/95 border-b border-slate-800 backdrop-blur sticky top-0 z-50 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">

            <!-- LEVA STRANA: Logo & Glavna Navigacija -->
            <div class="flex items-center gap-6 min-w-0">
                <router-link :to="dashboardRoute" class="flex items-center gap-2 shrink-0">
          <span class="text-xl font-extrabold tracking-tight text-emerald-400 hover:text-emerald-300 transition">
            Pravi Fudbal
          </span>
                </router-link>

                <!-- Desktop Navigacija -->
                <nav v-if="authStore.isAuthenticated && canManage" class="hidden lg:flex items-center gap-1.5 pl-6 border-l border-slate-800 overflow-x-auto no-scrollbar">
                    <router-link
                        :to="dashboardRoute"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === dashboardRoute || $route.path === '/dashboard' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                    >
                        Dashboard
                    </router-link>

                    <router-link
                        v-if="hasFeature('matches')"
                        to="/matches"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/matches' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                    >
                        Utakmice
                    </router-link>

                    <router-link
                        v-if="hasFeature('teams')"
                        to="/trainings"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/trainings' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                    >
                        Trening
                    </router-link>

                    <router-link
                        v-if="hasFeature('tactics')"
                        to="/tactics"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/tactics' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                    >
                        Taktika
                    </router-link>

                    <router-link
                        v-if="hasFeature('advanced_stats')"
                        to="/analytics"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/analytics' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                    >
                        Analitika
                    </router-link>

                    <router-link
                        v-if="hasFeature('teams')"
                        to="/teams"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/teams' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                    >
                        Moj Tim
                    </router-link>

                    <router-link
                        v-if="hasFeature('players')"
                        to="/players"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/players' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                    >
                        Igrači
                    </router-link>

                    <router-link
                        v-if="isAdmin"
                        to="/users"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/users' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                    >
                        Korisnici
                    </router-link>
                </nav>
            </div>

            <!-- DESNA STRANA: Profil, Super Admin oznaka, Kalendar, Logout -->
            <div class="flex items-center gap-3 shrink-0">

                <!-- Kalendar dugme -->
                <router-link
                    to="/calendar"
                    class="text-xs font-semibold px-3 py-2 rounded-xl transition flex items-center gap-1.5"
                    :class="$route.path === '/calendar' ? 'bg-emerald-500 text-slate-950 font-bold' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-800 border border-slate-700/60'"
                >
                    <span>📅</span>
                    <span class="hidden sm:inline">Kalendar</span>
                </router-link>

                <!-- Super Admin Brza Oznaka -->
                <router-link
                    v-if="isSuperAdmin"
                    to="/super-admin"
                    class="text-[11px] font-bold px-2.5 py-1.5 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/30 hover:bg-amber-500/20 transition flex items-center gap-1.5"
                >
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Super Admin</span>
                </router-link>

                <ThemeToggle />

                <!-- Korisnički profil & Odjava -->
                <template v-if="authStore.isAuthenticated">
                    <div class="hidden sm:flex flex-col text-right pl-2 border-l border-slate-800">
            <span class="text-xs font-bold text-slate-100 truncate max-w-[120px]">
              {{ authStore.user?.name }}
            </span>
                        <span class="text-[10px] text-slate-400 truncate max-w-[120px]">
              {{ authStore.user?.email }}
            </span>
                    </div>

                    <button
                        @click="authStore.logout"
                        class="text-slate-400 hover:text-rose-400 p-2 rounded-xl hover:bg-slate-800 transition hidden lg:block"
                        title="Odjava"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>

                    <!-- Mobilni meni dugme -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden text-slate-300 p-2 rounded-xl bg-slate-800 hover:bg-slate-700 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </template>

                <template v-else>
                    <router-link to="/login" class="text-xs font-semibold text-slate-300 hover:text-white px-3 py-1.5 transition">
                        Prijava
                    </router-link>
                    <router-link to="/register" class="text-xs font-bold bg-emerald-400 hover:bg-emerald-300 text-slate-950 px-3.5 py-1.5 rounded-xl transition">
                        Registracija
                    </router-link>
                </template>
            </div>
        </div>

        <!-- Mobilni Padajući Meni -->
        <div
            v-if="mobileMenuOpen && authStore.isAuthenticated && canManage"
            class="lg:hidden bg-slate-900 border-b border-slate-800 px-4 pt-3 pb-5 space-y-2 shadow-2xl"
        >
            <div class="pb-3 border-b border-slate-800 mb-2">
                <div class="text-sm font-bold text-white">{{ authStore.user?.name }}</div>
                <div class="text-xs text-slate-400">{{ authStore.user?.email }}</div>
            </div>

            <router-link
                v-if="hasFeature('tactics')"
                @click="mobileMenuOpen = false"
                :to="dashboardRoute"
                class="block text-sm font-semibold text-slate-300 py-2 px-3 rounded-xl hover:bg-slate-800 transition"
            >
                Dashboard
            </router-link>

            <router-link
                v-if="hasFeature('matches')"
                @click="mobileMenuOpen = false"
                to="/matches"
                class="block text-sm font-semibold text-slate-300 py-2 px-3 rounded-xl hover:bg-slate-800 transition"
            >
                Utakmice & Zapisnik
            </router-link>

            <router-link
                v-if="hasFeature('teams')"
                @click="mobileMenuOpen = false"
                to="/trainings"
                class="block text-sm font-semibold text-slate-300 py-2 px-3 rounded-xl hover:bg-slate-800 transition"
            >
                Trening Sesije
            </router-link>

            <router-link
                @click="mobileMenuOpen = false"
                to="/tactics"
                class="block text-sm font-semibold text-slate-300 py-2 px-3 rounded-xl hover:bg-slate-800 transition"
            >
                Taktika
            </router-link>

            <router-link
                v-if="hasFeature('teams')"
                @click="mobileMenuOpen = false"
                to="/teams"
                class="block text-sm font-semibold text-slate-300 py-2 px-3 rounded-xl hover:bg-slate-800 transition"
            >
                Moj Tim
            </router-link>

            <router-link
                v-if="hasFeature('players')"
                @click="mobileMenuOpen = false"
                to="/players"
                class="block text-sm font-semibold text-slate-300 py-2 px-3 rounded-xl hover:bg-slate-800 transition"
            >
                Igrači / Registar
            </router-link>

            <router-link
                v-if="isAdmin"
                @click="mobileMenuOpen = false"
                to="/users"
                class="block text-sm font-semibold text-slate-300 py-2 px-3 rounded-xl hover:bg-slate-800 transition"
            >
                Korisnici
            </router-link>

            <router-link
                v-if="isSuperAdmin"
                @click="mobileMenuOpen = false"
                to="/super-admin"
                class="block text-sm font-bold text-amber-400 py-2 px-3 rounded-xl bg-amber-500/10 border border-amber-500/20"
            >
                Super Admin Control Panel
            </router-link>

            <button
                @click="authStore.logout"
                class="w-full text-left text-sm font-semibold text-rose-400 py-2 px-3 rounded-xl hover:bg-rose-950/30 transition mt-2 border border-rose-900/40"
            >
                Odjavi se
            </button>
        </div>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import ThemeToggle from '../components/ThemeToggle.vue'

const authStore = useAuthStore()
const mobileMenuOpen = ref(false)

// Provera Super Admin uloge
const isSuperAdmin = computed(() => {
    const user = authStore.user || JSON.parse(localStorage.getItem('user') || '{}')
    if (!user) return false
    return user.is_admin === 1 || user.is_admin === '1' || user.is_admin === true || user.roles?.some(r => r.slug === 'super-admin')
})

// Provera običnog ili klupskog admina
const isAdmin = computed(() => {
    const user = authStore.user || JSON.parse(localStorage.getItem('user') || '{}')
    if (!user) return false
    return user.is_admin || user.roles?.some(r => ['admin', 'club-admin', 'super-admin'].includes(r.slug))
})

// Dinamičko preusmeravanje za Dashboard
const dashboardRoute = computed(() => {
    return isSuperAdmin.value ? '/super-admin' : '/'
})

const canManage = computed(() => {
    return isSuperAdmin.value || ['approved', 'active'].includes(authStore.user?.subscription_status)
})

const hasFeature = (feature) => {
    return isSuperAdmin.value || authStore.user?.subscription_features?.includes(feature)
}
</script>