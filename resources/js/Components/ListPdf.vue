<script setup>
import {DocumentIcon} from "@heroicons/vue/24/outline/index.js";
import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {EllipsisHorizontalIcon} from "@heroicons/vue/20/solid/index.js";
import moment from "moment/moment.js";
import {router} from "@inertiajs/vue3";

// Composables
import { useWhatsapp } from "@/Composables/useWhatsapp.vue";

const { shareWith } = useWhatsapp();

const props = defineProps({
    card: {
        required: true
    }
})

const formatCreatedDate = (item) => {
    return moment(item).format('MM-D, YYYY HH:mm');
}

const deletePdf = (pdf) => {
    router.delete(`/pdfs/${pdf}`);
}
function openWhatsApp(pdfTitle, item) {

    // Create a message containing the card's title and URL
    const message = encodeURIComponent(`${pdfTitle} - ${item}`);

    // Call the shareWith function from the useWhatsapp composable
    shareWith(message);
}
</script>

<template>
    <ul role="list" class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8 mt-6">
        <li v-for="pdf in card.pdfs" :key="pdf.id" class="overflow-hidden rounded-xl border border-gray-200">
            <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                <a :href="pdf.path" target="_blank">
                    <DocumentIcon class="h-12 w-12 flex-none rounded-lg bg-white object-cover ring-1 ring-gray-900/10" />
                </a>
                <div class="text-sm font-medium leading-6 text-gray-900 truncate">
                    <a :href="pdf.path" target="_blank">
                        {{ pdf.title }}
                    </a>
                </div>
                <Menu as="div" class="relative ml-auto">
                    <MenuButton class="-m-2.5 block p-2.5 text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Open options</span>
                        <EllipsisHorizontalIcon class="h-5 w-5" aria-hidden="true" />
                    </MenuButton>
                    <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                        <MenuItems class="absolute right-0 z-10 mt-0.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                            <MenuItem v-slot="{ active }">
                                <a :href="pdf.path" target="_blank" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                                >{{ __('Show') }}<span class="sr-only">, {{ pdf.title }}</span></a
                                >
                            </MenuItem>
                            <MenuItem v-slot="{ active }">
                                        <span @click="openWhatsApp(pdf.title, pdf.path)" class="cursor-pointer" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                                        >{{ __('Share') }}<span class="sr-only">, {{ pdf.title }}</span></span
                                        >
                            </MenuItem>
                            <MenuItem v-slot="{ active }" v-if="route().current('card')">
                                        <span @click="deletePdf(pdf.id)" class="cursor-pointer" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                                        >{{ __('Delete') }}<span class="sr-only">, {{ pdf.title }}</span></span
                                        >
                            </MenuItem>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
            <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                <div class="flex justify-between gap-x-4 py-3">
                    <dd class="text-gray-700">
                        <time :datetime="pdf.created_at.dateTime">{{ formatCreatedDate(pdf.created_at) }}</time>
                    </dd>
                </div>
                <div class="flex justify-between gap-x-4 py-3">
                    <dd class="flex items-start gap-x-2">
                        <div class="font-medium text-gray-900 truncate">{{ pdf.title }}</div>
                    </dd>
                </div>
            </dl>
        </li>
    </ul>
</template>

<style scoped>

</style>
