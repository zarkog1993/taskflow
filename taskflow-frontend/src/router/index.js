import { createRouter, createWebHistory } from "vue-router"
import RegisterView from "../views/RegisterView.vue"
import TeamDetailView from "../views/TeamDetailView.vue"
import LoginView from "../views/LoginView.vue"
import DashboardView from "../views/DashboardView.vue"
import UsersView from "../views/UsersView.vue"
import Players from "../views/PlayersView.vue"
import TrainingsView from "../views/TrainingsView.vue"
import TeamsView from "../views/TeamsView.vue"
import PlayerProfileView from "../views/PlayerProfileView.vue"
import MatchesView from "../views/MatchesView.vue"
import CalendarView from "../views/CalendarView.vue"
import RsvpConfirmationView from "../views/RsvpConfirmationView.vue"
import TacticsView from "../views/TacticsView.vue"
import Onboarding from "../views/Onboarding.vue"
import SuperAdminView from "../views/SuperAdminView.vue"
import SubscriptionPendingView from "../views/SubscriptionPendingView.vue"
import AnalyticsView from "../views/AnalyticsView.vue";

const routes = [
    {
        path: "/login",
        name: "login",
        component: LoginView,
        meta: { guestOnly: true }
    },
    {
        path: "/register",
        name: "register",
        component: RegisterView,
        meta: { guestOnly: true }
    },
    {
        path: '/subscription-pending',
        name: 'subscription-pending',
        component: SubscriptionPendingView,
        meta: { requiresAuth: true, allowPendingSubscription: true }
    },
    {
        path: '/',
        name: 'dashboard',
        component: DashboardView,
        meta: { requiresAuth: true, subscriptionRequired: true }
    },
    {
        path: '/super-admin',
        name: 'super-admin',
        component: SuperAdminView,
        meta: { requiresAuth: true }
    },
    {
        path: '/users',
        name: 'users',
        component: UsersView,
        meta: { requiresAuth: true, subscriptionRequired: true, feature: 'advanced.management' }
    },
    {
        path: '/players',
        name: 'players',
        component: Players,
        meta: { requiresAuth: true, subscriptionRequired: true, feature: 'players' }
    },
    {
        path: '/trainings',
        name: 'trainings',
        component: TrainingsView,
        meta: { requiresAuth: true, subscriptionRequired: true, feature: 'teams' }
    },
    {
        path: '/teams',
        name: 'teams',
        component: TeamsView,
        meta: { requiresAuth: true, subscriptionRequired: true, feature: 'teams' }
    },
    {
        path: '/teams/:id',
        name: 'team-detail',
        component: TeamDetailView,
        meta: { requiresAuth: true, subscriptionRequired: true, feature: 'teams' }
    },
    {
        path: '/players/:id',
        name: 'player-profile',
        component: PlayerProfileView,
        meta: { requiresAuth: true, subscriptionRequired: true, feature: 'players' }
    },
    {
        path: '/matches',
        name: 'matches',
        component: MatchesView,
        meta: { requiresAuth: true, subscriptionRequired: true, feature: 'matches' }
    },
    {
        path: '/calendar',
        name: 'calendar',
        component: CalendarView,
        meta: { requiresAuth: true, subscriptionRequired: true }
    },
    {
        path: '/rsvp-confirmation',
        name: 'rsvp-confirmation',
        component: RsvpConfirmationView
    },
    {
        path: '/tactics',
        name: 'tactics',
        component: TacticsView,
        meta: { requiresAuth: true, subscriptionRequired: true, feature: 'tactics' }
    },

    // ONBOARDING RUTE (ISPRAVLJENO: Ne zahtevaju aktivnu pretplatu)
    {
        path: '/onboarding',
        name: 'onboarding',
        component: Onboarding,
        meta: { allowPendingSubscription: true, publicTokenAccess: true }
    },
    {
        path: '/onboarding/:token',
        name: 'onboarding-token',
        component: Onboarding,
        meta: { allowPendingSubscription: true, publicTokenAccess: true }
    },
    {
        path: '/analytics',
        name: 'analytics',
        component: AnalyticsView,
        meta: {
            requiresAuth: true,
            subscriptionRequired: true,
            feature: 'advanced_stats'
        }
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

const isSuperAdmin = (user) => {
    if (!user) return false

    return user.is_admin === 1
        || user.is_admin === '1'
        || user.is_admin === true
        || user.roles?.some(role => role.slug === 'super-admin' || role.name === 'Super Admin')
}

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    const user = JSON.parse(localStorage.getItem('user') || '{}')

    // AKO JE ONBOARDING SA TOKENOM IZ EMAILA — DOZVOLI PRISTUP BEZ AUTENTIFIKACIJE
    if ((to.name === 'onboarding' || to.name === 'onboarding-token') && (to.query.token || to.params.token)) {
        return next()
    }

    // 1. Provera autentifikacije za ostale zaštićene rute
    if (to.meta.requiresAuth && !token) {
        return next({ name: 'login' })
    }

    // 2. Preusmeravanje za ulogovane korisnike ako posete login ili register
    if (to.meta.guestOnly && token) {
        if (isSuperAdmin(user)) {
            return next('/super-admin')
        }

        return ['approved', 'active'].includes(user.subscription_status)
            ? next({ name: 'dashboard' })
            : next({ name: 'subscription-pending' })
    }

    // 3. Automatsko preusmeravanje Super Admina sa početne strane
    if ((to.name === 'dashboard' || to.path === '/') && isSuperAdmin(user)) {
        return next('/super-admin')
    }

    // 4. Provera da li nalog čeka odobrenje pretplate
    if (to.meta.subscriptionRequired
        && !isSuperAdmin(user)
        && !['approved', 'active'].includes(user.subscription_status)) {
        return next({ name: 'subscription-pending' })
    }

    // 5. Provera feature flag-ova za odabrani paket
    if (to.meta.feature
        && !isSuperAdmin(user)
        && !user.subscription_features?.includes(to.meta.feature)) {
        return next({ name: 'dashboard' })
    }

    next()
})

export default router