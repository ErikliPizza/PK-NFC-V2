<script setup>
import MainTemplate from "@/Components/Apps/template/MainTemplate.vue";

defineProps({
    information: {
        required: true
    }
})

const getTelegramLink = (information) => {
    const { app } = information;
    const encodedMessage = encodeURIComponent(`Hello, I'm reaching out through your ${app.title} app.`);
    const username = information.value.replace('@', ''); // Remove "@" if it exists

    return `https://t.me/${username}?text=${encodedMessage}`;
};
</script>

<template>
    <a
        :href="getTelegramLink(information)"
        target="_blank"
        class="flex space-x-1 items-center">
        <MainTemplate :value="information.value" :title="information.title" :icon="information.app.icon" :app_title="information.app.title"/>
    </a>
</template>

<style scoped>

</style>
