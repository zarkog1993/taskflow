import { defineStore } from 'pinia'
import api from '../services/api'

export const useTaskStore = defineStore('task', {
    state: () => ({
        tasks: [],
        loading: false,
        error: null
    }),

    getters: {
        todoTasks: (state) => Array.isArray(state.tasks) ? state.tasks.filter(t => t.status === 'todo') : [],
        inProgressTasks: (state) => Array.isArray(state.tasks) ? state.tasks.filter(t => t.status === 'in_progress') : [],
        doneTasks: (state) => Array.isArray(state.tasks) ? state.tasks.filter(t => t.status === 'done') : [],
    },

    actions: {
        async fetchTasks() {
            this.loading = true
            this.error = null
            try {
                const response = await api.get('/tasks')

                // Rukovanje sa Laravel LengthAwarePaginator strukturom
                if (response.data.data?.data) {
                    this.tasks = response.data.data.data
                } else if (Array.isArray(response.data.data)) {
                    this.tasks = response.data.data
                } else if (Array.isArray(response.data)) {
                    this.tasks = response.data
                } else {
                    this.tasks = []
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri učitavanju zadataka.'
            } finally {
                this.loading = false
            }
        },

        async createTask(taskData) {
            this.error = null
            try {
                const response = await api.post('/tasks', {
                    title: taskData.title,
                    description: taskData.description,
                    status: taskData.status,
                    priority: taskData.priority,
                    assigned_to: taskData.assigned_user_id // Prilagodi polje sa imenom u bazi
                })

                const createdTask = response.data.data || response.data
                this.tasks.unshift(createdTask)
                return true
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri kreiranju zadatka.'
                return false
            }
        },

        async updateTaskStatus(taskId, newStatus) {
            const task = this.tasks.find(t => t.id === taskId)
            if (!task) return

            const oldStatus = task.status
            task.status = newStatus

            try {
                await api.put(`/tasks/${taskId}`, {
                    title: task.title,
                    description: task.description,
                    status: newStatus,
                    priority: task.priority,
                    assigned_to: task.assigned_to || task.assigned_user_id || task.assigned_user?.id
                })
            } catch (err) {
                task.status = oldStatus
                this.error = err.response?.data?.message || 'Greška pri izmeni statusa.'
            }
        }
    }
})