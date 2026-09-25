<template>
    <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-50">
        <div class="bg-gray-800 border border-gray-700/80 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5">
            <div class="flex justify-between items-center border-b border-gray-700 pb-3">
                <h3 class="text-lg font-black text-white">Novi Korisnik</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-white font-bold">✕</button>
            </div>

            <form @submit.prevent="$emit('submit')" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Ime i Prezime *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="npr. Petar Petrović"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                    />
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Email Adresa *</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        placeholder="petar@example.com"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                    />
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Lozinka *</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        placeholder="••••••••"
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                    />
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Dodeljeni Klub *</label>
                    <select
                        v-model="form.club_id"
                        required
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer"
                    >
                        <option :value="null" disabled>Izaberite klub...</option>
                        <option v-for="club in clubs" :key="club.id" :value="club.id">
                            {{ club.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Uloga Korisnika *</label>
                    <select
                        v-model="form.role"
                        required
                        class="w-full bg-gray-900 border border-gray-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500 cursor-pointer"
                    >
                        <option value="player">Igrač</option>
                        <option value="club-admin">Klupski Admin</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                    <button type="button" @click="$emit('close')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">
                        Odustani
                    </button>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold px-5 py-2 rounded-xl transition shadow-lg cursor-pointer disabled:opacity-50"
                    >
                        {{ isSubmitting ? 'Sačuvavanje...' : 'Kreiraj Korisnika' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
defineProps({
    form: {
        type: Object,
        required: true
    },
    clubs: {
        type: Array,
        default: () => []
    },
    isSubmitting: {
        type: Boolean,
        default: false
    }
})

defineEmits(['close', 'submit'])
</script>
