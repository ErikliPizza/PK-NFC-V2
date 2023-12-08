<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: String,
        required: true,
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <!-- This is the special input text for inserting the information value with opening-closing animation -->

    <input
        autocomplete="off"
        class="focus:border-0 focus:ring-0 input"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
<style scoped>
.input {
    padding: 10px;
    width: 80px;
    border: 0.1rem solid;
    outline: none;
    box-shadow: 0 0;
    font-size: 18px;
    transition: width 0.3s;
}

.input:focus {
    box-shadow: none;
    width: 230px;
}

</style>
