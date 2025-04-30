<script setup>
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import { ChevronDownIcon } from "@heroicons/vue/24/outline";
import { router } from "@inertiajs/vue3";
import { loadLanguageAsync } from "laravel-vue-i18n";
import { reactive, ref } from "vue";

const langs = reactive({
    lang: [
        { title: "English", value: "en", flag: "🇬🇧" },
        { title: "Indonesia", value: "id", flag: "🇮🇩" },
    ],
});

const langTitle = ref(localStorage.getItem("langTitle") || "English");
const currentFlag = ref(
    langs.lang.find((lang) => lang.title === langTitle.value)?.flag || "🇬🇧"
);

const changeLocale = (lang) => {
    router.get(route("language", lang.value));
    localStorage.setItem("langTitle", lang.title);
    localStorage.setItem("lang", lang.value);
    currentFlag.value = lang.flag;
    loadLanguageAsync(lang.value);
};
</script>

<template>
    <div class="relative ms-3">
        <Dropdown align="right" width="48">
            <template #trigger>
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md border border-transparent dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm font-medium leading-4 text-gray-500 dark:text-gray-400 transition duration-150 ease-in-out hover:text-indigo-600 dark:hover:text-amber-400 focus:outline-none"
                    >
                        <span class="mr-2">{{ currentFlag }}</span>
                        {{ langTitle }}
                        <ChevronDownIcon
                            class="h-4 w-4 ml-2 text-indigo-600 dark:text-amber-500"
                        />
                    </button>
                </span>
            </template>

            <template #content>
                <div v-for="lang in langs.lang" :key="lang.value">
                    <DropdownLink @click="changeLocale(lang)" href="#">
                        <span class="mr-2">{{ lang.flag }}</span>
                        {{ lang.title }}
                    </DropdownLink>
                </div>
            </template>
        </Dropdown>
    </div>
</template>
