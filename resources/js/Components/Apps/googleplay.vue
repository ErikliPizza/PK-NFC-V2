<script setup>
import MainTemplate from "@/Components/Apps/template/MainTemplate.vue";

defineProps({
    information: {
        required: true
    }
})

const constructAppLink = (information) => {
    const { app } = information;
    return `https://${app.prefix}${information.value}${app.suffix || ''}`;
};
</script>

<template>
    <a
        :href="constructAppLink(information)"
        target="_blank"
        class="flex space-x-1 items-center">
        <MainTemplate :value="information.value" :title="information.title" :icon="information.app.icon" :app_title="information.app.title"/>
    </a>
</template>

<style scoped>

</style>
