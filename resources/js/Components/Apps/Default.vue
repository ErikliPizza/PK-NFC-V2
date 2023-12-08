<script setup>
import MainTemplate from "@/Components/Apps/template/MainTemplate.vue";

defineProps({
    information: {
        required: true
    }
})

// This function takes an 'information' object as input and constructs a link based on its properties.
const constructAppLink = (information) => {
  // Extract the 'app' property from the 'information' object.
  const { app } = information;

  // Check if 'information.value' starts with 'https://' or 'http://'
  if (information.value.startsWith("https://") || information.value.startsWith("http://")) {
    // If it does, return the constructed link with optional 'app.prefix' and 'app.suffix'
    return `${app.prefix || ''}${information.value}${app.suffix || ''}`;
  } else if (information.value.startsWith("www")) {
    // Check if 'information.value' starts with 'www'
    // If it does, assume it's a URL missing the 'https://' prefix and construct the link accordingly.
    return `https://${app.prefix || ''}${information.value}${app.suffix || ''}`;
  } else {
    // If 'information.value' doesn't start with 'https://', 'http://', or 'www',
    // assume it's a relative URL and construct the link with 'https://' prefix and optional 'app.prefix' and 'app.suffix'.
    const url = `https://${app.prefix || ''}${information.value}${app.suffix || ''}`;
    return url;
  }
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
