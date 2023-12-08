<script setup>
// Custom components
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Language from "@/Components/SVG/Language.vue";
import Dropdown from "@/Components/Dropdown.vue";

// InertiaJS
import { Link, router, usePage } from '@inertiajs/vue3';

// Vue Composition API
import { ref, computed, watch, onMounted } from 'vue';

// Services
import { useFlashMessages } from "@/Services/useFlashMessages.js";
import { useChangeLanguage} from "@/Services/useChangeLanguage.js";
useFlashMessages(); // Initialize flash messages functionality

const page = usePage(); // Get the current page using InertiaJS hook
const activeComponent = computed(() => page.component); // Compute the active component on the current page

// Define reactive variables for controlling login visibility and toggle visibility
const showLogin = ref(true);
const showToggle = ref(true);


// Run when the component is mounted
onMounted(() => {
    outedLanding(activeComponent.value);
});

// Watch for changes in the active component
watch(activeComponent, (newValue) => {
    outedLanding(newValue);
});
// Function to change the language
const changeLanguage = (language) => {
    router.post('/change-language', {
        language: language
    }, {
        onSuccess: () => {window.location.reload()}
    })
}

// Function to determine landing behavior based on the active component
function outedLanding(component) {
    if (component === 'Auth/Register') {
        showLogin.value = true;
    } else {
        showLogin.value = false;
    }
}

// Function to toggle the login view and change the route accordingly
const toggleLogin = () => {
    showLogin.value = !showLogin.value;
    if (showLogin.value) {
        router.visit('/register');
    } else {
        router.visit('/login');
    }
};

</script>

<template>
    <div class="min-h-screen px-2 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <!-- Logo and Language Dropdown -->
        <div class="flex justify-center">
            <Link href="/">
                <!-- Application logo -->
                <ApplicationLogo class="block h-9 w-auto fill-current text-white mb-4" />
            </Link>
            <!-- Language Dropdown -->
            <Dropdown align="right" width="48">
                <!-- Dropdown trigger button -->
                <template #trigger>
          <span class="inline-flex rounded-md">
            <button
                type="button"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-black hover:text-lime-600 focus:outline-none transition ease-in-out duration-150"
            >
              <Language class="h-5 w-5" />
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

                <!-- Dropdown content -->
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

        <!-- Toggle Login/Register -->
        <div class="w-full sm:max-w-md" v-show="showToggle">
            <div
                @click="toggleLogin"
                class="rounded-xl bg-white flex items-center justify-between py-2 px-4"
                style="cursor: pointer; -webkit-tap-highlight-color: transparent;"
            >
        <span
            class="font-semibold text-not-selectable"
            :class="{
            'text-black': showLogin,
            'bg-black text-white py-1 px-3 rounded-xl': !showLogin
          }"
        >{{ __("Log in") }}</span>
                <span
                    class="font-semibold text-not-selectable"
                    :class="{
            'text-black': !showLogin,
            'bg-black text-white py-1 px-3 rounded-xl': showLogin
          }"
                >{{ __("Register") }}</span>
            </div>
        </div>

        <!-- Content Container -->
        <div class="w-full sm:max-w-md mt-2 px-6 py-4 bg-white shadow-md overflow-y-auto sm:rounded-lg">
            <div>
                <slot />
            </div>
        </div>
    </div>
</template>
