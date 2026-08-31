import { defineStore } from "pinia"
import api from "../services/api"

export const useNotificationStore = defineStore("notification", {
    state: () => ({
        notifications: [],
        loading: false
    }),

    getters: {
        unreadCount: (state) => state.notifications.filter(n => !n.read_at).length
    },

    actions: {
        async fetchNotifications() {
            this.loading = true
            try {
                const response = await api.get("/notifications")
                this.notifications = response.data.data.data || response.data.data
            } catch (err) {
                console.error("Greška pri dohvatanju obaveštenja:", err)
            } finally {
                this.loading = false
            }
        },

        async markAsRead(id) {
            try {
                await api.patch(`/notifications/${id}/read`)
                const notification = this.notifications.find(n => n.id === id)
                if (notification) {
                    notification.read_at = new Date().toISOString()
                }
            } catch (err) {
                console.error("Greška pri označavanju obaveštenja:", err)
            }
        }
    }
})