<script setup>
// Custom Components
import MainFrame from "@/Components/Card/Frames/MainFrame.vue";
import Avatar from "@/Components/Card/Avatar.vue";
import AppItem from "@/Components/AppItem.vue";
import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";

// Vue Composition API
import { ref, onBeforeUnmount, computed } from "vue";

// Heroicons
import {TrashIcon, ArrowLeftOnRectangleIcon, DocumentDuplicateIcon, EyeIcon} from "@heroicons/vue/24/outline/index.js";

// Inertia.js
import {router, useForm} from "@inertiajs/vue3";

// External Libraries
import draggable from 'vuedraggable';
import TopLink from "@/Components/Card/TopLink.vue";

const props = defineProps({
    card: {
        required: true,
        type: Object
    },
    apps: {
        required: true,
        type: Object
    },
    cards: {
        required: true,
        type: Object
    }
})
const informationForm = useForm({
    title: null,
    value: null,
    card_id: props.card.id,
    app_id: null
});

const modalCategory = ref(); // Set the modal category based on the information's app category
const informations = ref(props.card.informations); // A ref to store the card's information entries
const orderArray = ref([]); // A ref to store the order of information entries during drag and drop
const drag = ref(false); // A ref to track whether drag and drop mode is active
const showingDuplicateModal = ref(false); // Boolean ref to track whether the duplicate modal is currently displayed
const showingImportModal = ref(false); // Boolean ref to track whether the import modal is currently displayed
const selectedCard = ref(); // Ref to store the selected card
const deleting = ref(false);
// Run before the component is unmounted
onBeforeUnmount(() => {
    // Check if there are any changes in the order
    if (orderArray.value.length > 0) {
        // Update the order before unmounting
        updateOrder();
    }
});


// Function to update the order of items based on user's interaction
const handleItemOrderChange = () => {
    // Map through the information items and create an order array
    orderArray.value = informations.value.map((item, index) => ({
        order: index,
        information_id: item.id
    }));
};

// Close the active modal and reset associated values and forms.
const closeModal = () => {
  // Reset the selected card value
  selectedCard.value = "";
  // Hide both import and duplicate modals
  showingImportModal.value = false;
  showingDuplicateModal.value = false;
  // Reset the information form
  informationForm.reset();
  informationForm.errors = [];
  // Reset the modal category to default (0)
  modalCategory.value = 0;
};

/**
 * Show the duplicate modal and populate it with information data.
 *
 * @param {Object} information - The information to duplicate.
 */
const showDuplicateModal = (information) => {
  // Set the modal category based on the information's app category
  modalCategory.value = information.app.category;
  // Set the flag to show the duplicate modal
  showingDuplicateModal.value = true;
  // Populate the information form with data
  informationForm.title = information.title;
  informationForm.value = information.value;
};

// Show the import modal and reset selected card value.
const showImportModal = () => {
  // Reset the selected card value
  selectedCard.value = "";
  // Set the flag to show the import modal
  showingImportModal.value = true;
};
/**
 * Delete an information entry.
 *
 * @param {number} id - The ID of the information entry to be deleted.
 */
const deleteInfo = async (id) => {
  if(deleting.value === false){
    const uri = '/information/' + id;
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


/**
 * Update the order of information entries.
 *
 * @returns {Promise<boolean>} A promise that resolves to true after successful order update.
 */
const updateOrder = async () => {
    return new Promise((resolve) => {
        router.patch('/information', { order: orderArray.value }, {
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

/**
 * Duplicate information form data and send a POST request to store it.
 */
const duplicateInfo = () => {
  // Send a POST request to the 'information.store' route
  informationForm.post(route('information.store'), {
    // Preserve form state based on the number of errors
    preserveState: (page) => Object.keys(page.props.errors).length,
    // Preserve scroll position
    preserveScroll: true,
  });
}

/**
 * Compute and return a filtered list of applications based on the selected category.
 *
 * @return {Array} The filtered list of applications.
 */
const filteredApps = computed(() => {
  if (modalCategory.value === 0) {
    // Display all apps if "All" is selected
    return props.apps;
  } else {
    // Filter apps based on the selected category
    return props.apps.filter(app => app.category === modalCategory.value);
  }
});

/**
 * Import data from a selected card, if available.
 *
 * @return {Promise<void>} A promise indicating the completion of the import process.
 */
const importFrom = async () => {
  if (!!selectedCard.value) {
    const uri = '/card/importFrom';
    const patchOptions = {
      data: {
        card_id: props.card.id,
        other_card_id: selectedCard.value,
      },
      method: 'post',
      preserveState: false,
      preserveScroll: true,
      replace: true,
    };

    if (orderArray.value.length > 0) {
      // Update the order before importing, if necessary
      const updateResult = await updateOrder();

      if (updateResult) {
        // Visit the specified URI with patchOptions
        await router.visit(uri, patchOptions);
      }
    } else {
      // Visit the specified URI with patchOptions
      await router.visit(uri, patchOptions);
    }
  } else {
    // Close the modal if no card is selected
    closeModal();
  }
};

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
            <Avatar :image="card.image"/>
        </div>

        <!-- Stretched Button Section -->
        <div class="px-6 py-4 flex justify-center space-x-1">
            <button
                    :class="{'shake bg-blue-950' : orderArray.length}"
                    :disabled="!orderArray.length"
                    class="text-sm w-3/4 bg-black text-white font-bold py-2 rounded-lg hover:bg-blue-950 transition duration-300 cursor-pointer"
                    @click="updateOrder()"
            >
                {{__("Save Changes")}}
            </button>
            <button
                class="bg-black text-white p-2 rounded-lg hover:bg-blue-950 transition duration-300 cursor-pointer"
                @click="showImportModal"
            >
                <document-duplicate-icon class="w-4 h-4" />
            </button>
        </div>

        <!-- Information List Section -->
        <div class="px-2">
            <ul>
              <TopLink :href="'/nfc/'+card.slug">
                <EyeIcon class="w-5 h-5" />
                <span class="text-sm">{{ __('Show') }}</span>
              </TopLink>
                <draggable
                    :list="informations"
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
                        <div class="p-2 my-2 bg-gray-100 rounded-lg cursor-grab flex justify-between items-center">
                            <!-- Edit App Item Component -->
                            <AppItem :is-edit="true" :card_id="card.id" :app="element.app" :information="element"/>

                            <!-- Chevron Icon for Reordering and Delete Icon -->
                            <div class="flex items-center space-x-5">
                                <DocumentDuplicateIcon class="w-5 h-5 opacity-50" @click="showDuplicateModal(element)"/>
                                <TrashIcon class="w-6 h-6 text-blue-300 cursor-pointer hover:scale-125 transition duration-200" @click="deleteInfo(element.id)"/>
                            </div>
                        </div>
                    </template>
                </draggable>
            </ul>
        </div>
    </MainFrame>

  <Modal :show="showingDuplicateModal" @close="closeModal">
    <!-- Modal Content -->
    <div class="p-6">
      <!-- Information Display -->
      <div class="p-6 text-center italic text-sm text-gray-400">
        {{ informationForm.title }} <span v-if="informationForm.title">-</span> {{ informationForm.value }}
      </div>

      <!-- Duplicate Header -->
      <h2 class="text-lg font-medium text-gray-900 text-center">
        {{ __("Duplicate With") }}
      </h2>

      <!-- Display Validation Errors -->
      <InputError :message="informationForm.errors.value" class="mt-2 text-sm text-center" />

      <!-- App Selection Section -->
      <div class="flex justify-center flex-wrap lg:gap-x-4 gap-x-3 overflow-y-auto h-72">
        <!-- Loop through filtered apps -->
        <div v-for="app in filteredApps">
          <div class="flex flex-col items-center justify-center m-2">
            <!-- App Icon -->
            <label :for="app.id">
              <div class="p-1 bg-zinc-50 rounded-lg text-center flex flex-col items-center justify-center square-icon cursor-pointer shadow-md shadow-gray-200">
                <img :src="'/storage/images/apps/' + app.icon" class="rounded-lg w-full h-full no-context-menu" alt="App Icon">
              </div>
            </label>

            <!-- App Title and Radio Input -->
            <div class="text-center mt-1" style="width: 64px;">
              <p class="truncate text-xs font-bold">{{ app.title }}</p>
              <input type="radio" name="item" class="form-radio text-blue-500" @click="informationForm.app_id = app.id" :id="app.id"/>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Buttons -->
      <div class="flex justify-between">
        <div>
          <!-- Cancel Button -->
          <DangerButton @click="closeModal">{{ __("Cancel") }}</DangerButton>
        </div>
        <div>
          <!-- OK Button -->
          <PrimaryButton
              :class="{ 'opacity-25': informationForm.processing }"
              :disabled="informationForm.processing"
              @click="duplicateInfo"
          >{{ __("OK") }}</PrimaryButton>
        </div>
      </div>
    </div>
  </Modal>

  <Modal :show="showingImportModal" @close="closeModal">
    <!-- Modal Content -->
    <div class="p-6">
        <!-- Title for Import Modal -->
        <h2 v-if="cards.length > 1" class="font-bold text-gray-900 text-center mb-2">
            {{ __("Import from this card") }}
        </h2>
        <h3 v-else class="font-bold text-gray-900 text-center mb-2">
            {{ __("This place looks quite empty") }}
        </h3>

      <!-- List of Cards to Choose From -->
      <div class="px-2">
        <ul>
          <!-- Loop through 'cards' array -->
          <div v-for="item in cards">
            <!-- Display a card option if it's not the current card being imported from -->
            <div v-if="item.id !== card.id" class="p-2 my-2 bg-gray-100 rounded-lg">
              <label :for="item.id">
                <!-- Card Selection -->
                <div class="flex justify-between items-center">
                  <div class="flex items-center space-x-4 truncate">
                    <!-- Radio Button for Card Selection -->
                    <input type="radio" name="card" class="form-radio text-blue-500" @click="selectedCard = item.id" :id="item.id"/>
                    <!-- Display the card's title -->
                    <span class="truncate">{{ item.title }}</span>
                  </div>
                  <div class="flex">
                    <!-- Display the card's avatar (image) -->
                    <avatar class="w-9 h-9" :image="item.image" />
                  </div>
                </div>
              </label>
            </div>
          </div>
        </ul>
      </div>

      <!-- Modal Buttons (Cancel and OK) -->
      <div class="flex justify-between">
        <div>
          <!-- Cancel Button -->
          <DangerButton @click="closeModal">{{ __("Cancel") }}</DangerButton>
        </div>
        <div>
          <!-- OK Button for Import -->
          <PrimaryButton @click="importFrom">{{ __("OK") }}</PrimaryButton>
        </div>
      </div>
    </div>
    <!-- End of Modal Content -->
  </Modal>
</template>

<style>
.draggedClass {
    opacity: 0%;
}
.chosenClass {
    background-color: #757696;
}
.shake {
    animation: shake 5s infinite;
}
.square-icon {
    width: 55px;
    height: 55px;
}
@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-2px);
    }
    20%, 40%, 60%, 80% {
        transform: translateX(2px);
    }
}
</style>
