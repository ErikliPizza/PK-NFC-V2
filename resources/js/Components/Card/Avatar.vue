<script setup>

defineProps({
    image: {
        required: false
    }
})
</script>

<template>
    <img
            :src="image" alt="Profile Photo"
        class="w-20 h-20 rounded-full mr-2 border-2 border-red-50 transition duration-300 hover:scale-125 z-999">

</template>

<style scoped>

</style>
