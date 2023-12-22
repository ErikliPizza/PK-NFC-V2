<script setup>
// Vue Components
import Avatar from "@/Components/Card/Avatar.vue";
import Qur from "@/Components/Qur.vue";
import MainFrame from "@/Components/Card/Frames/MainFrame.vue";
import AvatarFrame from "@/Components/Card/Frames/AvatarFrame.vue";

// Vue Composition API
import { ref, onMounted } from 'vue';

import 'vue3-carousel/dist/carousel.css'
import { Carousel, Slide, Pagination } from 'vue3-carousel'

// Partials
import TopMenuSection from "@/Pages/Nfc/Partials/TopMenuSection.vue";
import CategoryAndAppSection from "@/Pages/Nfc/Partials/CategoryAndAppSection.vue";
import ListPdf from "@/Components/ListPdf.vue";
import CustomModal from "@/Components/CustomModal.vue";
import {ShareIcon} from "@heroicons/vue/24/outline/index.js";
import Snowflake from "@/Components/Snowflake.vue";

const numSnowflakes = ref(15);

// Custom Composables
import { useWhatsapp } from "@/Composables/useWhatsapp.vue";
import TopButton from "@/Components/Card/TopButton.vue";
import XmasButton from "@/Components/XmasButton.vue";

// Composables
const { shareWith } = useWhatsapp();

const load = ref(false);
onMounted(() => {
    load.value = true;
})

const qr = ref();
const props = defineProps({
    card: {
        required: true,
        type: Object
    },
})

const isCollapsed = ref(props.card.catalog);

function toggleCollapse() {
  isCollapsed.value = !isCollapsed.value;
}

const isZoomed = ref(false);

function toggleZoom() {
  isZoomed.value = !isZoomed.value;
}

const pdfModal = ref(false); // Ref to control the visibility of the pdf modal
const closePdfModal = () => { pdfModal.value = false; }; // Function to close the billing modal
const openPdfModal = () => { pdfModal.value = true; }; // Function to open the billing modal

/**
 * Opens WhatsApp with a message containing the card's title and current URL.
 */
function openWhatsApp() {
    // Create a new URL object based on the current window location
    const currentURL = new URL(window.location.href);

    // Remove any query parameters from the URL
    currentURL.search = '';

    // Create a message containing the card's title and URL
    const message = encodeURIComponent(`${props.card.title} - ${currentURL.href}`);

    // Call the shareWith function from the useWhatsapp composable
    shareWith(message);
}
</script>

<template>
    <!-- This is the main page for NFC Card showcase -->

    <Head title="NFC" ref="content"/>

  <div class="overlay" v-if="isZoomed" @click="toggleZoom">
  </div>
  <MainFrame>
      <Snowflake v-for="n in numSnowflakes" :key="n" />

      <transition name="main-fade">
      <div v-if="load">
          <div class="flex justify-between items-center space-x-2 px-2 mb-3.5">
              <XmasButton v-if="card.images.length>0" @click="toggleCollapse" >
                  {{__('Catalog')}}
              </XmasButton>
              <XmasButton @click="openPdfModal" v-if="card.pdfs.length > 0">
                  {{ __('PDF') }}
              </XmasButton>
          </div>
          <div v-if="card.images.length>0" class="mb-3.5">
              <Carousel v-if="isCollapsed" :autoplay="9000" :wrap-around="true">
                  <Slide v-for="slide in card.images" :key="slide.id">
                      <div class="carousel__item p-1">
                          <img :src="slide.filename" class="rounded-sm border border-gray-700" alt="Cover Image">
                      </div>
                  </Slide>

                  <template #addons>
                      <Pagination />
                  </template>
              </Carousel>
          </div>
        <!-- Avatar and QR -->
          <AvatarFrame>

            <span :class="{ 'relative inline-block me-6': !isZoomed}">
    <img class="h-20 w-20 rounded-md" :src="card.image"/>
                        <div class="absolute top-1.5 left-1 transform -translate-y-1/2 -translate-x-1/2">
            <img src="https://res.cloudinary.com/freecodez/image/upload/v1701705719/images/guidvrtf8kre7pc3jdk5.webp" alt="Christmas Cap" class="h-8 w-8"> <!-- Adjust size as needed -->
        </div>
    <span class="absolute bottom-0 right-0 block translate-x-1/2 translate-y-1/2 transform rounded-full border-2 border-white">
        <div class="bg-white p-2 rounded-full block cursor-pointer" @click="openWhatsApp">
        <ShareIcon class="w-4 h-4"/>
        </div>
    </span>
  </span>



              <div class="zoom-container" :class="
              { 'z-50 zoomed flex items-center justify-center fixed': isZoomed,
                'ms-6': !isZoomed
              }"
                   @click="toggleZoom">
                  <Qur ref="qr" :title="card.title" />
              </div>
          </AvatarFrame>
        <!-- Avatar and QR -->


        <!-- Add Contact, Download QR, Show on Maps, Edit buttons and Google Maps iFrame modal -->
        <TopMenuSection :card="card" @downloadQR="qr.onDownload()"/>
        <!-- Add Contact, Download QR, Share buttons -->

        <div class="m-2 flex justify-center">
          <hr class="sep-2 w-2/4">
        </div>

        <!-- Category Buttons, Listed Apps -->
        <CategoryAndAppSection :informations="card.informations" />
        <!-- Category Buttons, Listed Apps -->

      </div>
    </transition>
      <CustomModal :show="pdfModal" @close="closePdfModal">
          <!-- Modal Content -->
          <div class="p-6">
              <ListPdf :card="card"/>
          </div>
      </CustomModal>
  </MainFrame>


</template>

<style scoped>
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vh;
  height: 120vh;
  background-color: black; /* Set the background color to match your project */
  z-index: 30; /* Ensure the backdrop is above everything else */
}
.zoom-container {
  transition: transform 0.3s ease;
  transform-origin: center center;
}

.zoomed {
    transform: scale(3);
}
hr.sep-2 {
    border: 0;
    height: 1px;
    background-image: linear-gradient(to right, #f0f0f0, #333030, #333030FF, #f0f0f0);
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
.carousel__item {
  width: 100%; /* Set a fixed width for the carousel items */
  height: 100%; /* Default height for desktop */
  display: flex;
  align-items: center;
  justify-content: center;
}

.carousel__item img {
  width: 100%; /* Make the image fill the width of the container */
  height: 100%; /* Make the image fill the height of the container */
  object-fit: cover; /* Ensure the image covers the container without stretching */
}


</style>
