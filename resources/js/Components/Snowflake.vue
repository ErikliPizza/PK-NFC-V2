<template>
    <span class="snowflake" :style="style">{{ content }}</span>
</template>

<script setup>
import {ref} from 'vue';

const props = defineProps({
    content: {
        type: String,
        default: '❄'
    }
});

const style = ref({
    animationDuration: `${randomNumber(20, 40)}s`,
    animationDelay: `${randomNumber(0, 5)}s`, // Added delay for more natural effect
    opacity: randomNumber(0.2, 0.6),
    fontSize: `${randomNumber(5, 10)}px`,
    left: `${randomNumber(0, 100)}vw`,
    top: `-25%`
});

function randomNumber(min, max) {
    return Math.random() * (max - min) + min;
}
</script>

<style scoped>
.snowflake {
    position: absolute;
    user-select: none;
    z-index: 1000;
    color: white;
    animation-name: fall;
    animation-timing-function: linear;
    animation-iteration-count: infinite; /* Infinite animation */
}

@keyframes fall {
    0% {
        transform: translateY(-100vh);
    }
    100% {
        transform: translateY(100vh);
    }
}
</style>
