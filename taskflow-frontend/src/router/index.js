import { createRouter, createWebHistory } from "vue-router"
import RegisterView from "../views/RegisterView.vue"
import TeamDetailView from "../views/TeamDetailView.vue"
import LoginView from "../views/LoginView.vue"
import DashboardView from "../views/DashboardView.vue"
import UsersView from "../views/UsersView.vue"
import Players from "../views/PlayersView.vue"
import TrainingsView from "../views/TrainingsView.vue"
import TeamsView from "../views/TeamsView.vue"

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
        component: () => import('../views/PlayerProfileView.vue'),
        meta: { requiresAuth: true }
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
