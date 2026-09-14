<template>
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-white">Upravljanje Korisnicima i Adminima</h2>
                <p class="text-sm text-gray-400 mt-1">Pregled registrovanih naloga, uloga i dodeljenih ekipa</p>
            </div>
        </div>

        <!-- Tabela Korisnika -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr class="bg-gray-900/60 border-b border-gray-700 text-gray-400 uppercase text-xs">
                    <th class="py-3.5 px-6">ID</th>
                    <th class="py-3.5 px-6">Ime i Prezime</th>
                    <th class="py-3.5 px-6">Email</th>
                    <th class="py-3.5 px-6">Uloge</th>
                    <th class="py-3.5 px-6">Dodeljene Ekipe</th>
                    <th class="py-3.5 px-6 text-right">Akcije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50 text-sm">
                <tr v-for="user in userStore.users" :key="user.id" class="hover:bg-gray-700/30 transition">
                    <td class="py-4 px-6 text-gray-400 font-mono text-xs">
                        #{{ user.id }}
                    </td>
                    <td class="py-4 px-6 font-semibold text-white">
                        {{ user.name }}
                    </td>
                    <td class="py-4 px-6 text-gray-300 text-xs">
                        {{ user.email }}
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex flex-wrap gap-1">
                <span
                    v-for="role in user.roles"
                    :key="role.id"
                    :class="[
                    'text-[10px] font-bold uppercase px-2 py-0.5 rounded-full border',
                    role.slug === 'admin' ? 'bg-red-500/20 text-red-400 border-red-500/30' :
                    role.slug === 'team_admin' ? 'bg-purple-500/20 text-purple-400 border-purple-500/30' :
                    role.slug === 'head_coach' ? 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' :
                    'bg-blue-500/20 text-blue-400 border-blue-500/30'
                  ]"
                >
                  {{ role.name }}
                </span>
                            <span v-if="!user.roles || user.roles.length === 0" class="text-xs text-gray-500 italic">
                  Bez uloge
                </span>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <!-- Prikaz Timova kojima je korisnik dodeljen / koje vodi -->
                        <div class="flex flex-wrap gap-1.5">
                <span
                    v-for="team in user.teams"
                    :key="team.id"
                    class="bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 text-xs px-2 py-0.5 rounded font-medium"
                >
                  {{ team.name }}
                </span>
                            <span v-if="!user.teams || user.teams.length === 0" class="text-xs text-gray-500 italic">
                  Nije dodeljen ekipi
                </span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <button
                            @click="openRoleModal(user)"
                            class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded text-xs font-semibold transition"
                        >
                            Izmeni Uloge
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUserStore } from '../stores/user'

const userStore = useUserStore()
const selectedUser = ref(null)
const selectedRoleIds = ref([])

onMounted(() => {
    userStore.fetchUsers()
    userStore.fetchRoles()
})

const openRoleModal = (user) => {
    selectedUser.value = user
    selectedRoleIds.value = user.roles ? user.roles.map(r => r.id) : []
}

const handleSaveRoles = async () => {
    if (!selectedUser.value) return
    const success = await userStore.updateUserRoles(selectedUser.value.id, selectedRoleIds.value)
    if (success) {
        selectedUser.value = null
    }
}
</script>