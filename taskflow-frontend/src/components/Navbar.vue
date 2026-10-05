<template>
    <header class="bg-white/95 dark:bg-slate-900/95 border-b border-slate-200 dark:border-slate-800 backdrop-blur sticky top-0 z-50 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-2 sm:gap-4">

            <!-- LEVA STRANA: Logo & Glavna Navigacija -->
            <div class="flex items-center gap-6 min-w-0">
                <router-link :to="dashboardRoute" class="flex items-center gap-2 shrink-0">
          <span class="text-xl font-extrabold tracking-tight text-emerald-700 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition">
            ProTrainer
          </span>
                </router-link>

                <!-- Desktop Navigacija -->
                <nav v-if="authStore.isAuthenticated && canManage" class="hidden lg:flex items-center gap-1.5 pl-6 border-l border-slate-200 dark:border-slate-800 overflow-x-auto no-scrollbar">
                    <router-link
                        :to="dashboardRoute"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === dashboardRoute || $route.path === '/dashboard' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Početna Stranica
                    </router-link>

                    <router-link
                        v-if="hasFeature('matches') && !isSuperAdmin"
                        to="/matches"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/matches' || $route.path.startsWith('/matches/') ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Utakmice
                    </router-link>

                    <router-link
                        v-if="hasFeature('teams') && !isSuperAdmin"
                        to="/trainings"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/trainings' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Trening
                    </router-link>

                    <router-link
                        v-if="hasFeature('tactics') && !isSuperAdmin"
                        to="/tactics"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/tactics' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Taktika
                    </router-link>

                    <router-link
                        v-if="hasFeature('advanced_stats') && !isSuperAdmin"
                        to="/analytics"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/analytics' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Analitika
                    </router-link>

                    <router-link
                        v-if="hasFeature('teams') && !isSuperAdmin"
                        to="/teams"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/teams' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Moj Tim
                    </router-link>

                    <router-link
                        v-if="hasFeature('players')"
                        to="/players"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/players' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Igrači
                    </router-link>

                    <router-link
                        to="/finances"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/finances' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Finansije
                    </router-link>

                    <router-link
                        v-if="isSuperAdmin"
                        to="/users"
                        class="text-xs font-semibold px-3 py-2 rounded-xl transition shrink-0"
                        :class="$route.path === '/users' ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 hover:bg-slate-100/60 dark:hover:bg-slate-800/60'"
                    >
                        Korisnici
                    </router-link>
                </nav>
            </div>

            <!-- DESNA STRANA: Profil, Super Admin oznaka, Kalendar, Logout -->
            <div class="flex shrink-0 items-center gap-2 sm:gap-3">

                <!-- Kalendar dugme -->
                <router-link
                    v-if="authStore.isAuthenticated"
                    to="/calendar"
                    class="text-xs font-semibold px-3 py-2 rounded-xl transition flex items-center gap-1.5"
                    :class="$route.path === '/calendar' ? 'bg-emerald-500 text-slate-950 font-bold' : 'bg-slate-100/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60'"
                >
                    <span>📅</span>
                    <span class="hidden sm:inline">Kalendar</span>
                </router-link>

                <!-- Super Admin Brza Oznaka -->
                <router-link
                    v-if="isSuperAdmin && authStore.isAuthenticated"
                    to="/super-admin"
                    class="hidden sm:flex text-[11px] font-bold px-2.5 py-1.5 rounded-lg bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 hover:bg-amber-500/20 transition items-center gap-1.5"
                >
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>Super Admin</span>
                </router-link>

                <ThemeToggle />

                <!-- Korisnički profil & Odjava -->
                <template v-if="authStore.isAuthenticated">
                    <div class="hidden sm:flex flex-col text-right pl-2 border-l border-slate-200 dark:border-slate-800">
            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 truncate max-w-[120px]">
              {{ authStore.user?.name }}
            </span>
                        <span class="text-[10px] text-slate-600 dark:text-slate-400 truncate max-w-[120px]">
              {{ authStore.user?.email }}
            </span>
                    </div>

                    <button
                        @click="authStore.logout"
                        class="text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition hidden lg:block"
                        title="Odjava"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>

                    <!-- Mobilni meni dugme -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        :aria-expanded="mobileMenuOpen"
                        aria-controls="mobile-navigation"
                        :aria-label="mobileMenuOpen ? 'Zatvori meni' : 'Otvori meni'"
                        class="lg:hidden text-slate-700 dark:text-slate-300 p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="mobileMenuOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"></path>
                        </svg>
                    </button>
                </template>

                <template v-else>
                    <router-link to="/login" class="text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 transition">
                        Prijava
                    </router-link>
                    <router-link to="/register" class="text-xs font-bold bg-emerald-400 hover:bg-emerald-300 text-slate-950 px-3.5 py-1.5 rounded-xl transition">
                        Registracija
                    </router-link>
                </template>
            </div>
        </div>

        <!-- Mobilni Padajući Meni -->
        <nav
            v-if="mobileMenuOpen && authStore.isAuthenticated"
            id="mobile-navigation"
            aria-label="Glavna navigacija"
            class="lg:hidden max-h-[calc(100dvh-4rem)] overflow-y-auto bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 pt-3 pb-5 shadow-2xl"
        >
            <div class="pb-3 border-b border-slate-200 dark:border-slate-800 mb-3">
                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ authStore.user?.name }}</div>
                <div class="text-xs text-slate-600 dark:text-slate-400 break-all">{{ authStore.user?.email }}</div>
            </div>

            <div v-if="canManage" class="grid grid-cols-2 gap-2">
                <router-link
                    :to="dashboardRoute"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="isActive(dashboardRoute) || $route.path === '/' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Početna Stranica
                </router-link>
                <router-link
                    v-if="hasFeature('matches') && !isSuperAdmin"
                    to="/matches"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path.startsWith('/matches') ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Utakmice
                </router-link>
                <router-link
                    v-if="hasFeature('teams') && !isSuperAdmin"
                    to="/trainings"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path === '/trainings' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Treninzi
                </router-link>
                <router-link
                    v-if="hasFeature('tactics') && !isSuperAdmin"
                    to="/tactics"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path === '/tactics' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Taktika
                </router-link>
                <router-link
                    v-if="hasFeature('advanced_stats') && !isSuperAdmin"
                    to="/analytics"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path === '/analytics' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Analitika
                </router-link>
                <router-link
                    v-if="hasFeature('teams') && !isSuperAdmin"
                    to="/teams"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path.startsWith('/teams') ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Moj Tim
                </router-link>
                <router-link
                    v-if="hasFeature('players')"
                    to="/players"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path.startsWith('/players') ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Igrači
                </router-link>
                <router-link
                    to="/finances"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path === '/finances' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Finansije
                </router-link>
                <router-link
                    to="/calendar"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path === '/calendar' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    📅 Kalendar
                </router-link>
                <router-link
                    v-if="isSuperAdmin"
                    to="/users"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                    :class="$route.path === '/users' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Korisnici
                </router-link>
                <router-link
                    v-if="isSuperAdmin"
                    to="/super-admin"
                    @click="mobileMenuOpen = false"
                    class="flex min-h-[44px] items-center rounded-xl border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-sm font-semibold text-amber-700 transition dark:text-amber-300"
                    :class="$route.path === '/super-admin' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
                >
                    Super Admin
                </router-link>
            </div>
            <router-link
                v-else
                to="/calendar"
                @click="mobileMenuOpen = false"
                class="flex min-h-[44px] items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-200 dark:hover:bg-slate-800"
                :class="$route.path === '/calendar' ? 'border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : ''"
            >
                📅 Kalendar
            </router-link>

            <button
                type="button"
                @click="authStore.logout"
                class="mt-3 w-full min-h-[44px] text-left text-sm font-semibold text-rose-600 dark:text-rose-400 py-2 px-3 rounded-xl hover:bg-rose-50/30 dark:hover:bg-rose-950/30 transition border border-rose-200/40 dark:border-rose-900/40"
            >
                Odjavi se
            </button>
        </nav>
    </header>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import ThemeToggle from '../components/ThemeToggle.vue'

const authStore = useAuthStore()
const route = useRoute()
const mobileMenuOpen = ref(false)
const isActive = (path) => route.path === path || (path !== '/' && route.path.startsWith(`${path}/`))

watch(() => route.path, () => {
    mobileMenuOpen.value = false
})

// Provera Super Admin uloge
const isSuperAdmin = computed(() => {
    const user = authStore.user || JSON.parse(localStorage.getItem('user') || '{}')
    if (!user) return false
    return user.is_admin === 1 || user.is_admin === '1' || user.is_admin === true || user.roles?.some(r => r.slug === 'super-admin')
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
