<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Link, Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    status: Number,
    isAuthenticated: Boolean,
    user: Object,
});

const currentLocale = computed(() => usePage().props.locale || "en");
</script>

<template>
    <!-- Guest Layout -->
    <GuestLayout v-if="!isAuthenticated" :key="currentLocale">
        <Head :title="$t('pages.errors.Page_Not_Found')" />

        <div class="flex flex-col items-center justify-center py-12">
            <div class="text-center p-8 max-w-md">
                <div class="mb-6">
                    <h1
                        class="text-9xl font-bold text-indigo-600 dark:text-amber-500"
                    >
                        404
                    </h1>
                    <p
                        class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mt-2"
                    >
                        {{ $t("pages.errors.Page_Not_Found") }}
                    </p>
                </div>

                <div class="mb-8 text-gray-600 dark:text-gray-400">
                    <p>{{ $t("pages.errors.Page_Not_Found_Message") }}</p>
                </div>

                <PrimaryButton>
                    <Link :href="route('login')">
                        {{ $t("pages.errors.Login") }}
                    </Link>
                </PrimaryButton>
            </div>
        </div>
    </GuestLayout>

    <!-- Authenticated Layout -->
    <AuthenticatedLayout v-else>
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                {{ $t("pages.errors.Page_Not_Found") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-center">
                        <div class="mb-6">
                            <h1
                                class="text-9xl font-bold text-indigo-600 dark:text-amber-500"
                            >
                                404
                            </h1>
                        </div>

                        <div class="mb-6 text-gray-600 dark:text-gray-400">
                            <p>
                                {{ $t("pages.errors.Page_Not_Found_Message") }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
