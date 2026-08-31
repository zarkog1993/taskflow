import { createRouter, createWebHistory } from "vue-router"
import LoginView from "../views/LoginView.vue"
import RegisterView from "../views/RegisterView.vue"
import DashboardView from "../views/DashboardView.vue"

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
    }
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
