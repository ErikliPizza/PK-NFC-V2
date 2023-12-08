<script setup>
import MainTemplate from "@/Components/Apps/template/MainTemplate.vue";
import {useWhatsapp} from "@/Composables/useWhatsapp.vue";

defineProps({
    information: {
        required: true
    }
})

const { sendWith } = useWhatsapp()
</script>

<template>
    <a
        :href="sendWith(information.value)"
        target="_blank"
        class="flex space-x-1 items-center">
        <MainTemplate :value="information.value" :title="information.title" :icon="information.app.icon" :app_title="information.app.title"/>
    </a>
</template>

<style scoped>

</style>
