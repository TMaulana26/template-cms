<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
</script>

<template>
    <Head :title="$t('pages.dashboard.Dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                {{ $t("pages.dashboard.Dashboard") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-200">
                        {{ $t("pages.dashboard.You_are_logged_in") }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
