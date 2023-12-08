<script setup>
// Composition API
import {nextTick, ref, watch} from "vue";

// Custom components
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import Modal from "@/Components/Modal.vue";
import InformationText from "@/Components/InformationText.vue";
import AppItemIcon from "@/Components/AppItemIcon.vue";

// InertiaJS
import {useForm} from "@inertiajs/vue3";

// Heroicons
import {PlusCircleIcon} from "@heroicons/vue/24/solid/index.js";
import {ChevronUpDownIcon} from "@heroicons/vue/24/outline/index.js";
import InputLabel from "@/Components/InputLabel.vue";


const titleHolder = window._translations['Title Holder']; // A variable to hold the translated text for location

// State
const addInformation = ref(false); // A ref to track whether the information modal is on/off
const informationInput = ref(null); // A ref to hold a reference to the information input element
const titleInput = ref(null); // A ref to hold a reference to the title input element
const isValid = ref(true); // A ref to track the validity of the form

// Props
const props = defineProps({
  isEdit: {
    required: true,
    type: Boolean
  },
  app: {
    required: true,
    type: Object
  },
  card_id: {
    required: true
  },
  information: {
    required: false,
    type: Object
  }
});

// Form setup
const informationForm = useForm({
    id: props.information?.id ?? null,
    title: props.information?.title ?? null,
    value: props.information?.value ?? null,
  extra: props.information?.extra ?? null,
    card_id: props.card_id,
    app_id: props.app.id
});

// Methods
// Method to open the modal for adding/editing information
const openModal = () => {
  // Set the state to indicate the modal is open
  addInformation.value = true;
  // Use nextTick to focus on the title input after rendering
  nextTick(() => titleInput.value.focus());
};

// Method to close the modal for adding/editing information
const closeModal = () => {
  // Set the state to indicate the modal is closed
  addInformation.value = false;
  // Reset the information form
  informationForm.reset();
};

// Method to handle sending the request to update or store information
const sendRequest = () => {
  // Check if the component is in edit mode
  if (props.isEdit) {
    // Check if form data is valid and has changed
    if (isValid.value && !!informationForm.value &&
        (informationForm.value !== props.information.value ||
            informationForm.title !== props.information.title ||
                informationForm.extra !== props.information.extra)) {
      // Send a PUT request to update information
      informationForm.put(route('information.update'), {
        preserveState: page => Object.keys(page.props.errors).length,
        preserveScroll: true,
        onSuccess: closeModal,
        onError: () => informationInput.value.focus(),
        onFinish: informationForm.reset
      });
    }
  } else {
    // Check if form data is valid
    if (isValid.value && !!informationForm.value) {
      // Send a POST request to store new information
      informationForm.post(route('information.store'), {
        preserveScroll: true,
        onSuccess: closeModal,
        onError: () => informationInput.value.focus()
      });
    }
  }
};

// Method to extract information using the provided regex
const extractInformation = text => {
  const match = text.match(props.app.regex);
  return !!match;
};

// Watcher to update the validity state based on form value changes
watch(informationForm, newValue => {
  if (!!newValue.value) {
    // Update the validity state using the extracted information
    isValid.value = extractInformation(newValue.value);
  }
});
</script>

<template>
  <!-- This is the component that allows user to add/edit their information -->

  <!-- if it is edit item-->
    <div v-if="isEdit"  class="flex items-center w-3/4">
        <ChevronUpDownIcon class="w-4 h-4 opacity-50"/>
        <img :src="'/storage/images/apps/'+app.icon" class="w-10 h-10 no-context-menu" alt="App Icon" @click="openModal">
        <div class="flex flex-col cursor-pointer truncate ms-2 pe-4 w-3/4">
            <p class="text-sm font-bold text-gray-800 truncate" @click="openModal">{{ information.value }}</p>
            <p class="text-gray-500 text-sm" v-if="information.title">({{ information.title.toLocaleUpperCase() }})</p>
        </div>
    </div>
  <!-- if it is add item-->
  <AppItemIcon v-else :icon="app.icon" @click="openModal"/>

  <Modal :show="addInformation" @close="closeModal">
    <div class="p-6">
      <div class="flex justify-center items-center space-x-2">
          <div>
              <InputLabel for="title">{{__('Title')}}</InputLabel>
              <TextInput
                  maxlength="30"
                  ref="titleInput"
                  id="title"
                  v-model="informationForm.title"
                  type="text"
                  class="mt-1 block w-full"
                  :placeholder="titleHolder"
                  @keyup.enter="sendRequest"
              />
          </div>
          <div v-if="app.category_id === 3">
              <InputLabel for="extra">{{__('Currency')}}</InputLabel>
              <TextInput
                  maxlength="30"
                  id="extra"
                  v-model="informationForm.extra"
                  type="text"
                  class="mt-1 block w-full"
                  placeholder="EUR/BTC/ETH"
                  @keyup.enter="sendRequest"
              />
          </div>
      </div>

      <div class="flex mt-3 justify-center">
        <span class="cursor-default flex items-center text-sm px-1 border-2 border-gray-900 truncate-prefix bg-gray-50 rounded-l-md fix" v-if="app.prefix && (app.component !== 'bank' && app.component !== 'cryptoc')">{{ app.prefix }}</span>
        <InformationText
            maxlength="255"
            ref="informationInput"
            id="value"
            v-model="informationForm.value"
            type="text"
            class="w-2/4 px-3 py-2"
            :placeholder="app.placeholder"
            @keyup.enter="sendRequest"
        />
        <span class="cursor-default flex items-center text-sm px-1 border-2 border-gray-900 truncate bg-gray-50 rounded-r-md fix" v-if="app.suffix">{{ app.suffix }}</span>
      </div>

      <div class="flex justify-between mt-3">
        <img :src="'/storage/images/apps/'+app.icon" class="w-10 h-10 no-context-menu" alt="App Icon">

        <span v-show="isValid === false" class="text-red-600 text-sm">{{__('Invalid Data Structure')}}</span>
        <InputError :message="informationForm.errors.value" class="mt-2 text-sm" />
        <PlusCircleIcon
            class="w-10 h-10 cursor-pointer"
            :class="{ 'opacity-25': informationForm.processing }"
            :disabled="informationForm.processing"
            @click="sendRequest"
        />
      </div>
    </div>
  </Modal>
</template>

<style scoped>
.truncate-prefix {
    display: flex;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    justify-content: flex-end;

    /* Add these lines for scrolling */
    cursor: pointer;
    position: relative;
}

.fix:hover {
    overflow: visible; /* Show full text on hover */
    white-space: normal;
}
</style>
