<script setup>
// Vue Components
import TopButton from "@/Components/Card/TopButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Modal from "@/Components/Modal.vue";
import TopFrame from "@/Components/Card/Frames/TopFrame.vue";
import MiddleFrame from "@/Components/Card/Frames/MiddleFrame.vue";
import CustomModal from "@/Components/CustomModal.vue";

// Heroicons
import {
    ClipboardIcon,
    CreditCardIcon,
    MapPinIcon,
    PhoneArrowUpRightIcon,
    QrCodeIcon,
    ShareIcon
} from "@heroicons/vue/24/outline/index.js";

// Vue Composition API
import { ref, computed, onMounted, nextTick } from "vue";

// Inertia.js
import { router, usePage } from "@inertiajs/vue3";

// Custom Composables
import { useClipboard } from "@/Composables/useClipboard.vue";
import { useContact } from "@/Composables/useContact.vue";


const props = defineProps({
    card: {
        required: true
    },
})



const { addContact } = useContact();
const { copyToClipboard } = useClipboard();

// State and Values
const page = usePage();
const showMap = ref(false);
const hasPhoneApp = ref(false); // Define a reactive variable 'hasPhoneApp' and set its initial value to 'false'.
const phoneInfoValue = ref([]); // Define a reactive variable 'phoneInfoValue' and initialize it as an empty array.
const phoneInfos = props.card.informations.filter(info => info.app.component === 'phone' || info.app.component === 'whatsapp' || info.app.component === 'viber'); // Filter 'props.card.informations' to get phone-related information.
const mailInfoValue = ref([]); // Define a reactive variable 'mailInfoValue' and initialize it as an empty array.
const mailInfos = props.card.informations.filter(info => info.app.component === 'mail'); // Filter 'props.card.informations' to get mail-related information.
const webInfoValue = ref(); // Define a reactive variable 'webInfoValue'.
const webInfos = props.card.informations.filter(info => info.app.component === 'web'); // Filter 'props.card.informations' to get web-related information.

// Check if there is mail-related information.
if (mailInfos.length > 0) {
  // Map 'mailInfos' to extract 'title' and 'value' properties and assign them to 'mailInfoValue'.
  mailInfoValue.value = mailInfos.map(mailInfo => ({
    title: mailInfo.title ?? '', // Use the title if it exists, otherwise use an empty string.
    value: mailInfo.value
  }));
}

// Check if there is web-related information.
if (webInfos.length > 0) {
    // Map 'webInfos' to extract 'value' properties and assign them to 'webInfoValue'.
    webInfoValue.value = webInfos.map(webInfo => webInfo.value);
}


// Check if there is phone-related information.
if (phoneInfos.length > 0) {
  // Set 'hasPhoneApp' to 'true' to indicate the presence of phone-related information.
  hasPhoneApp.value = true;

  // Map 'phoneInfos' to extract 'title' and 'value' properties and assign them to 'phoneInfoValue'.
  phoneInfoValue.value = phoneInfos.map(phoneInfo => ({
    title: phoneInfo.title ?? '', // Use the title if it exists, otherwise use an empty string.
    value: phoneInfo.value
  }));
}


const location = `https://www.google.com/maps/embed/v1/place?key=AIzaSyBzhHFJCvgxk4TB58mWT33ZD70POzBWVk0&q=${props.card.latitude},${props.card.longitude}`;




/**
 * Closes the map modal.
 */
const closeModal = () => {
    showMap.value = false;
};

/**
 * Copies the card's address to the clipboard.
 */
const copyAddress = () => {
    // Get the translation for the title "Location Holder"
    const title = window._translations['Location Holder'];

    // Get the address value from the card's props
    const value = props.card.address;

    // Call the copyToClipboard function with the address and title
    copyToClipboard(value, title);
};

/**
 * Edit the card and update its default status if needed.
 */
function editCard() {
    // Check if the current user's default card ID does not match the card's ID
    if (page.props.auth?.user.default_card !== props.card.id) {
        // Visit the route to set the card as the default
        router.visit('/card/default_card', {
            method: 'patch',
            onFinish: () => {
                // After the patch is done, visit the card listing page
                router.visit('/card');
            },
            data: {
                default_card: props.card.id,
            },
        });
    } else {
        // If the card is already the default, simply visit the card listing page
        router.visit('/card');
    }
}

const billingModal = ref(false); // Ref to control the visibility of the billing modal

const closeBillingModal = () => { billingModal.value = false; }; // Function to close the billing modal
const openBillingModal = () => { billingModal.value = true; }; // Function to open the billing modal

/**
 * Copies the card's billing information to the clipboard.
 */
const copyBillingInformation = () => {
    // Get the translation for the title "Location Holder"
    const title = window._translations['Billing Information'];

    const value = generateFullBodyTextWithTranslations(props.card.billing_information);

    // Call the copyToClipboard function with the address and title
    copyToClipboard(value, title);
};

/**
 * Copies the card's billing information to the clipboard as part.
 */
const copyBillingInformationSpecific = (t, v) => {
    // Get the translation for the title "Location Holder"
    const title = window._translations[t];

    // Call the copyToClipboard function with the address and title
    copyToClipboard(v, title);
};

// Function to generate full body text with translations
function generateFullBodyTextWithTranslations(billingInfo) {
    if (billingInfo && typeof billingInfo === 'object') {
        const keysToConcatenate = ['t_ü', 'a', 't_s_n', 'v_d', 'v_n', 'm_n', 'email', 'k_a'];
        return keysToConcatenate
            .map(key => {
                // Fetch the translation for the key, if it exists, else use the key itself
                const title = window._translations && window._translations[key] ? window._translations[key] : key;
                const value = billingInfo[key];
                // If the value is not empty, return the concatenated title and value, otherwise return an empty string
                return value ? `${title}: ${value}` : '';
            })
            .filter(line => line) // Filter out any empty strings resulting from empty billing_info values
            .join('\n \n');
    }
    return '';
}

// Computed property to check for any non-empty billing information fields
const hasBillingInformation = computed(() => {
    const billingInfo = props.card.billing_information;

    // Check if billing_information exists and is an object
    if (billingInfo && typeof billingInfo === 'object') {
        // List of keys to check for non-empty values
        const keysToCheck = ['t_ü', 'a', 't_s_n', 'v_d', 'v_n', 'm_n', 'email', 'k_a'];
        // Return true if any of the keys have a truthy value
        return keysToCheck.some(key => billingInfo[key]);
    }

    // If billing_information does not exist, return false
    return false;
});

const showBio = ref(false); // State to toggle bio visibility
const bioParagraph = ref(null); // Reference to the paragraph element
const toggleBio = () => {
    showBio.value = !showBio.value;
    // If we are hiding the bio, check for truncation
    if (!showBio.value) {
        nextTick(() => {
            checkTruncation();
        });
    }
};

// This function checks if the text is truncated
const checkTruncation = () => {
    if (bioParagraph.value) {
        showBio.value = bioParagraph.value.scrollWidth > bioParagraph.value.clientWidth;
        showBio.value = !showBio.value;
    }
};

// Use the mounted hook to check for truncation after the component has been mounted
onMounted(() => {
    nextTick(() => {
        checkTruncation();
    });
});
</script>

<template>
    <p class="text-lg font-medium truncate break-words text-center text-gray-800 mt-1">{{ card.title }}</p>
    <p
        ref="bioParagraph"
        class="px-3 indent-3 cursor-pointer break-words mt-1 mb-3"
        :class="{'truncate text-gray-500 italic text-xs bg-gradient-to-r from-gray-500 to-gray-50 text-transparent bg-clip-text': !showBio, 'text-xs bg-gradient-to-r from-gray-700 to-blue-100 inline-block text-transparent bg-clip-text': showBio}"
        @click="toggleBio"
        v-if="card.bio"
    >
        {{ card.bio }}
    </p>
    <div class="flex justify-center mb-2" v-if="hasBillingInformation">
        <TopButton @click="openBillingModal">
            {{ __('Billing Information') }}
        </TopButton>
    </div>
    <TopFrame>
        <!-- Button to add to contact -->
        <TopButton @click="addContact(card.title, phoneInfoValue, mailInfoValue, card.address??undefined, webInfoValue)" v-if="hasPhoneApp">
            <PhoneArrowUpRightIcon class="w-3 h-3" />
            <span class="text-sm truncate px-1">{{__('Add Contact')}}</span>
        </TopButton>

        <!-- Button to download QR code -->
        <TopButton @click="$emit('downloadQR')">
            <QrCodeIcon class="w-3 h-3"/>
            <span class="text-sm">{{__('QR')}}</span>
        </TopButton>
    </TopFrame>

    <MiddleFrame>
        <!-- Button to show location on maps -->
        <TopButton @click="showMap = true" v-if="!!card.latitude">
            <MapPinIcon class="w-3 h-3"/>
            <span class="text-sm truncate px-1">{{__('Show on Maps')}}</span>
        </TopButton>

        <!-- Button to edit card -->
        <TopButton v-if="$page.props.auth.user?.id === card.user_id" @click="editCard">
            <CreditCardIcon class="w-3 h-3"/>
            <span class="text-sm truncate px-1">{{ __('Edit') }}</span>
        </TopButton>
    </MiddleFrame>

    <Modal :show="showMap" @close="closeModal">
        <!-- Modal Content -->
        <div class="p-6">
            <!-- Location Title -->
            <h2 class="text-lg font-medium text-gray-900">
                {{__("Location Holder")}}
            </h2>

            <!-- Display address and allow copying -->
            <p class="mt-1 text-sm text-gray-600" v-if="card.address" @click="copyAddress()">
                {{card.address}}
            </p>

            <!-- Map Display -->
            <div class="mt-3">
                <div class="flex justify-center rounded-lg">
                    <!-- Google Maps Embedded iframe -->
                    <iframe
                        style="width: 100%; height: 50vh;"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        :src="location">
                    </iframe>
                </div>
            </div>

            <!-- Modal Close Button -->
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal"> {{__("OK")}} </SecondaryButton>
            </div>
        </div>
        <!-- End of Modal Content -->
    </Modal>

    <CustomModal :show="billingModal" @close="closeBillingModal">
        <!-- Modal Content -->
        <div class="p-6">
            <!-- Location Title -->
            <h2 class="text-lg font-medium text-gray-900 cursor-pointer" @click="copyBillingInformation()">
                <div class="flex items-center">
                    {{ __('Billing Information') }}
                    <ClipboardIcon class="w-6 h-6 p-1"/>
                </div>
            </h2>

            <div class="mt-3" v-if="card.billing_information.t_ü" @click="copyBillingInformationSpecific('t_ü', card.billing_information.t_ü)">
                <span class="font-bold">{{ __('t_ü') }}</span>
                <p class="px-0.5 text-gray-600">{{ card.billing_information.t_ü }}</p>
            </div>

            <div class="mt-3" v-if="card.billing_information.a" @click="copyBillingInformationSpecific('a', card.billing_information.a)">
                <span class="font-bold">{{ __('a') }}</span>
                <p class="px-0.5 text-gray-600">{{ card.billing_information.a }}</p>
            </div>

            <div class="mt-3" v-if="card.billing_information.t_s_n" @click="copyBillingInformationSpecific('t_s_n', card.billing_information.t_s_n)">
                <span class="font-bold">{{ __('t_s_n') }}</span>
                <p class="px-0.5 text-gray-600">{{ card.billing_information.t_s_n }}</p>
            </div>

            <div class="mt-3" v-if="card.billing_information.v_d" @click="copyBillingInformationSpecific('v_d', card.billing_information.v_d)">
                <span class="font-bold">{{ __('v_d') }}</span>
                <p class="px-0.5 text-gray-600">{{ card.billing_information.v_d }}</p>
            </div>

            <div class="mt-3" v-if="card.billing_information.v_n" @click="copyBillingInformationSpecific('v_n', card.billing_information.v_n)">
                <span class="font-bold">{{ __('v_n') }}</span>
                <p class="px-0.5 text-gray-600">{{ card.billing_information.v_n }}</p>
            </div>

            <div class="mt-3" v-if="card.billing_information.m_n" @click="copyBillingInformationSpecific('m_n', card.billing_information.m_n)">
                <span class="font-bold">{{ __('m_n') }}</span>
                <p class="px-0.5 text-gray-600">{{ card.billing_information.m_n }}</p>
            </div>

            <div class="mt-3" v-if="card.billing_information.email" @click="copyBillingInformationSpecific('Email', card.billing_information.email)">
                <span class="font-bold">{{ __('Email') }}</span>
                <p class="px-0.5 text-gray-600">{{ card.billing_information.email }}</p>
            </div>

            <div class="mt-3" v-if="card.billing_information.k_a" @click="copyBillingInformationSpecific('k_a', card.billing_information.k_a)">
                <span class="font-bold">{{ __('k_a') }}</span>
                <p class="px-0.5 text-gray-600">{{ card.billing_information.k_a }}</p>
            </div>

        </div>
        <!-- End of Modal Content -->
    </CustomModal>

</template>

<style scoped>
.preserve-space {
    white-space: pre-line;
}
</style>
