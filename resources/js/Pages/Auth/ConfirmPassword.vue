<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirm Password" />
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 px-4 sm:pt-0 bg-gray-100">
        <div
            class="w-full sm:max-w-md mt-2 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <div class="mb-4 text-sm text-gray-600">
                {{__("This is a secure area of the application. Please confirm your password before continuing.")}}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="password">{{__("Password")}}</InputLabel>
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="flex justify-end mt-4">
                    <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{__("Confirm")}}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
