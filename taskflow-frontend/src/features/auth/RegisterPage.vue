<template>
    <div class="min-h-screen bg-slate-900 text-white flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-8">
            <h2 class="text-3xl font-extrabold text-indigo-400">TaskFlow Sport</h2>
            <p class="mt-2 text-sm text-slate-400">Registrujte vaš klub i započnite upravljanje</p>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-slate-800 py-8 px-6 shadow-xl rounded-2xl border border-slate-700/80 space-y-6">
                <form @submit.prevent="handleRegister" class="space-y-4">
                    <!-- Ime i prezime -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Ime i Prezime *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Petar Petrović"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <!-- Naziv Kluba -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Naziv Kluba / Akademije *</label>
                        <input
                            v-model="form.club_name"
                            type="text"
                            required
                            placeholder="npr. FK Rudar Vrdnik"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Grad</label>
                            <input v-model="form.city" type="text" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Telefon</label>
                            <input v-model="form.phone" type="tel" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500" />
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Email Adresa *</label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            placeholder="admin@rudar.com"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <!-- Lozinka -->
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Lozinka *</label>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            placeholder="••••••••"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Potvrda lozinke *</label>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white outline-none focus:border-indigo-500"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-3 rounded-xl transition shadow-lg disabled:opacity-50 cursor-pointer"
                    >
                        {{ loading ? 'Registracija u toku...' : 'Registruj klub i pošalji onboarding link' }}
                    </button>
                </form>

                <div v-if="successMessage" class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-xl">
                    {{ successMessage }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRegisterForm } from './composables/useRegisterForm'

const { form, loading, successMessage, handleRegister } = useRegisterForm()
</script>
