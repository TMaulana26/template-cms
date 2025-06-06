<script setup>
import { computed, ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import DarkModeToggle from "@/Components/DarkModeToggle.vue";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";
import LanguageSwitcher from "./LanguageSwitcher.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const navlinks = computed(() => page.props.menu.navlinks);

const showingNavigationDropdown = ref(false);
</script>

<template>
    <nav
        class="border-b border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900"
    >
        <!-- Primary Navigation Menu -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex shrink-0 items-center">
                        <Link :href="route('dashboard')">
                            <ApplicationLogo
                                class="block h-10 w-auto fill-current text-gray-800 dark:text-gray-200"
                            />
                        </Link>
                    </div>

                    <!-- Navigation Links -->
                    <div
                        class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                        v-for="navlink in navlinks"
                        :key="navlink.id"
                    >
                        <NavLink
                            :href="route(navlink.route)"
                            :active="route().current(navlink.route)"
                        >
                            {{ navlink.title }}
                        </NavLink>
                    </div>
                </div>

                <div class="hidden sm:ms-6 sm:flex sm:items-center">
                    <language-switcher class="mr-3" />
                    <DarkModeToggle />
                    <!-- Settings Dropdown -->
                    <div class="relative ms-3">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-md border border-transparent dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium leading-4 text-gray-500 dark:text-gray-400 transition duration-150 ease-in-out hover:text-indigo-600 dark:hover:text-amber-400 focus:outline-none"
                                    >
                                        {{ user.name }}
                                        <ChevronDownIcon
                                            class="h-4 w-4 ml-2 text-indigo-600 dark:text-amber-500"
                                        />
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile
                                </DropdownLink>
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button
                        @click="
                            showingNavigationDropdown =
                                !showingNavigationDropdown
                        "
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 dark:text-gray-500 transition duration-150 ease-in-out hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-500 dark:hover:text-gray-400 focus:bg-gray-100 dark:focus:bg-gray-800 focus:text-gray-500 dark:focus:text-gray-400 focus:outline-none"
                    >
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
            :class="{
                block: showingNavigationDropdown,
                hidden: !showingNavigationDropdown,
            }"
            class="sm:hidden"
        >
            <div
                class="space-y-1 pb-3 pt-2"
                v-for="navlink in navlinks"
                :key="navlink.id"
            >
                <ResponsiveNavLink
                    :href="route(navlink.route)"
                    :active="route().current(navlink.route)"
                >
                    {{ navlink.title }}
                </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div
                class="border-t border-gray-200 dark:border-gray-700 pb-1 pt-4"
            >
                <div class="px-4 flex justify-between items-center">
                    <div
                        class="text-base font-medium text-gray-800 dark:text-gray-200"
                    >
                        {{ user.name }}
                    </div>
                    <div
                        class="text-sm font-medium text-gray-500 dark:text-gray-400"
                    >
                        {{ user.email }}
                    </div>
                    <DarkModeToggle />
                </div>

                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink :href="route('profile.edit')">
                        Profile
                    </ResponsiveNavLink>
                    <ResponsiveNavLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                    >
                        Log Out
                    </ResponsiveNavLink>
                </div>
            </div>
        </div>
    </nav>
</template>
