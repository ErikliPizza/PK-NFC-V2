<script setup>
import MainTemplate from "@/Components/Apps/template/MainTemplate.vue";
import { useContact } from "@/Composables/useContact.vue";

const {callNumber} = useContact();

const props = defineProps({
    information: {
        required: true
    }
})

</script>

<template>
    <div
        @click="callNumber(information.value)"
        class="flex space-x-1 items-center truncate">
        <MainTemplate :value="information.value" :title="information.title" :icon="information.app.icon" :app_title="information.app.title"/>
    </div>
</template>
