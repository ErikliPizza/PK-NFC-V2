<script setup>
import MainTemplate from "@/Components/Apps/template/MainTemplate.vue";
import {useClipboard} from "@/Composables/useClipboard.vue";
import {ref} from "vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import {ClipboardIcon} from "@heroicons/vue/24/outline/index.js";
import Modal from "@/Components/Modal.vue";

const { copyToClipboard } = useClipboard();

defineProps({
    information: {
        required: true
    }
})

const modal = ref(false); // Ref to control the visibility of the billing modal

const closeModal = () => { modal.value = false; }; // Function to close the billing modal
const openModal = () => { modal.value = true; }; // Function to open the billing modal
</script>

<template>
  <div
      @click="openModal"
      class="flex space-x-1 items-center truncate">
    <MainTemplate :value="information.value" :title="information.title" :icon="information.app.icon" :app_title="information.app.title"/>
  </div>

  <Modal :show="modal" @close="closeModal">
    <!-- Modal Content -->
    <div class="p-6">
      <!-- Location Title -->


      <div class="w-16 h-16 rounded-full flex items-center justify-center text-gray-800 border-gray-200 border-2" v-if="information.extra">
        <span class="text-xs">{{ information.extra }}</span>
      </div>
      <div class="flex justify-center" @click="copyToClipboard(information.title, information.app.title)" v-if="information.title">
        <p class="text-lg font-medium text-gray-500 cursor-pointer text-center break-words">{{ information.title }}</p>
        <ClipboardIcon class="w-5 h-5 p-1 opacity-50"/>
      </div>


      <div class="flex justify-center mt-4" @click="copyToClipboard(information.value, information.app.title)">
        <p class="font-medium text-gray-700 cursor-pointer text-center truncate px-2">{{ information.value }}</p>
        <ClipboardIcon class="w-5 h-5 p-1 opacity-50"/>
      </div>

      <!-- Modal Close Button -->
      <div class="mt-6 flex justify-between">
        <img :src="'/storage/images/apps/'+information.app.icon" class="w-10 h-10 no-context-menu" alt="App Icon">
        <SecondaryButton @click="closeModal"> {{__("OK")}} </SecondaryButton>
      </div>
    </div>
    <!-- End of Modal Content -->
  </Modal>


</template>

<style scoped>

</style>
