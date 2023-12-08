<script setup>

// Vue Composition API
import { ref } from 'vue';
import { router } from "@inertiajs/vue3";

// Custom components
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from "@inertiajs/vue3";
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Language from "@/Components/SVG/Language.vue";

// Heroicons
import { BarsArrowDownIcon } from "@heroicons/vue/24/solid/index.js";

// Services
import { useFlashMessages } from "@/Services/useFlashMessages.js";
import { useChangeLanguage} from "@/Services/useChangeLanguage.js";

const showingNavigationDropdown = ref(false); // Define a reactive variable to control the visibility of navigation dropdown
const showNav = ref(false); // Define a reactive variable to control the visibility of navigation
useFlashMessages(); // Initialize flash messages functionality


// Function to change the language
const changeLanguage = (language) => {
    // Send a POST request to change the language
    router.post('/change-language', {
        language: language
    }, {
        // Reload the window on successful language change
        onSuccess: () => { window.location.reload() }
    });
};

</script>

<template>
    <div class="min-h-screen">
        <div class="mx-auto flex justify-end">
            <div class="absolute z-999">
                <BarsArrowDownIcon class="m-1 w-6 h-6 text-black cursor-pointer opacity-50" @click="showNav = true" v-show="!showNav"/>
            </div>
        </div>
        <transition name="navbar-slide" mode="out-in">
            <nav class="bg-white navbar" v-show="showNav" key="navbar">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('home')" @click="showingNavigationDropdown = false">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-black"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex items-center">
                                <div>
                                    <Dropdown align="right" width="48">
                                        <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black hover:text-lime-600 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                <Language class="h-5 w-5"/>
                                                <svg
                                                    class="ml-2 -mr-0.5 h-3 w-3"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                        </template>

                                        <template #content>
                                          <Link @click="useChangeLanguage('en')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> English </Link>
                                            <Link @click="useChangeLanguage('de')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> Deutsch </Link>
                                            <Link @click="useChangeLanguage('tr')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> Türkçe </Link>
                                          <Link @click="useChangeLanguage('ru')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> Русский </Link>
                                          <Link @click="useChangeLanguage('fr')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> Français </Link>
                                            <Link @click="useChangeLanguage('ar')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> عربي </Link>
                                        </template>
                                    </Dropdown>
                                </div>
                                <a href="https://businessnfc.com/" target="_blank">
                                    {{ __('Buy a Card') }}
                                </a>
                                <a href="https://wa.me/+905335649378" target="_blank">
                                    {{ __('Create Virtual Card') }}
                                </a>
                            </div>
                        </div>


                        <div v-if="$page.props.auth.user" class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black hover:text-lime-600 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="'/card/'"> {{__("Cards")}} </DropdownLink>
                                        <DropdownLink :href="route('profile.edit')"> {{__("Settings")}} </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            {{__("Logout")}}
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div v-else class="hidden sm:flex sm:items-center sm:ml-6">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Link
                                    :href="route('login')"
                                    class="font-semibold text-black hover:text-lime-600 focus:outline focus:outline-2 mx-2 focus:rounded-sm focus:outline-red-500"
                                >{{__("Login")}}</Link
                                >

                                <Link
                                    :href="route('register')"
                                    class="ml-4 font-semibold text-black hover:text-lime-600 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                >{{__("Register")}}</Link
                                >
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden hamburger relative">
                            <div>
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black hover:text-lime-600 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                <Language class="h-5 w-5"/>
                                                <svg
                                                    class="ml-2 -mr-0.5 h-3 w-3"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                      <Link href="/change-language" @click="changeLanguage('en')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> English </Link>
                                        <Link @click="useChangeLanguage('de')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> Deutsch </Link>
                                        <Link href="/change-language" @click="changeLanguage('tr')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> Türkçe </Link>
                                      <Link @click="useChangeLanguage('ru')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> Русский </Link>
                                      <Link @click="useChangeLanguage('fr')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> Français </Link>
                                      <Link href="/change-language" @click="changeLanguage('ar')" class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"> عربي </Link>
                                    </template>
                                </Dropdown>
                            </div>
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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
                    class="sm:hidden"
                >
                    <div
                        v-if="$page.props.auth.user"
                        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                        class="sm:hidden focus:bg-indigo-100"
                    >
                        <div class="pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink @click="showingNavigationDropdown = false" :href="route('card')" :active="route().current('card')">
                                {{__("Cards")}}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.edit')" @click="showingNavigationDropdown = false"> {{__("Settings")}} </ResponsiveNavLink>

                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">
                            <div class="mt-3 space-y-1">
                                <a href="https://businessnfc.com/" target="_blank" class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                                    {{ __('Buy a Card') }}
                                </a>
                                <a href="https://wa.me/+905335649378" target="_blank" class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                                    {{ __('Create Virtual Card') }}
                                </a>
                                <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                    {{__("Logout")}}
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                    <!-- Responsive Navigation Menu -->
                    <div
                        v-else
                        :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                        class="sm:hidden"
                    >
                        <!-- Responsive Settings Options -->
                        <div class="pt-4 pb-1 border-t border-gray-200">

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('login')"> {{__("Log in")}} </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('register')"> {{__("Register")}} </ResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </div>

            </nav>
        </transition>

        <main>
            <slot />
        </main>
    </div>
</template>

<style scoped>
.navbar-slide-enter-active,
.navbar-slide-leave-active {
    transition: all 0.5s;
}
.navbar-slide-enter, .navbar-slide-leave-to {
    transform: translateY(-100%);
}

.navbar-slide-leave-active,
.navbar-slide-enter-active {
    transform: translateY(-100%);

    transition: all 0.5s;
}
.navbar-slide-leave, .navbar-slide-enter-to {
    transform: translateY(0%);
}

</style>
