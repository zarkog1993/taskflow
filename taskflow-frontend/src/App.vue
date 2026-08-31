<template>
  <div class="min-h-screen bg-gray-900 text-gray-100 antialiased font-sans flex flex-col">
    <!-- Globalni Navbar -->
    <Navbar />

    <!-- Sadržaj aktivne rute -->
    <div class="flex-1">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
</script>