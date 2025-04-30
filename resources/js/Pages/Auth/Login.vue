<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { trans } from "laravel-vue-i18n";
import { computed } from "vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    login: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};

// This maps the 'email' error to 'login' so it displays properly
const loginError = computed(() => {
    return form.errors.login || form.errors.email;
});

const translateStatus = (status) => {
    // Try to get a translation for this status
    const translation = trans(`pages.auth.statuses.${status}`);

    // Return the translation if it exists, otherwise return the original status
    return translation !== `pages.auth.statuses.${status}`
        ? translation
        : status;
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('pages.auth.Login')" />

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
            {{ translateStatus(status) }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel
                    for="login"
                    :value="$t('pages.auth.Email_or_Username')"
                />

                <TextInput
                    id="login"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.login"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="loginError" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="$t('pages.auth.Password')" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span
                        class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                        >{{ $t("pages.auth.Remember_me") }}</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 dark:text-gray-400 underline hover:text-indigo-600 dark:hover:text-amber-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                >
                    {{ $t("pages.auth.Forgot_your_password") }}
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ $t("pages.auth.Login") }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
