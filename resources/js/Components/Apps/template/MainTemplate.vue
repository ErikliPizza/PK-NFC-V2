<script setup>
import AppItemIcon from "@/Components/AppItemIcon.vue";

defineProps({
  icon: {
    required: true
  },
  value: {
    required: true
  },
  title: {
    required: false
  },
  app_title: {
    required: false
  }
})
</script>

<template>
    <div class="flex items-center">
      <AppItemIcon :icon="icon" :title="title" :app_title="app_title"/>
    </div>
</template>
