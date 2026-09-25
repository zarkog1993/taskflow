<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6 text-white">
        <!-- ZAGLAVLJE -->
        <div class="bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-emerald-400">Super Admin Panel</h1>
                <p class="text-xs text-gray-400 mt-0.5">Kompletan pregled klubova, korisnika i pretplatničkih paketa</p>
            </div>
            <span class="px-3 py-1 bg-emerald-950 border border-emerald-800 text-emerald-400 text-xs font-mono font-bold rounded-xl uppercase">
                Platform Overview
            </span>
        </div>

        <AdminMetricsSummary :stats="stats" />

        <AdminTabsNav
            :active-tab="activeTab"
            :clubs-count="clubs.length"
            :users-count="users.length"
            :subscriptions-count="subscriptions.length"
            @update:active-tab="activeTab = $event"
        />

        <!-- UČITAVANJE -->
        <div v-if="loading" class="text-center py-12 text-gray-400 italic">Učitavanje admin podataka...</div>

        <div v-else>
            <ClubsTab v-if="activeTab === 'clubs'" :clubs="clubs" @delete="deleteClub" />
            <AdminUsersTab v-if="activeTab === 'users'" :users="users" @delete="deleteUser" />
            <SubscriptionsTab
                v-if="activeTab === 'subscriptions'"
                :subscriptions="subscriptions"
                :plans="plans"
                @approve="approveSubscription"
                @cancel="cancelSubscription"
                @change-plan="changeSubscriptionPlan"
            />
        </div>

        <AdminConfirmModal
            v-if="confirmation"
            :title="confirmation.title"
            :message="confirmation.message"
            :confirm-label="confirmation.confirmLabel"
            :processing-label="confirmation.processingLabel"
            :icon="confirmation.icon"
            :processing="confirmationProcessing"
            :error="confirmationError"
            @close="closeConfirmation"
            @confirm="confirmAction"
        />
    </div>
</template>

<script setup>
import { useSuperAdminDashboard } from './composables/useSuperAdminDashboard'
import AdminMetricsSummary from './components/AdminMetricsSummary.vue'
import AdminTabsNav from './components/AdminTabsNav.vue'
import ClubsTab from './components/ClubsTab.vue'
import AdminUsersTab from './components/AdminUsersTab.vue'
import SubscriptionsTab from './components/SubscriptionsTab.vue'
import AdminConfirmModal from './components/AdminConfirmModal.vue'

const {
    activeTab,
    loading,
    stats,
    clubs,
    users,
    subscriptions,
    plans,
    confirmation,
    confirmationProcessing,
    confirmationError,
    approveSubscription,
    deleteUser,
    deleteClub,
    cancelSubscription,
    closeConfirmation,
    confirmAction,
    changeSubscriptionPlan
} = useSuperAdminDashboard()
</script>
