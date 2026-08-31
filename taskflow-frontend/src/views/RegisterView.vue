<template>
  <div
    class="min-h-screen flex items-center justify-center bg-gray-900 text-white p-4"
  >
    <div
      class="max-w-md w-full bg-gray-800 rounded-xl shadow-2xl p-8 border border-gray-700"
    >
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-indigo-400">
          TaskFlow
        </h1>
        <p class="text-gray-400 mt-2 text-sm">Kreirajte vaš nalog</p>
      </div>

      <div
        v-if="error"
        class="mb-4 p-3 bg-red-500/20 border border-red-500 text-red-300 rounded-lg text-sm"
      >
        {{ error }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label
            class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1"
            >Ime i Prezime</label
          >
          <input
            v-model="form.name"
            type="text"
            required
            placeholder="Petar Petrović"
            class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white outline-none transition"
          />
        </div>

        <div>
          <label
            class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1"
            >Email Adresa</label
          >
          <input
            v-model="form.email"
            type="email"
            required
            placeholder="ime@primer.com"
            class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white outline-none transition"
          />
        </div>

        <div>
          <label
            class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1"
            >Lozinka</label
          >
          <input
            v-model="form.password"
            type="password"
            required
            placeholder="••••••••"
            class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white outline-none transition"
          />
        </div>

        <div>
          <label
            class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1"
            >Potvrda Lozinke</label
          >
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            placeholder="••••••••"
            class="w-full px-4 py-2.5 bg-gray-900 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white outline-none transition"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-semibold rounded-lg shadow-lg hover:shadow-indigo-500/30 transition duration-200 mt-2"
        >
          <span v-if="loading">Registracija u toku...</span>
          <span v-else>Registruj se</span>
        </button>
      </form>

      <div class="mt-6 text-center text-sm text-gray-400">
        Već imate nalog?
        <router-link
          to="/login"
          class="text-indigo-400 hover:underline font-medium"
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

    // Čuvamo token i podatke o korisniku
    authStore.token = response.data.token;
    authStore.user = response.data.user;
    localStorage.setItem("token", response.data.token);
    localStorage.setItem("user", JSON.stringify(response.data.user));

    router.push({ name: "dashboard" });
  } catch (err) {
    error.value = err.response?.data?.message || "Greška pri registraciji.";
  } finally {
    loading.value = false;
  }
};
</script>
