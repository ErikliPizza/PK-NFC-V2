<script setup>
import { computed } from 'vue'

const props = defineProps({ status: Number })

const title = computed(() => {
  return {
    503: '503: Service Unavailable',
    500: '500: Server Error',
    404: '404: Page Not Found',
    403: '403: Forbidden',
  }[props.status]
})

const description = computed(() => {
  return {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Whoops, something went wrong on our servers.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
  }[props.status]
})
</script>

<template>
    <!-- This is the page which we are handling the errors with inertia -->

    <div class="flex flex-col items-center justify-center h-screen bg-gray-100 p-2">
    <h1 class="text-3xl font-bold mb-4">{{ title }}</h1>
    <div class="text-gray-700">{{ description }}</div>
  </div>
</template>
