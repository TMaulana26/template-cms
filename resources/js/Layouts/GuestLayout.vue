<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import DarkModeToggle from "@/Components/DarkModeToggle.vue";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 dark:bg-gray-900 pt-6 sm:justify-center sm:pt-0"
    >
        <div class="absolute top-0 right-0 p-4 flex gap-2">
            <DarkModeToggle />
            <LanguageSwitcher />
        </div>
        <div>
            <Link href="/">
                <ApplicationLogo
                    class="h-20 w-20"
                />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white dark:bg-gray-800 px-6 py-4 shadow-md border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
