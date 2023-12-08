<script setup>
import { Cropper } from 'vue-advanced-cropper';
import Wrapper from "@/Components/Vue-Cropper/Partials/Wrapper.vue";
import 'vue-advanced-cropper/dist/style.css';
import {ref} from "vue";
import VerticalButtons from "@/Components/Vue-Cropper/Partials/VerticalButtons.vue";
import SquareButton from "@/Components/Vue-Cropper/Partials/SquareButton.vue";
import Swal from 'sweetalert2'
import {ArrowUpTrayIcon, TrashIcon, PlusCircleIcon} from "@heroicons/vue/24/outline/index.js";
import Flip from "@/Components/SVG/Flip.vue";
import Rotate from "@/Components/SVG/Rotate.vue";
import Maximize from "@/Components/SVG/Maximize.vue";


const props = defineProps({
    modelValue: {
        type: Array,
        required: true
    }
})


const file = ref(null);
const isCropper = ref();
const minWidth = 480;
const minHeight = 480;



const image = ref({
    src: null,
    type: null,
    x: null,
    y: null
});


function defaultPosition() {
    return {
        left: 100,
        top: 100,
    };
}
function defaultSize({ imageSize, visibleArea }) {
    return {
        width: (visibleArea || imageSize).width,
        height: (visibleArea || imageSize).height,
    };
}
function percentsRestriction() {
    return {
        minWidth: minWidth,
        minHeight: minHeight,
    };
}
function flip(x,y) {
    const { image } = isCropper.value.getResult();
    if (image.transforms.rotate % 180 !== 0) {
        isCropper.value.flip(!x, !y);
    } else {
        isCropper.value.flip(x, y);
    }
}
function rotate(angle) {
    isCropper.value.rotate(angle);
}

function maximize() {
    const center = {
        left: isCropper.value.coordinates.left + isCropper.value.coordinates.width / 2,
        top: isCropper.value.coordinates.top + isCropper.value.coordinates.height / 2,
    };
    isCropper.value.setCoordinates([
        ({ imageSize }) => ({
            width: imageSize.width,
            height: imageSize.height,
        }),
        ({ coordinates }) => ({
            left: center.left - coordinates.width / 2,
            top: center.top - coordinates.height / 2,
        }),
    ]);
}

// This function is used to detect the actual image type
function getMimeType(file, fallback = null) {
    const byteArray = (new Uint8Array(file)).subarray(0, 4);
    let header = '';
    for (let i = 0; i < byteArray.length; i++) {
        header += byteArray[i].toString(16);
    }
    switch (header) {
        case "89504e47":
            return "image/png";
        case "47494638":
            return "image/gif";
        case "ffd8ffe0":
        case "ffd8ffe1":
        case "ffd8ffe2":
        case "ffd8ffe3":
        case "ffd8ffe8":
            return "image/jpeg";
        default:
            return fallback;
    }
}

function loadImage(event) {
    // Reference to the DOM input element
    const { files } = event.target;
    // Ensure that you have a file before attempting to read it
    if (files && files[0]) {
        // 1. Revoke the object URL, to allow the garbage collector to destroy the uploaded file before
        if (image.value.src) {
            alert('sa');
            URL.revokeObjectURL(image.value.src);
        }
        // 2. Create the blob link to the file to optimize performance
        const blob = URL.createObjectURL(files[0]);

        // Create a new FileReader to read this image binary data
        const reader = new FileReader();
        // Define a callback function to run when FileReader finishes its job
        reader.onload = (e) => {
            // Note: arrow function used here, so that "this.image" refers to the image of Vue component
            const newImage = {
                // Set the image source (it will look like blob:http://example.com/2c5270a5-18b5-406e-a4fb-07427f5e7b94)
                src: blob,
                // Determine the image type to preserve it during extracting the image from canvas
                type: getMimeType(e.target.result, files[0].type)
            };

            // Check if the image type is PNG or JPEG
            if (newImage.type !== "image/png" && newImage.type !== "image/jpeg") {
                // Perform the necessary action (e.g., show an error message, reset the image, etc.)
                // In this example, we'll reset the image
                resetImage();
                URL.revokeObjectURL(blob);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please upload a PNG or JPEG image.',
                });
                return; // Stop the uploading process
            }

            // Create a new Image object to get the image dimensions
            const img = new Image();
            img.onload = () => {
                const updatedImage = {
                    ...newImage,
                    x: img.width,
                    y: img.height
                };
                // Check if the image dimensions meet your requirements
                if (updatedImage.x < minWidth || updatedImage.y < minHeight) {
                    resetImage();
                    URL.revokeObjectURL(blob);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: `Image must be at least ${minWidth}x${minHeight}, given: ${updatedImage.x}x${updatedImage.y}`,
                    });
                } else {
                    image.value = updatedImage;
                }
            };
            img.src = blob;

            function resetImage() {
                image.value = {
                    src: null,
                    type: null,
                    x: null,
                    y: null
                };
            }
        };
        // Start the reader job - read file as a data URL (base64 format)
        reader.readAsArrayBuffer(files[0]);
    }
}

function destroyed() {
    // Destroy the created images
    if (image.value && image.value.src instanceof File) {
        URL.revokeObjectURL(image.value.src);
    }
    // Clear the file input value
    if (file.value) {
        file.value.value = '';
    }
    // Reset the image object
    image.value = {
        src: null,
        type: null,
        x: null,
        y: null,
    };

    // Reset the cropper
    if (isCropper.value) {
        isCropper.value.reset();
    }
}
function save() {
    if (props.modelValue.length >= 1) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "You can add maximum 1 image",
        });
    } else {
        let uploaded = isCropper.value.getCanvas().toDataURL('image/jpeg');
        if (uploaded.length>10) {
            props.modelValue.push({
                src: uploaded
            });
            destroyed();
        }
    }
}
defineExpose({
    save
})
</script>

<template>
    <Wrapper class="cont">
        <cropper
            background-class="opacity-0"
            :src="image.src"
            :size-restrictions-algorithm="percentsRestriction"
            :stencil-props="{aspectRatio: 1}"
            :default-position="defaultPosition"
            :default-size="defaultSize"
            :auto-zoom="false"
            ref="isCropper"
        />

        <Transition name="fade" mode="out-in">
            <div v-show="!image.src && modelValue.length<1" class="flex flex-col justify-center items-center absolute">
                <ArrowUpTrayIcon class="w-full h-full text-gray-500 cursor-pointer" @click="$refs.file.click()" />
                <input type="file" hidden ref="file" @change="loadImage($event)" accept="image/*">
            </div>
        </Transition>

        <Transition name="fade" mode="out-in">
            <div class="flex justify-center items-center" v-if="modelValue.length>=1">
              <img
                  :src="modelValue[0].src"
                  alt="Profile Photo"
                  class="rounded-full border-2 border-indigo-300 hover:scale-110 cursor-pointer transition duration-150 ease-in-out">
            </div>
        </Transition>

        <transition name="fade" >
            <vertical-buttons>
                <section v-if="image.src">
                    <square-button title="Rotate Clockwise" @click="rotate(90)" v-if="image.y>=minWidth">
                        <Rotate class="w-7 h-7 text-black" />
                    </square-button>
                    <square-button class="text-red-500" title="Maximize" @click="maximize()">
                        <Maximize class="w-7 h-7 text-black" />
                    </square-button>
                    <square-button title="Flip Horizontal" @click="flip(true, false)">
                        <Flip class="w-7 h-7 text-black" />
                    </square-button>
                    <square-button title="Flip Vertical" @click="flip(false, true)">
                        <Flip class="w-7 h-7 text-black -rotate-90" />
                    </square-button>
                    <square-button class="mt-3" title="Unset" @click="destroyed()" v-if="image.src">
                        <TrashIcon class="w-6 h-6 text-violet-400" />
                    </square-button>
                    <square-button class="mt-1" title="Save" @click="save()" v-if="image.src">
                        <PlusCircleIcon class="shake w-8 h-8 text-lime-200 hover:text-green-600 duration-300" />
                    </square-button>
                </section>
            </vertical-buttons>
        </transition>
    </Wrapper>
</template>

<style scoped>

.cont {
    height: 80vh; /* Set initial height to 90% of viewport height */
    display: flex;
    justify-content: center;
    align-items: center;

    @media (max-width: 767px) {
        height: 55vh; /* Adjust height for smaller devices */
    }

    @media (min-width: 768px) and (max-width: 1023px) {
        height: 60vh; /* Adjust height for medium-sized devices */
    }

    @media (min-width: 1024px) {
        height: 63vh; /* Adjust height for larger devices */
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: all 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translatey(30px);
}

.shake {
  animation: shake 2.5s infinite;
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
