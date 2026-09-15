<template>
    <div class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 antialiased font-sans flex flex-col transition-colors duration-200">
        <Navbar />
        <div class="flex-1">
            <router-view />
        </div>
    </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
</script>