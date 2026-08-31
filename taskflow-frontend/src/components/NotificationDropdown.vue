<template>
  <div class="relative">
    <!-- Dugiće za otvaranje notifikacija -->
    <button
      @click="isOpen = !isOpen" 
      class="relative p-2 text-gray-300 hover:text-white rounded-lg hover:bg-gray-700 transition"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
      </svg>

      <!-- Brojač nepročitanih obaveštenja -->
      <span
        v-if="notificationStore.unreadCount > 0"
        class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"
      >
        {{ notificationStore.unreadCount }}
      </span>
    </button>

    <!-- Meni sa listom notifikacija -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 bg-gray-800 border border-gray-700 rounded-xl shadow-2xl z-50 overflow-hidden"
    >
      <div class="p-3 border-b border-gray-700 flex justify-between items-center">
        <h4 class="font-bold text-sm text-white">Obaveštenja</h4>
        <span class="text-xs text-gray-400">{{ notificationStore.unreadCount }} nepročitanih</span>
      </div>

      <div class="max-h-80 overflow-y-auto divide-y divide-gray-700/50">
        <div v-if="notificationStore.notifications.length === 0" class="p-4 text-center text-xs text-gray-400">
          Nemate obaveštenja.
        </div>

        <div
          v-for="notification in notificationStore.notifications"
          :key="notification.id"
          @click="notificationStore.markAsRead(notification.id)"
          :class="['p-3 text-xs cursor-pointer hover:bg-gray-700/50 transition', !notification.read_at ? 'bg-indigo-500/10' : '']"
        >
          <p class="font-medium text-gray-200 mb-1">{{ notification.data?.message || 'Novo obaveštenje' }}</p>
          <span class="text-[10px] text-gray-500">{{ formatDate(notification.created_at) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useNotificationStore } from '../stores/notification'

const notificationStore = useNotificationStore()
const isOpen = ref(false)

onMounted(() => {
  notificationStore.fetchNotifications()
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}
</script>