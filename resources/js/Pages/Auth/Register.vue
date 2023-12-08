<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Google from "@/Components/SVG/Google.vue";

defineOptions({
    layout: GuestLayout,
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const isVisible = ref(false);
onMounted(() => {
    isVisible.value = true;
});
</script>

<template>
    <transition
        enter-from-class="transition duration-1000 opacity-0"
        enter-to-class="transition duration-1000 opacity-100">
        <div v-if="isVisible">
            <Head title="Sign in" />

            <form @submit.prevent="submit">
                <div>
                  <InputLabel for="name">{{__("Name")}}</InputLabel>

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                  <InputLabel for="email">{{__("Email")}}</InputLabel>

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                  <InputLabel for="password">{{__("Password")}}</InputLabel>

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                  <InputLabel for="password_confirmation">{{__("Confirm Password")}}</InputLabel>

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <Link
                        :href="route('login')"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      {{__("Already registered?")}}
                    </Link>

                    <PrimaryButton class="ml-4 text-sm" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                      {{__("Register")}}
                    </PrimaryButton>
                    <a href="/login/google" class="ml-4">
                        <Google class="w-8 h-8"/>
                    </a>
                </div>
            </form>
        </div>
    </transition>
</template>
