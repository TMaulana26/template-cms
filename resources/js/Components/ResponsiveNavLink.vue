<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-400 dark:border-amber-500 text-start text-base font-medium text-indigo-700 dark:text-amber-400 bg-indigo-50 dark:bg-gray-800 focus:outline-none focus:text-indigo-800 dark:focus:text-amber-300 focus:bg-indigo-100 dark:focus:bg-gray-700 focus:border-indigo-700 dark:focus:border-amber-400 transition duration-150 ease-in-out'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-amber-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-indigo-700 dark:focus:text-amber-400 focus:bg-gray-50 dark:focus:bg-gray-800 focus:border-indigo-400 dark:focus:border-amber-500 transition duration-150 ease-in-out',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
