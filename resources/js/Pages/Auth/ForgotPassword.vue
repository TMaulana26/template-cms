<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { trans } from "laravel-vue-i18n";

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};

const translateStatus = (status) => {
    const translation = trans(`pages.auth.statuses.${status}`);
    
    return translation !== `pages.auth.statuses.${status}` ? translation : status;
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('pages.auth.Forgot_Password')" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ $t("pages.auth.Forgot_Password_Desc") }}
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600 dark:text-green-400"
        >
            {{ translateStatus(status) }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="$t('pages.auth.Email_password')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ $t("pages.auth.Email_Reset_Link") }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
