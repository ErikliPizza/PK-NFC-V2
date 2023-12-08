<template>
    <Listbox as="div" v-model="selected">
        <div class="relative mt-2 w-full">
            <ListboxButton class="relative w-full cursor-default rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm sm:leading-6">
        <span class="flex items-center">
          <img :src="selected.flag" alt="" class="h-5 w-6 flex-shrink-0 rounded-full" v-if="selected.id !== 1 && selected.id !== 2"/>
          <span class="ml-3 block truncate">{{ __(selected.name) }}</span>
        </span>
                <span class="pointer-events-none absolute inset-y-0 right-0 ml-3 flex items-center pr-2">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
        </span>
            </ListboxButton>

            <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <ListboxOptions class="z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                    <ListboxOption as="template" v-for="country in countries" :key="country.id" :value="country" v-slot="{ active, selected }">
                        <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                            <div class="flex items-center">
                                <img :src="country.flag" alt="" class="h-5 w-6" v-if="country.id !== 1 && country.id !== 2"/>
                                <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">{{ __(country.name) }}</span>
                            </div>

                            <span v-if="selected" :class="[active ? 'text-white' : 'text-indigo-600', 'absolute inset-y-0 right-0 flex items-center pr-4']">
                <CheckIcon class="h-5 w-5" aria-hidden="true" />
              </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

<script setup>
import { watch, ref } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'


const props = defineProps({
    modelValue: String // expecting an object that represents the selected country
})

const emits = defineEmits(['update:modelValue'])

const countries = [
    {
        id: 1,
        name: 'Filter according to your local area',
        prefix: 'ALL',
    },
    {
        id: 2,
        name: 'Crypto Currency',
        prefix: 'CPT',
    },
    {
        id: 3,
        name: 'Turkey',
        prefix: 'TR',
        flag:
            'https://flagcdn.com/84x63/tr.png',
    },
    {
        id: 4,
        name: 'France',
        prefix: 'FR',
        flag:
        'https://flagcdn.com/84x63/fr.png'
    },
    {
        id: 5,
        name: 'Germany',
        prefix: 'DE',
        flag:
            'https://flagcdn.com/84x63/de.png',
    },
    {
        id: 6,
        name: 'Iraq',
        prefix: 'IQ',
        flag:
            'https://flagcdn.com/84x63/iq.png',
    },
    {
        id: 7,
        name: 'Russia',
        prefix: 'RU',
        flag:
            'https://flagcdn.com/84x63/ru.png',
    },
]

const selected = ref(countries[props.modelValue])

// Watch for changes in the selected country and emit an event to update the parent component
watch(selected, (newValue) => {
    emits('update:modelValue', newValue)
})

// Ensure that the selected value updates if the prop changes from outside
watch(() => props.modelValue, (newVal) => {
    selected.value = newVal
})
</script>
