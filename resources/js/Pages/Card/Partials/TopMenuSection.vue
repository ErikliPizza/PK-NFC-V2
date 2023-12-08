<script setup>
// Heroicons
import { CreditCardIcon, EyeIcon, TrashIcon, ChatBubbleBottomCenterTextIcon, PlusCircleIcon } from "@heroicons/vue/24/outline/index.js";
import { StarIcon } from "@heroicons/vue/24/solid/index.js";
// Custom Components
import Modal from "@/Components/Modal.vue";
import Geolocation from "@/Components/Geolocation.vue";
import TopButton from "@/Components/Card/TopButton.vue";
import TopLink from "@/Components/Card/TopLink.vue";
import TopFrame from "@/Components/Card/Frames/TopFrame.vue";
import MiddleFrame from "@/Components/Card/Frames/MiddleFrame.vue";

// Vue Composition API
import {ref} from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import ListPdf from "@/Components/ListPdf.vue";
import CustomModal from "@/Components/CustomModal.vue";


const props = defineProps({
    card: {
        required: true
    }
})

const form = useForm({
    card_id: props.card.id,
    t_ü: props.card.billing_information?.t_ü ?? null,
    a: props.card.billing_information?.a ?? null,
    t_s_n: props.card.billing_information?.t_s_n ?? null,
    v_d: props.card.billing_information?.v_d ?? null,
    v_n: props.card.billing_information?.v_n ?? null,
    m_n: props.card.billing_information?.m_n ?? null,
    email: props.card.billing_information?.email ?? null,
    k_a: props.card.billing_information?.k_a ?? null
});

const mapModal = ref(false); // Ref to control the visibility of the map modal

const closeMapModal = () => { mapModal.value = false; }; // Function to close the map modal
const openMapModal = () => { mapModal.value = true; }; // Function to open the map modal

const billingModal = ref(false); // Ref to control the visibility of the billing modal

const closeBillingModal = () => { billingModal.value = false; }; // Function to close the billing modal
const openBillingModal = () => { billingModal.value = true; }; // Function to open the billing modal

const pdfModal = ref(false); // Ref to control the visibility of the pdf modal
const closePdfModal = () => { pdfModal.value = false; }; // Function to close the pdf modal
const openPdfModal = () => { pdfModal.value = true; }; // Function to open the pdf modal

const aboutModal = ref(false); // Ref to control the visibility of the about modal
const closeAboutModal = () => { aboutModal.value = false; }; // Function to close the about modal
const openAboutModal = () => { aboutModal.value = true; }; // Function to open the about modal


/**
 * Update the billing information to the card.
 */
const addBillingInformation = () => {
    // Send a POST request to the 'card.location' route
    form.post(route('card.billing_info'), {
        preserveState: false,
    });
};

const pdf = ref(null);
const pdfForm = useForm({
    title: null,
    pdf: null,
})
const handlePdfUpload = (event) => {
    const file = event.target.files[0];
    const maxFileSize = 20 * 1024 * 1024; // 20 MB in bytes

    if (file.size > maxFileSize) {
        const alertText = window._translations['The file is too large. Please select a file that is 20MB or less.'];
        pdf.value.value = '';
        pdfForm.pdf = null;
        // File is too large
        alert(alertText);
    } else {
        pdfForm.pdf = event.target.files[0]
    }
};
const uploadPdf = () => {
    pdfForm.post(`/card/${props.card.id}/pdfs`, {
        preserveState: true,
        onSuccess: () => pdfForm.reset(),
    });
};

const aboutForm = useForm({
    card_id: props.card.id,
    about: props.card.bio ?? null,
})

/**
 * Update about to the card.
 */
const addAbout = () => {
    // Send a POST request to the 'card.location' route
    aboutForm.post(route('card.about'), {
        preserveState: true,
    });
};

const maximumAllowedPdf = usePage().props.auth.user.premium === 1 ? 3 : 1;
</script>

<template>

  <MiddleFrame>
    <TopLink :href="'/nfc/'+card.slug">
      <EyeIcon class="w-5 h-5" />
      <span class="text-sm">{{ __('Show') }}</span>
    </TopLink>
    <TopButton @click="openAboutModal">
      <ChatBubbleBottomCenterTextIcon class="w-5 h-5" />
      <span class="text-sm">{{ __('About') }}</span>
    </TopButton>
  </MiddleFrame>
    <!-- Show, Edit, Delete buttons-->
    <TopFrame>
        <TopLink :href="'/card/'+card.id">
            <CreditCardIcon class="w-5 h-5"/>
            <span class="text-sm">{{ __('Edit') }}</span>
        </TopLink>
        <TopButton @click="$emit('deleteCard')">
            <TrashIcon class="w-5 h-5"/>
            <span class="text-sm truncate">{{ __('Delete') }}</span>
        </TopButton>
    </TopFrame>
    <!-- Show, Edit, Delete buttons-->
  <hr class="my-3">
    <!-- Map button-->
    <MiddleFrame>
      <TopButton @click="openMapModal">
        <span class="truncate px-1">
          {{ __('Add Location') }}
        </span>
      </TopButton>
      <TopLink :href="'/card/'+card.id+'/catalog'">
        <span class="text-sm truncate">{{ __('Create Catalog') }}</span>
      </TopLink>
    </MiddleFrame>
    <MiddleFrame>
        <TopButton @click="openBillingModal">
          <span class="truncate px-1">
            {{ __('Billing Information') }}
          </span>
        </TopButton>
        <button @click="openPdfModal" class="relative bg-blue-50 p-1 rounded-lg flex justify-center w-full items-center space-x-1 text-sm">
            <div class="absolute top-0 right-0 -mt-2.5 -mr-2" v-if="$page.props.auth.user.premium === 1">
                <StarIcon name="star" class="h-6 w-6 text-yellow-400"></StarIcon>
            </div>
            {{ __('PDF') }}
        </button>

    </MiddleFrame>

    <!-- Map button-->


    <!-- Geo Location Modal-->
    <Modal :show="mapModal" @close="closeMapModal">
        <Geolocation @close="closeMapModal" :card_id="card.id" :address="card.address ?? null" :longitude="card.longitude ?? null" :latitude="card.latitude ?? null"/>
    </Modal>
    <!-- Geo Location Modal-->

    <CustomModal :show="pdfModal" @close="closePdfModal">
        <!-- Modal Content -->
        <div class="p-2">
            <div class="text-gray-600 text-sm">
              {{ card.pdfs.length }}/{{ maximumAllowedPdf }}
            </div>

            <form @submit.prevent="uploadPdf" v-if="card.pdfs.length < maximumAllowedPdf">

                <InputLabel for="pdfTitle" class="text-center mb-1">{{__('Title')}}</InputLabel>
                <div class="flex justify-center items-center">
                    <TextInput
                        maxlength="30"
                        id="pdfTitle"
                        type="text"
                        class="block w-3/4"
                        v-model="pdfForm.title"
                        required
                    />
                </div>
                <InputError :message="$page.props.errors.title" class="mt-2 text-sm text-center" />
                <input type="file" hidden ref="pdf" class="border p-2 mb-4" accept=".pdf" @input="handlePdfUpload"/>
                <div class="flex justify-center items-center mt-2" v-if="!pdfForm.pdf">
                    <PlusCircleIcon @click="()=>{ pdf.click(); }" class="w-10 h-10 text-blue-500 cursor-pointer"/>
                </div>
                <div v-else>
                    <div @click="pdfForm.reset('pdf')" class="flex justify-center mt-2 text-lg items-center text-gray-800 cursor-pointer space-x-1" >
                        <span class="truncate">{{ pdfForm.pdf.name }}</span>
                        <TrashIcon class="w-4 h-4 text-red-400"/>
                    </div>
                    <div class="flex items-center justify-center mt-4">
                        <div v-if="pdfForm.progress" class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-12 w-12"></div>
                    </div>
                </div>
                <InputError :message="$page.props.errors.pdf" class="mt-2 text-sm text-center" />
                <!-- Modal Close Button -->
                <div class="mt-6 flex justify-between space-x-2">
                    <SecondaryButton @click="closePdfModal" class="truncate"> {{__("Cancel")}} </SecondaryButton>
                    <button
                        type="submit"
                        class="inline-flex truncate items-center px-4 py-2 bg-green-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        {{ __('Upload') }}
                    </button>
                </div>
            </form>
            <div v-else>
                <div class="flex justify-center text-gray-600 text-sm mt-6">
                    {{ __('You\'ve reached the maximum file upload limit.') }}
                </div>
            </div>
          <div class="text-gray-600 text-xs my-4 text-center" v-if="$page.props.auth?.user.premium !== 1">
            {{ __('upgrade_to_premium') }}
          </div>
            <ListPdf :card="card"/>
        </div>
    </CustomModal>

    <Modal :show="billingModal" @close="closeBillingModal">
        <!-- Modal Content -->
        <div class="p-6">
            <!-- Location Title -->
            <h2 class="text-lg font-medium text-gray-900 cursor-pointer">
                <div class="flex items-center">
                    {{ __('Billing Information') }}
                </div>
            </h2>


            <!-- Map Display -->
            <div class="mt-3">
                <InputLabel for="t_ü" class="px-1">{{ __('t_ü') }}</InputLabel>
                <textarea
                    maxlength="255"
                    v-model="form.t_ü"
                    class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300"
                    rows="2"
                >
                </textarea>
            </div>
            <div class="mt-3">
                <InputLabel for="t_s_n" class="px-1">{{ __('a') }}</InputLabel>
                <textarea
                    maxlength="255"
                    v-model="form.a"
                    class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300"
                    rows="2"
                >
                </textarea>
            </div>

            <div class="mt-3 justify-center">
                <InputLabel for="t_s_n" class="px-1">{{ __('t_s_n') }}</InputLabel>
                <TextInput
                    maxlength="255"
                    id="t_s_n"
                    type="text"
                    class="block w-full"
                    v-model="form.t_s_n"
                />
            </div>

            <div class="mt-3">
                <InputLabel for="v_d" class="px-1">{{ __('v_d') }}</InputLabel>
                <TextInput
                    maxlength="255"
                    id="v_d"
                    type="text"
                    class="block w-full"
                    v-model="form.v_d"
                />
            </div>

            <div class="mt-3">
                <InputLabel for="v_n" class="px-1">{{ __('v_n') }}</InputLabel>
                <TextInput
                    maxlength="255"
                    id="v_n"
                    type="text"
                    class="block w-full"
                    v-model="form.v_n"
                />
            </div>

            <div class="mt-3">
                <InputLabel for="m_n" class="px-1">{{ __('m_n') }}</InputLabel>
                <TextInput
                    maxlength="255"
                    id="m_n"
                    type="text"
                    class="block w-full"
                    v-model="form.m_n"
                />
            </div>

            <div class="mt-3">
                <InputLabel for="email" class="px-1">{{ __('Email') }}</InputLabel>
                <TextInput
                    maxlength="255"
                    id="email"
                    type="email"
                    class="block w-full"
                    v-model="form.email"
                />
            </div>

            <div class="mt-3">
                <InputLabel for="k_a" class="px-1">{{ __('k_a') }}</InputLabel>
                <TextInput
                    maxlength="255"
                    id="k_a"
                    type="text"
                    class="block w-full"
                    v-model="form.k_a"
                />
            </div>

            <!-- Modal Close Button -->
            <div class="mt-6 flex justify-between">
                <SecondaryButton @click="closeBillingModal"> {{__("Cancel")}} </SecondaryButton>
                <button
                    class="inline-flex items-center px-4 py-2 bg-green-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    @click="addBillingInformation"> {{__("OK")}} </button>
            </div>
        </div>
        <!-- End of Modal Content -->
    </Modal>

    <Modal :show="aboutModal" @close="closeAboutModal">
        <!-- Modal Content -->
        <div class="p-6">
            <!-- Location Title -->
            <h2 class="text-lg font-medium text-gray-900 cursor-pointer">
                <div class="flex items-center">
                    {{ __('About') }}
                </div>
            </h2>


            <!-- Map Display -->
            <div class="mt-3">
                <textarea
                    v-model="aboutForm.about"
                    class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300"
                    rows="2"
                    maxlength="255"
                    @keyup.enter="addAbout"
                    @keydown.prevent.enter
                >
                </textarea>
            </div>

            <!-- Modal Close Button -->
            <div class="mt-6 flex justify-between">
                <SecondaryButton @click="closeAboutModal"> {{__("Cancel")}} </SecondaryButton>
                <button
                    class="inline-flex items-center px-4 py-2 bg-green-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    @click="addAbout"> {{__("OK")}} </button>
            </div>
        </div>
        <!-- End of Modal Content -->
    </Modal>

</template>

<style scoped>
.preserve-space {
    white-space: pre-line;
}
.loader {
    border-top-color: #3498db; /* Change the color of the spinner */
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
