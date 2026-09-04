<template>
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-white">Upravljanje Korisnicima</h2>
                <p class="text-sm text-gray-400 mt-1">Pregled registrovanih korisnika i dodeljivanje uloga</p>
            </div>
        </div>

        <!-- Prikaz greške ako postoji -->
        <div v-if="userStore.error" class="mb-4 p-3 bg-red-500/20 border border-red-500 text-red-300 rounded-lg text-sm">
            {{ userStore.error }}
        </div>

        <!-- Tabela korisnika -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
            <div v-if="userStore.loading" class="p-8 text-center text-gray-400">
                Učitavanje korisnika...
            </div>

            <table v-else class="w-full text-left border-collapse">
                <thead>
                <tr class="bg-gray-900/60 border-b border-gray-700 text-gray-400 uppercase text-xs tracking-wider">
                    <th class="py-3.5 px-6 font-semibold">ID</th>
                    <th class="py-3.5 px-6 font-semibold">Ime i Prezime</th>
                    <th class="py-3.5 px-6 font-semibold">Email</th>
                    <th class="py-3.5 px-6 font-semibold">Uloge</th>
                    <th class="py-3.5 px-6 font-semibold text-right">Akcije</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50 text-sm">
                <tr v-for="user in userStore.users" :key="user.id" class="hover:bg-gray-700/30 transition">
                    <td class="py-4 px-6 text-gray-400 font-mono">#{{ user.id }}</td>
                    <td class="py-4 px-6 font-medium text-white">{{ user.name }}</td>
                    <td class="py-4 px-6 text-gray-300">{{ user.email }}</td>
                    <td class="py-4 px-6">
                        <div class="flex flex-wrap gap-1.5">
                <span
                    v-for="role in user.roles"
                    :key="role.id"
                    class="bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs px-2.5 py-0.5 rounded-full font-medium"
                >
                  {{ role.name }}
                </span>
                            <span v-if="!user.roles || user.roles.length === 0" class="text-xs text-gray-500 italic">
                  Bez uloge
                </span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <button
                            @click="openRoleModal(user)"
                            class="bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-medium px-3 py-1.5 rounded-lg border border-gray-600 transition"
                        >
                            Izmeni uloge
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal za izmenu uloga -->
        <div v-if="selectedUser" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold text-white mb-1">Izmeni uloge korisnika</h3>
                <p class="text-sm text-gray-400 mb-4">{{ selectedUser.name }} ({{ selectedUser.email }})</p>

                <div class="space-y-3 mb-6">
                    <label
                        v-for="role in userStore.roles"
                        :key="role.id"
                        class="flex items-center space-x-3 p-3 bg-gray-900 border border-gray-700 rounded-lg cursor-pointer hover:border-indigo-500 transition"
                    >
                        <input
                            type="checkbox"
                            :value="role.id"
                            v-model="selectedRoleIds"
                            class="w-4 h-4 text-indigo-600 rounded bg-gray-800 border-gray-600 focus:ring-indigo-500"
                        />
                        <span class="text-sm font-medium text-gray-200">{{ role.name }}</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700">
                    <button @click="selectedUser = null" type="button" class="px-4 py-2 text-sm text-gray-400 hover:text-white">Odustani</button>
                    <button @click="handleSaveRoles" type="button" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-500 text-white rounded font-medium">Sačuvaj</button>
                </div>
            </div>
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