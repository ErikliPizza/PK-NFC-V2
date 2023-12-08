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
import {usePage} from "@inertiajs/vue3";

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
</script>

<template>
    <!-- This is the main page for NFC Card showcase -->

    <Head title="NFC" ref="content"/>

  <div class="overlay" v-if="isZoomed" @click="toggleZoom">

  </div>
  <MainFrame>
    <transition name="main-fade">
      <div v-if="load">
          <div class="flex justify-between items-center space-x-2 px-2">
              <button v-if="card.images.length>0" @click="toggleCollapse" class="bg-blue-50 p-1.5 rounded-lg flex justify-center w-full items-center space-x-1 mb-2 text-xs">
                  {{__('Catalog')}}
              </button>
              <button @click="openPdfModal" v-if="card.pdfs.length > 0" class="bg-blue-50 p-1.5 rounded-lg flex justify-center w-full items-center space-x-1 mb-2 text-xs">
                  {{ __('PDF') }}
              </button>
          </div>
          <div  v-if="card.images.length>0">
              <Carousel v-if="isCollapsed" :autoplay="9000" :wrap-around="true">
                  <Slide v-for="slide in card.images" :key="slide.id">
                      <div class="carousel__item">
                          <img :src="slide.filename" alt="Cover Image">
                      </div>
                  </Slide>

                  <template #addons>
                      <Pagination />
                  </template>
              </Carousel>
          </div>
        <!-- Avatar and QR -->
        <AvatarFrame>
          <div class="flex items-center">
            <Avatar :image="card.image"/>
          </div>
          <div class="flex-col items-center zoom-container" :class="{ 'zoomed': isZoomed }" @click="toggleZoom">
              <p class="text-gray-600 text-sm text-center" v-show="isZoomed">{{__('Click to close')}}</p>
            <Qur ref="qr" :title="card.title" />
          </div>
        </AvatarFrame>
        <!-- Avatar and QR -->


        <!-- Add Contact, Download QR, Share, Show on Maps, Edit buttons and Google Maps iFrame modal -->
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
  width: 100vw;
  height: 100vh;
  background-color: black; /* Set the background color to match your project */
  z-index: 999; /* Ensure the backdrop is above everything else */
}
.zoom-container {
  transition: transform 0.3s ease;
  transform-origin: center center;
}

.zoomed {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) scale(3);
  z-index: 1000;
}
hr.sep-2 {
    border: 0;
    height: 1px;
    background-image: linear-gradient(to right, #f0f0f0, #dbeafe, #dbeafe, #f0f0f0);
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
