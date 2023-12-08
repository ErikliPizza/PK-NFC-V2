<template>
  <div class="square-button rounded">
    <slot />
  </div>
</template>

<style >
.square-button {
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 42px;
    width: 42px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: background 0.5s;
}
</style>
