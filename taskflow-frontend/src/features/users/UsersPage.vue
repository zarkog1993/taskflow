<template>
    <div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-6">
        <!-- ZAGLAVLJE -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-800/80 p-5 rounded-2xl border border-gray-700/80 shadow-xl">
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Upravljanje Korisnicima</h1>
                <p class="text-xs text-gray-400 mt-0.5">Pregled svih korisnika u sistemu i dodeljivanje novih članova klubovima</p>
            </div>

            <button
                @click="showCreateModal = true"
                class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 border border-indigo-500/30 cursor-pointer"
            >
                <span>👤</span> DODAJ KORISNIKA
            </button>
        </div>

        <UsersTable :users="users" :loading="loading" @delete="deleteUser" />

        <CreateUserModal
            v-if="showCreateModal"
            :form="newUser"
            :clubs="clubs"
            :is-submitting="isSubmitting"
            @close="closeModal"
            @submit="handleCreateUser"
        />
    </div>
</template>

<script setup>
import { useUsersPage } from './composables/useUsersPage'
import UsersTable from './components/UsersTable.vue'
import CreateUserModal from './components/CreateUserModal.vue'

const {
    users,
    clubs,
    loading,
    showCreateModal,
    isSubmitting,
    newUser,
    handleCreateUser,
    deleteUser,
    closeModal
} = useUsersPage()
</script>
