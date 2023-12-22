<script setup>
import { ref, watch, onBeforeUnmount, onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import Swal from 'sweetalert2';

// Custom Components
import CustomButton from "@/Components/CustomButton.vue";
import HeadlessList from "@/Components/HeadlessList.vue";
import ImageCropper from "@/Components/Vue-Cropper/ImageCropper.vue";
import CropperModal from "@/Components/Vue-Cropper/Partials/CropperModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";

// Card Components
import Avatar from "@/Components/Card/Avatar.vue";
import AvatarFrame from "@/Components/Card/Frames/AvatarFrame.vue";
import MainFrame from "@/Components/Card/Frames/MainFrame.vue";

// Partials
import TopMenuSection from "@/Pages/Card/Partials/TopMenuSection.vue";
import CategoryAndAppSection from "@/Pages/Card/Partials/CategoryAndAppSection.vue";


const props = defineProps({
    card: {
        required: true,
        type: Object
    },
    cards: {
        required: true,
        type: Object
    },
    categories: {
        required: true,
        type: Object
    }
})

const updated = ref(true); // A ref to track whether the card has been updated
const default_card = ref(props.card.id); // A ref to store the ID of the default card
const loaded = ref(false); // A ref to track whether the component has loaded
const imageCropper = ref(null); // A ref to hold a reference to the image cropper component
const openCropperModal = ref(false); // A ref to control the visibility of the image cropper modal
const cardForm = useForm({
    id: props.card.id,
    image: [],
    title: props.card.title,
}); // A useForm instance for handling the card form data


// Run when the component is mounted
onMounted(() => {
    // Mark as loaded
    loaded.value = true;
});

// Run before the component is unmounted
onBeforeUnmount(() => {
    // Check if the form was not updated and not in processing state
    if (!updated.value && !cardForm.processing) {
        // Submit the card
        submitCard();
    }
});

// Watch for changes in cardForm
watch(cardForm, (newValue) => {
    // Check if the image is changed or the title is modified
    if (newValue.image.length !== 0 || newValue.title.toLowerCase() !== props.card.title.toLowerCase()) {
        // Mark the card as not updated
        updated.value = false;
    } else {
        // Mark the card as updated
        updated.value = true;
    }
});

// Watch for changes in default_card
watch(default_card, (newValue) => {
    // Check if the new default card ID is different from the current card ID
    if (newValue !== props.card.id) {
        // Visit the route to update the default card
        router.visit('/card/default_card', {
            method: 'patch',
            data: {
                default_card: newValue,
            },
        });
    }
});


// Function to close the image cropper modal
const closeCropperModal = () => {
    openCropperModal.value = false;
};

// Function to handle the "OK" button click
const clickedOk = () => {
    // Check if an image is selected in the card form
    if (cardForm.image.length > 0) {
        // Close the cropper modal and submit the card
        openCropperModal.value = false;
        submitCard();
    } else {
        // Save the image using the image cropper, go and check the imageCropper defineExpose section
        imageCropper.value.save();

        // Check if an image is still not selected
        if (cardForm.image.length <= 0) {
            // Close the cropper modal
            openCropperModal.value = false;
        }
    }
};

/**
 * Submit the card form for updating.
 */
function submitCard() {
    // Check if the title and image are unchanged, then return
    if (props.card.title.toLowerCase() === cardForm.title.toLowerCase() && cardForm.image.length <= 0) {
        return;
    }

    // Submit the card form using the PUT method
    cardForm.put('/card', {
        // Preserve the state of the form only if there are errors
        preserveState: (page) => Object.keys(page.props.errors).length,

        // Handle the success scenario
        onSuccess: () => {
            // Reset the image field after a successful submission
            cardForm.image = [];
        },
    });
}

/**
 * Prompt user to confirm card deletion and perform deletion if confirmed.
 */
function destroy() {
    // Translated text for the confirmation dialog
    const bodyText = window._translations['You cannot undo this action; all linked informations to this card will lost.'];
    const title = window._translations['Are you sure?'];
    const isDelete = window._translations['Delete'];
    const isCancel = window._translations['Cancel'];

    // Show a confirmation dialog using Swal (SweetAlert)
    Swal.fire({
        title: title,
        html: `<div>${bodyText} <span style="color:darkviolet; font-weight: bold">(${props.card.title})</span></div>`,
        showDenyButton: true,
        denyButtonText: isDelete,
        confirmButtonText: isCancel,
    }).then((result) => {
        // If the user confirms deletion
        if (result.isDenied) {
            // Delete the card using the cardForm instance
            cardForm.delete(route('card.destroy'), {
                preserveScroll: false,
                preserveState: false,
            });
        }
    });
}

</script>

<template>
    <!-- This is the page which users create new informations and edit their card/s -->

    <Head title="Card" />

    <transition name="main-fade">
        <div v-if="loaded">
            <MainFrame>
                <!-- select default card -->
                <div class="flex justify-center items-center mb-4">
                    <HeadlessList :list="cards" v-model="default_card"/>
                </div>
                <!-- select default card -->


                <!-- Avatar and Username Input -->
                <AvatarFrame>
                    <div class="flex items-center">
                        <Avatar :image="card.image" @click="openCropperModal = true" class="cursor-pointer"/>
                        <div class="flex justify-center items-center">
                            <TextInput
                                id="title"
                                type="text"
                                class="block w-3/4"
                                v-model="cardForm.title"
                                maxlength="30"
                                required
                                @keyup.enter="submitCard"
                            />
                            <CustomButton class="ms-1 w-4 h-4" @click="submitCard" :class="{'updatedColor' : !updated}"/>
                        </div>
                    </div>
                </AvatarFrame>
                <!-- Avatar and Username Input -->

                <!-- Show, Edit, Delete, Add Location buttons and Google Maps iFrame modal -->
                <TopMenuSection :card="card" @deleteCard="destroy()"/>
                <!-- Show, Edit, Delete, Add Location buttons and Google Maps iFrame modal -->

                <!-- Category Buttons, Listed Apps -->
                <CategoryAndAppSection :categories="categories" :card_id="card.id"/>
                <!-- Category Buttons, Listed Apps -->
            </MainFrame>
        </div>
    </transition>

    <CropperModal :show="openCropperModal" @close="closeCropperModal">
        <ImageCropper v-model="cardForm.image" @closeModal="closeCropperModal" ref="imageCropper"/>
        <div class="flex justify-between">
          <div>
            <DangerButton @click="cardForm.image = []" v-show="cardForm.image.length"> {{__("DISCARD")}} </DangerButton>
          </div>
          <PrimaryButton @click="clickedOk"> {{__("OK")}} </PrimaryButton>
        </div>
    </CropperModal>
</template>

<style scoped>
.updatedColor {
  background-color: red;
}

.main-fade-enter-active, .main-fade-leave-active {
    transition: opacity 1.6s;
}
.main-fade-leave-active, .main-fade-enter-active {
    opacity: 0;
    transition: opacity 1.6s;
}
.main-fade-enter, .main-fade-leave-to {
    opacity: 0;
}
.main-fade-leave, .main-fade-enter-to {
    opacity: 100%;
}
</style>
