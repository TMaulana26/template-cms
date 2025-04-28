<script setup>
import { ref, onMounted, watch } from "vue";
import { SunIcon, MoonIcon } from "@heroicons/vue/24/outline";

const isDark = ref(false);

// Initialize dark mode state based on localStorage or system preference
onMounted(() => {
    // Check localStorage first
    if (localStorage.getItem("darkMode") === "dark") {
        isDark.value = true;
        document.documentElement.classList.add("dark");
    } else if (localStorage.getItem("darkMode") === "light") {
        isDark.value = false;
        document.documentElement.classList.remove("dark");
    } else {
        // If no localStorage value, check system preference
        const prefersDark = window.matchMedia(
            "(prefers-color-scheme: dark)"
        ).matches;
        isDark.value = prefersDark;
        if (prefersDark) document.documentElement.classList.add("dark");
    }
});

// Toggle dark mode
const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    localStorage.setItem("darkMode", isDark.value ? "dark" : "light");
};

// Watch for changes to apply/remove the dark class
watch(isDark, (newValue) => {
    if (newValue) {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
});
</script>

<template>
    <button
        @click="toggleDarkMode"
        class="rounded-md p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:text-gray-400 dark:hover:text-amber-400 dark:hover:bg-gray-700 dark:focus:ring-amber-500/20"
        aria-label="Toggle dark mode"
    >
        <SunIcon v-if="isDark" class="h-5 w-5" />
        <MoonIcon v-else class="h-5 w-5" />
    </button>
</template>
