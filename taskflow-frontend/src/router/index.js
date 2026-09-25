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
import RsvpConfirmationView from "../views/RsvpConfirmationView.vue";
import TacticsView from "../views/TacticsView.vue";
import Onboarding from "../views/Onboarding.vue";
import SuperAdminView from "../views/SuperAdminView.vue";
import SubscriptionPendingView from "../views/SubscriptionPendingView.vue";

const routes = [
    {
        path: "/login",
        name: "login",
        component: LoginView,
        meta: { guestOnly: true }
    },
    {
        path: '/subscription-pending',
        name: 'subscription-pending',
        component: SubscriptionPendingView,
        meta: { requiresAuth: true, allowPendingSubscription: true }
    },
    {
        path: "/register",
        name: "register",
        component: RegisterView,
        meta: { guestOnly: true }
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
        meta: { requiresAuth: true, subscriptionRequired: true }
    },
    {
        path: '/onboarding',
        name: 'onboarding',
        component: Onboarding,
        meta: { requiresAuth: true, subscriptionRequired: true }
    },
    {
        path: '/onboarding/:token',
        name: 'onboarding-token',
        component: Onboarding,
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

    if (to.meta.requiresAuth && !token) {
        return next({ name: 'login' })
    }

    if (to.meta.guestOnly && token) {
        return next(isSuperAdmin(user)
            ? '/super-admin'
            : user.subscription_status === 'approved' || user.subscription_status === 'active'
                ? { name: 'dashboard' }
                : { name: 'subscription-pending' })
    }

    if (to.meta.subscriptionRequired
        && !isSuperAdmin(user)
        && !['approved', 'active'].includes(user.subscription_status)) {
        return next({ name: 'subscription-pending' })
    }

    if (to.meta.feature
        && !isSuperAdmin(user)
        && !user.subscription_features?.includes(to.meta.feature)) {
        return next({ name: 'dashboard' })
    }

    if ((to.name === 'dashboard' || to.path === '/dashboard') && isSuperAdmin(user)) {
        return next('/super-admin')
    }

    next()
})

export default router
