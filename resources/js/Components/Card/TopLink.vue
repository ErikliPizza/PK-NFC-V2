<script setup>
const props = defineProps({
    href: {
        type: String,
        required: true,
    }
});
</script>

<template>
    <Link :href="href" class="bg-blue-50 p-1 rounded-lg flex justify-center w-full items-center space-x-1">
        <slot />
    </Link>
</template>

<style scoped>

</style>
