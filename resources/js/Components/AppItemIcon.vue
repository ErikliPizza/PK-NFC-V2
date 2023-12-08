<script setup>
defineProps({
  icon: { required: true },
  title: { required: false },
  app_title: { required: false }
})
</script>

<template>
  <div class="flex flex-col items-center justify-center my-2">
    <div class="p-1 bg-zinc-50 rounded-lg text-center flex flex-col items-center justify-center square-icon cursor-pointer shadow-md shadow-gray-200">
      <img :src="'/storage/images/apps/'+icon" class="rounded-lg w-full h-full" alt="App Icon">
    </div>
    <div class="text-center mt-1" style="width: 68px;">
      <p class="truncate text-xs font-bold" v-if="title">{{title}}</p>
      <p class="truncate text-xs font-bold text-gray-400" v-else>{{__(app_title)}}</p>
    </div>
  </div>


</template>

<style scoped>
.square-icon {
  width: 55px;
  height: 55px;
}
</style>
