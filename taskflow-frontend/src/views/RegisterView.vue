<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white p-4 transition-colors duration-200 relative"
    >
        <div class="absolute top-4 right-4">
            <ThemeToggle />
        </div>

        <div
            class="max-w-md w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700"
        >
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">
                    TaskFlow
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2 text-sm">Kreirajte vaš nalog</p>
            </div>

            <div
                v-if="error"
                class="mb-4 p-3 bg-red-50 dark:bg-red-500/20 border border-red-200 dark:border-red-500 text-red-600 dark:text-red-300 rounded-xl text-sm"
            >
                {{ error }}
            </div>

            <form @submit.prevent="handleRegister" class="space-y-4">
                <div>
                    <label
                        class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1"
                    >Ime i Prezime</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="Petar Petrović"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900 dark:text-white outline-none transition text-sm"
                    />
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1"
                    >Email Adresa</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        placeholder="ime@primer.com"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900 dark:text-white outline-none transition text-sm"
                    />
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1"
                    >Lozinka</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900 dark:text-white outline-none transition text-sm"
                    />
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1"
                    >Potvrda Lozinke</label
                    >
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-gray-900 dark:text-white outline-none transition text-sm"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-semibold rounded-xl shadow-lg hover:shadow-indigo-500/30 transition duration-200 mt-2 cursor-pointer text-sm"
                >
                    <span v-if="loading">Registracija u toku...</span>
                    <span v-else>Registruj se</span>
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                Već imate nalog?
                <router-link
                    to="/login"
                    class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium ml-1"
                >Prijavite se</router-link
                >
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import api from "../services/api";
import { useAuthStore } from "../stores/auth";
import ThemeToggle from "../components/ThemeToggle.vue";

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(false);
const error = ref(null);

const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const handleRegister = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await api.post("/register", form);
        authStore.setAuth(response.data.data.user, response.data.data.token);
        router.push({ name: "dashboard" });
    } catch (err) {
        if (err.response && err.response.data.errors) {
            error.value = Object.values(err.response.data.errors).flat().join(", ");
        } else if (err.response && err.response.data.message) {
            error.value = err.response.data.message;
        } else {
            error.value = "Došlo je do greške prilikom registracije.";
        }
    } finally {
        loading.value = false;
    }
};
</script>
