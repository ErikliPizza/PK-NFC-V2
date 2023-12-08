<script setup>
import MainFrame from "@/Components/Card/Frames/MainFrame.vue";
import Avatar from "@/Components/Card/Avatar.vue";
import {ArrowLeftOnRectangleIcon, PlusIcon, ChevronUpDownIcon} from "@heroicons/vue/24/outline/index.js";

import {ref, onBeforeUnmount, watch} from "vue";

// External Libraries
import draggable from 'vuedraggable';
import DangerButton from "@/Components/DangerButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import CropperModal from "@/Components/Vue-Cropper/Partials/CropperModal.vue";
import {router, useForm} from "@inertiajs/vue3";
import { Switch } from '@headlessui/vue'
import CatalogCropper from "@/Components/Vue-Cropper/CatalogCropper.vue";

const props = defineProps({
    'card': {
        required: true
    }
})

const enabled = ref(props.card.catalog)
const switching = ref(false);
const deleting = ref(false);
const drag = ref(false); // A ref to track whether drag and drop mode is active
const images = ref(props.card.images);
const orderArray = ref([]); // A ref to store the order of information entries during drag and drop
const imageCropper = ref(null); // A ref to hold a reference to the image cropper component
const openCropperModal = ref(false); // A ref to control the visibility of the image cropper modal
const cardForm = useForm({
    id: props.card.id,
    image: [],
}); // A useForm instance for handling the card form data

// Run before the component is unmounted
onBeforeUnmount(() => {
  // Check if there are any changes in the order
  if (orderArray.value.length > 0) {
    // Update the order before unmounting
    updateOrder();
  }
});

watch(enabled, (newValue) => {
  if (switching.value === false) {
    updateSwitch(newValue);
  }
});

const updateSwitch  = async (status) => {
  if(switching.value === false){
    const uri = '/card/switchCatalog/' + props.card.id;
    const switchOptions = {
      status: status,
      onStart: () => {switching.value = true},
      onFinish: () => {switching.value = false},
      preserveState: false,
      preserveScroll: true,
      replace: true,
    };

    if (orderArray.value.length > 0) {
      const updateResult = await updateOrder();
      if (updateResult) {
        await router.put(uri, switchOptions);
      }
    } else {
      await router.put(uri, switchOptions);
    }
  }
}

// Function to update the order of items based on user's interaction
const handleItemOrderChange = () => {
    // Map through the information items and create an order array
    orderArray.value = images.value.map((item, index) => ({
        order: index,
        image_id: item.id
    }));
};

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
    if (cardForm.image.length <= 0) {
        return;
    }

    // Submit the card form using the PUT method
    cardForm.post('/card/catalog/addImage', {
        // Preserve the state of the form only if there are errors
        preserveState: (page) => Object.keys(page.props.errors).length,

        // Handle the success scenario
        onSuccess: () => {
            // Reset the image field after a successful submission
            cardForm.image = [];
        },
    });
}

const deleteImage = async (id) => {
  if(deleting.value === false){
    const uri = '/card/catalog/delete/' + id;
    const deleteOptions = {
      onStart: () => {deleting.value = true},
      onFinish: () => {deleting.value = false},
      preserveState: false,
      preserveScroll: true,
      replace: true,
    };

    if (orderArray.value.length > 0) {
      const updateResult = await updateOrder();
      if (updateResult) {
        await router.delete(uri, deleteOptions);
      }
    } else {
      await router.delete(uri, deleteOptions);
    }
  }
}

const updateOrder = async () => {
  return new Promise((resolve) => {
    router.patch('/card/catalog/updateOrder', { order: orderArray.value }, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onSuccess: () => {
        orderArray.value = [];
        resolve(true);
      },
    });
  });
}
</script>

<template>
    <!-- This is the page which users can order and edit their existed informations -->
    <Head title="Card" />

    <MainFrame>
        <!-- Back button -->
        <Link href="/card">
            <ArrowLeftOnRectangleIcon class="w-8 h-8 m-6 absolute" />
        </Link>

        <!-- Avatar and User Name Section -->
        <div class="flex justify-center">
            <Avatar :image="card.image" class="w-34 h-34"/>
        </div>
      <div class="flex justify-between items-center gap-2 mt-4 px-8">
        <button
            :class="{'bg-sky-800' : orderArray.length}"
            :disabled="!orderArray.length"
            class="w-full bg-blue-950 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition duration-300 cursor-pointer"
            @click="updateOrder()"
        >
          {{__("Save Changes")}}
        </button>
      </div>

      <div class="flex justify-between items-center mt-2">
          <div class="flex justify-center items-center">
              <PlusIcon
                  v-if="images.length < 12"
                  :disabled="!orderArray.length"
                  class="w-12 h-12 text-blue-200 font-bold py-2 rounded-lg hover:bg-blue-100 transition duration-300 cursor-pointer"
                  @click="openCropperModal = true"
              >
              </PlusIcon>
              <p class="text-gray-600 italic text-sm">{{ images.length }}/12</p>
          </div>


        <Switch
            v-model="enabled"
            :class="enabled ? 'bg-green-300' : 'bg-red-400'"
            class="relative inline-flex h-[30px] w-[68px] shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
        >
            <span
                aria-hidden="true"
                :class="enabled ? 'translate-x-9' : 'translate-x-0'"
                class="pointer-events-none inline-block h-[26px] w-[26px] transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
            />
        </Switch>
      </div>

        <!-- Information List Section -->
        <div class="px-2" @contextmenu.prevent>
            <ul>
                <draggable
                        :list="images"
                        group="information"
                        @start="drag = true"
                        @end="drag = false"
                        :delayOnTouchOnly="true"
                        :delay="200"
                        item-key="id"
                        :animation="150"
                        :dragClass="'draggedClass'"
                        :chosenClass="'chosenClass'"
                        @change="handleItemOrderChange"
                >
                    <template #item="{element, index}">
                      <div class="p-2 flex-col justify-center">
                        <!-- Edit App Item Component -->
                        <img :src="element.filename" class="rounded-lg w-full h-full no-context-menu">

                        <div class="flex justify-between items-center">
                          <p class="w-2/4 bg-blue-950 p-1 rounded-lg m-2 text-white cursor-pointer" @click="deleteImage(element.id)">{{ __('Delete') }}</p>
                          <ChevronUpDownIcon class="w-6 h-6"/>
                        </div>

                      </div>
                    </template>
                </draggable>
              <button
                  v-if="card.images.length>2"
                  :class="{'bg-sky-800' : orderArray.length}"
                  :disabled="!orderArray.length"
                  class="w-full bg-blue-950 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition duration-300 cursor-pointer"
                  @click="updateOrder()"
              >
                {{__("Save Changes")}}
              </button>
            </ul>
        </div>
    </MainFrame>
    <CropperModal :show="openCropperModal" @close="closeCropperModal">
        <CatalogCropper v-model="cardForm.image" @closeModal="closeCropperModal" ref="imageCropper"/>
        <div class="flex justify-between">
            <div>
                <DangerButton @click="cardForm.image = []" v-show="cardForm.image.length"> {{__("DISCARD")}} </DangerButton>
            </div>
            <PrimaryButton @click="clickedOk"> {{__("OK")}} </PrimaryButton>
        </div>
    </CropperModal>
</template>

<style scoped>
.draggedClass {
    opacity: 0%;
}
.chosenClass {
    background-color: #757696;
}
</style>
