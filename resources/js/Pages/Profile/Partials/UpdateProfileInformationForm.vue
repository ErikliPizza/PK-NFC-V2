<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import HeadlessList from "@/Components/HeadlessList.vue";
import { router, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from "vue";
import {CreditCardIcon} from "@heroicons/vue/24/solid/index.js";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    cards: {
        type: Object
    }
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

// Create a ref for the user's default card
const default_card = ref(user.default_card);

// Watch for changes in the default_card ref
watch(default_card, (newValue) => {
    // Check if the new default card ID is different from the current user's default card ID
    if (newValue !== user.default_card) {
        // Visit the route to update the user's default card
        router.visit('/card/default_card', {
            method: 'patch',
            data: {
                default_card: newValue,
            },
        });
    }
});

</script>

<template>
    <!-- Flex container to horizontally align content -->
    <div class="flex justify-center items-center mb-4 gap-x-2 px-2">
        <!-- Flex container for default card selection and card creation button -->
        <div class="flex items-center space-x-2" v-if="cards.length > 0">
            <!-- Use HeadlessList component to select default card -->
            <HeadlessList :list="cards" v-model="default_card"/>
            <!-- Link to navigate to card creation page -->
            <Link href="/card"><CreditCardIcon class="w-7 h-7 text-blue-400 mt-1.5 cursor-pointer"/></Link>
        </div>
        <!-- Display a message if no cards are defined -->
        <p class="text-red-400 font-bold" v-else>{{__("You haven't defined a card yet")}}</p>
    </div>

    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">{{__("Profile Information")}}</h2>

            <p class="mt-1 text-sm text-gray-600">
              {{__("Update your account's profile information and email address.")}}
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
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

            <div>
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

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                  {{__("Your email address is unverified.")}}
                  <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                    {{__("Click here to re-send the verification email.")}}
                  </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                  {{__("A new verification link has been sent to your email address.")}}
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">{{__("Save")}}</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">{{__("Saved.")}}</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
