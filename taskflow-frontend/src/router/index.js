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
        path: "/",
        name: "dashboard",
        component: DashboardView,
        meta: { requiresAuth: true }
    },
    {
        path: '/users',
        name: 'users',
        component: UsersView,
        meta: { requiresAuth: true }
    },
    {
        path: '/players',
        name: 'players',
        component: Players,
        meta: { requiresAuth: true }
    },
    {
        path: '/trainings',
        name: 'trainings',
        component: TrainingsView,
        meta: { requiresAuth: true }
    },
    {
        path: '/teams',
        name: 'teams',
        component: TeamsView,
        meta: { requiresAuth: true }
    },
    {
        path: '/teams/:id',
        name: 'team-detail',
        component: TeamDetailView,
        meta: { requiresAuth: true }
    },
    {
        path: '/players/:id',
        name: 'player-profile',
        component: PlayerProfileView,
        meta: { requiresAuth: true }
    },
    {
        path: '/matches',
        name: 'matches',
        component: MatchesView,
        meta: { requiresAuth: true }
    },
    {
        path: '/calendar',
        name: 'calendar',
        component: CalendarView,
        meta: { requiresAuth: true }
    },
    {
        path: '/rsvp-confirmation',
        name: 'rsvp-confirmation',
        component: RsvpConfirmationView
    },
    {
        path: '/tactics',
        name: 'tactics',
        component: TacticsView
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Navigation Guard
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("token")

    if (to.meta.requiresAuth && !token) {
        next({ name: "login" })
    } else if (to.meta.guestOnly && token) {
        next({ name: "dashboard" })
    } else {
        next()
    }
})

export default router
