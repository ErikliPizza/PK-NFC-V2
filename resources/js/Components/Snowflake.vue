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
    animationDelay: '-2s', // No delay for immediate start
    opacity: randomNumber(0.2, 0.7),
    fontSize: `${randomNumber(5, 12)}px`,
    left: `${randomNumber(0, 90)}vw`,
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
    animation-iteration-count: infinite;
}

@keyframes fall {
    0% {
        transform: translateY(0);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh);
        opacity: 0; /* Optional: Fade out at the bottom */
    }
}
</style>
