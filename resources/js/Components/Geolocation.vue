<script setup>
// Composition API
import { ref, onMounted, onUnmounted } from 'vue'

// Resources
import { Loader } from "@googlemaps/js-api-loader"

// InertiaJS
import {useForm} from "@inertiajs/vue3";

// Heroicons
import {MapPinIcon, InformationCircleIcon} from "@heroicons/vue/24/solid/index.js";

// Custom components
import InputLabel from "@/Components/InputLabel.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";



const GOOGLE_MAPS_API_KEY = 'AIzaSyBzhHFJCvgxk4TB58mWT33ZD70POzBWVk0';
const loader = new Loader({
    apiKey: GOOGLE_MAPS_API_KEY,
    version: 'weekly',
    libraries: ['places']
});

const locationHolder = window._translations['Location Holder']; // A variable to hold the translated text for location
const mapDiv = ref(null); // A ref to hold the reference to the map DOM element
const loading = ref(false); // A ref to track the loading state (initially set to false)
let marker = ref(null); // A ref to hold a marker reference (initially set to null)
let map = ref(null); // A ref to hold a map reference (initially set to null)
let clickListener = null; // A variable to hold the reference for a click event listener on the map (initially set to null)


const props = defineProps({
    card_id: { required: true },
    address: { required: false },
    longitude: { required: false },
    latitude: { required: false }
});

const form = useForm({
    card_id: props.card_id,
    latitude: props.latitude ?? null,
    longitude: props.longitude ?? null,
    address: props.address ?? null
});

const emit = defineEmits(['close']);


/**
 * Perform actions when the component is mounted.
 */
onMounted(async () => {
    // Load Google Maps API using the loader
    await loader.load();

    // Check if longitude and latitude are not available in the form
    if (!form.longitude && !form.latitude) {
        // Fetch user's location if not available
        fetchLocation();
    } else {
        // Create a position object from form data
        const currPos = {
            lat: form.latitude,
            lng: form.longitude
        };

        // Initialize the map with the retrieved position
        initializeMap(currPos);
    }
});


/**
 * Clean up resources when the component is unmounted.
 */
onUnmounted(() => {
    // Check if a click listener exists
    if (clickListener) {
        // Remove the click listener to prevent memory leaks
        clickListener.remove();
    }
});


/**
 * Fetches the user's current location using geolocation API.
 */
async function fetchLocation() {
    // Show loading icon
    loading.value = true;

    try {
        // Get user's current position using geolocation API
        const position = await new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject);
        });

        // Create a position object with latitude and longitude
        const currPos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        // Initialize the map with the retrieved position
        initializeMap(currPos);
    } catch (error) {
        // Display an error alert if geolocation retrieval fails
        alert("Error getting user's location");

        // Create a fallback position
        const currPos = {
            lat: 36.83,
            lng: 34.64
        };

        // Initialize the map with the fallback position and zoom level
        initializeMap(currPos, 12);
    } finally {
        // Hide loading icon
        loading.value = false;
    }
}


/**
 * Initializes the Google Map with a given position and optional zoom level.
 *
 * @param {Object} position - The position to center the map on.
 * @param {number} [zoom=17] - The zoom level for the map (default is 17).
 */
function initializeMap(position, zoom = 17) {
    // Create a new Google Map instance
    map = new google.maps.Map(mapDiv.value, {
        center: position,
        zoom: zoom
    });

    // Create a marker at the specified position
    marker = new google.maps.Marker({
        position,
        map
    });

    // Add a click event listener to the map
    clickListener = map.addListener('click', ({ latLng: { lat, lng } }) => {
        // Check if a marker exists
        if (marker) {
            // Update marker position
            marker.setPosition({ lat: lat(), lng: lng() });
        } else {
            // Create a new marker
            marker = new google.maps.Marker({
                position: { lat: lat(), lng: lng() },
                map: map
            });
        }

        // Update form coordinates with marker position
        updateFormCoordinates(marker.position.lat(), marker.position.lng());
    });

    // Update form coordinates initially with marker position
    updateFormCoordinates(marker.position.lat(), marker.position.lng());
}


/**
 * Updates the form coordinates with the given latitude and longitude.
 *
 * @param {number} lat - The latitude to update.
 * @param {number} lng - The longitude to update.
 */
function updateFormCoordinates(lat, lng) {
    // Update the latitude and longitude in the form
    form.latitude = lat;
    form.longitude = lng;
}


/**
 * Update the location to the card if latitude and longitude are available.
 */
const addLocation = () => {
    // Check if latitude and longitude are available in the form
    if (form.latitude && form.longitude) {
        // Send a POST request to the 'card.location' route
        form.post(route('card.location'), {
            preserveScroll: true,
            // Define the success callback function
            onSuccess: () => emit('close'),
            // Define the finish callback function
            onFinish: () => form.reset(),
        });
    }
};

const geo_info = ref(false);
</script>


<template>
  <!-- This is the component that allows user to select their location -->
  <div ref="mapDiv" style="width: 100%; height: 45vh;" v-show="!loading"/>
  <div style="width: 100%; height: 45vh;" v-show="loading">
    <div class="loader"></div>
  </div>
    <div class="p-3">
        <InputLabel for="address" class="px-1.5">{{ __('Location Holder') }}</InputLabel>
        <textarea
            id="address"
            @keyup.enter="addLocation"
            maxlength="255"
            v-model="form.address"
            class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300"
            rows="2"
        />
        <div class="flex justify-center items-center mt-2 px-2">
            <p class="text-xs text-gray-800" :class="{'truncate w-2/4 opacity-25' : !geo_info }" @click="geo_info = !geo_info">
                <span v-show="geo_info">(</span>{{ __('geo_advise') }}<span v-show="geo_info">)</span>
            </p>
            <InformationCircleIcon class="w-4 h-4 text-blue-500 cursor-pointer" @click="geo_info = true" v-show="!geo_info"/>
        </div>
        <div class="flex justify-center">
            <button
                class="mt-2 bg-blue-50 p-2 rounded-lg flex justify-center items-center space-x-1 text-sm"
                @click="fetchLocation()"
            >
                <MapPinIcon class="w-5 h-5" />
                <span class="text-sm">{{ __('Press to Locate') }}</span>
            </button>
        </div>
    </div>
    <div class="px-2 mb-2 flex justify-between space-x-2" v-show="!loading">
        <SecondaryButton @click="$emit('close')" class="truncate"> {{__("Cancel")}} </SecondaryButton>
        <button
            class="inline-flex truncate items-center px-4 py-2 bg-green-300 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-green-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="addLocation"
        > {{__("Save")}} </button>
    </div>

</template>

<style scoped>
.loader {
    position: relative;
    width: 2.5em;
    height: 2.5em;
    transform: rotate(165deg);
}

.loader:before, .loader:after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    display: block;
    width: 0.5em;
    height: 0.5em;
    border-radius: 0.25em;
    transform: translate(-50%, -50%);
}

.loader:before {
    animation: before8 2s infinite;
}

.loader:after {
    animation: after6 2s infinite;
}

@keyframes before8 {
    0% {
        width: 0.5em;
        box-shadow: 1em -0.5em rgba(225, 20, 98, 0.75), -1em 0.5em rgba(111, 202, 220, 0.75);
    }

    35% {
        width: 2.5em;
        box-shadow: 0 -0.5em rgba(225, 20, 98, 0.75), 0 0.5em rgba(111, 202, 220, 0.75);
    }

    70% {
        width: 0.5em;
        box-shadow: -1em -0.5em rgba(225, 20, 98, 0.75), 1em 0.5em rgba(111, 202, 220, 0.75);
    }

    100% {
        box-shadow: 1em -0.5em rgba(225, 20, 98, 0.75), -1em 0.5em rgba(111, 202, 220, 0.75);
    }
}

@keyframes after6 {
    0% {
        height: 0.5em;
        box-shadow: 0.5em 1em rgba(61, 184, 143, 0.75), -0.5em -1em rgba(233, 169, 32, 0.75);
    }

    35% {
        height: 2.5em;
        box-shadow: 0.5em 0 rgba(61, 184, 143, 0.75), -0.5em 0 rgba(233, 169, 32, 0.75);
    }

    70% {
        height: 0.5em;
        box-shadow: 0.5em -1em rgba(61, 184, 143, 0.75), -0.5em 1em rgba(233, 169, 32, 0.75);
    }

    100% {
        box-shadow: 0.5em 1em rgba(61, 184, 143, 0.75), -0.5em -1em rgba(233, 169, 32, 0.75);
    }
}

.loader {
    position: absolute;
    top: calc(50% - 1.25em);
    left: calc(50% - 1.25em);
}
</style>
