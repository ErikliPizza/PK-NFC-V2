<script setup>
import {ChatBubbleLeftRightIcon} from "@heroicons/vue/24/outline/index.js";

defineProps({
    icon: {
        required: true,
        type: String
    },
    title: {
        required: true,
        type: String
    }
})
</script>

<template>
    <button class="bg-slate-50 p-1.5 rounded-lg text-center text-sm flex flex-col items-center justify-center hover-child:scale-150 transition duration-150 ease-in-out">
        <div class="flex items-center justify-center">
            <img :src="'/storage/images/categories/' + icon" class="w-6 h-6 cursor-pointer" alt="Category Icon">
        </div>
        <span class="block">{{ __(title) }}</span>
    </button>
</template>

<style scoped>
/* Custom CSS to target the image and apply the scale transformation */
.hover-child\:scale-150:hover > div > img {
    transition: 0.2s;
    transform: scale(1.5);
}
</style>
