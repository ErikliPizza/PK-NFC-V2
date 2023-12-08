<script setup>
// Custom Components
import CategoryItem from "@/Components/CategoryItem.vue";
import CategoryList from "@/Components/Card/CategoryList.vue";
import AppItem from "@/Components/AppItem.vue";
import CategoriesFrame from "@/Components/Card/Frames/CategoriesFrame.vue";
import AppsFrame from "@/Components/Card/Frames/AppsFrame.vue";

// Vue Composition API
import { ref, computed } from "vue";
import FilterBank from "@/Components/FilterBank.vue";
import {TransitionChild} from "@headlessui/vue";

const props = defineProps({
    categories: {
        required: true,
        type: Object
    },
    card_id: {
        required: true
    }
})

const active = ref(props.categories[0].id); // A ref representing the index of the active category.

// New ref for the selected prefix in the third category
const selectedPrefix = ref(''); // You can initialize it with a specific prefix to start with that filter

const filteredApps = computed(() => {
    if (active.value === 3 && selectedCountry.value && selectedCountry.value.prefix !== "ALL") {
        const activeCategory = props.categories.find(category => category.id === active.value);

        return activeCategory.apps.filter(app => {
            // Check if app.prefix is not null or undefined
            if (app.prefix) {
                const appPrefixes = app.prefix.split(',').map(prefix => prefix.trim());
                return appPrefixes.includes(selectedCountry.value.prefix);
            }
            // If app.prefix is null or undefined, the app does not match the filter
            return false;
        });
    }

    return props.categories.find(category => category.id === active.value).apps;
});
const selectedCountry = ref(0)
</script>

<template>
    <CategoriesFrame>
        <!-- Display categorized buttons -->
        <CategoryItem
            v-for="item in categories"
            :key="item.id"
            :icon="item.icon"
            :title="item.title"
            @click="active = item.id"
            :class="{'bg-gray-300 transition duration-500 rounded-lg' : item.id === active}"
        />
    </CategoriesFrame>
    <div class="px-6 mb-4" v-show="active === 3">
        <FilterBank v-model="selectedCountry"/>
    </div>
    <AppsFrame>

        <!-- Iterate through each category -->
        <CategoryList v-if="active === 3" :key="3">
            <AppItem :is-edit="false" v-for="app in filteredApps" :app="app" :card_id="card_id" :key="app.id"/>
        </CategoryList>
        <CategoryList v-for="category in categories" :key="category.id" v-show="category.id === active && active !== 3">
            <AppItem :is-edit="false" v-for="app in category.apps" :app="app" :card_id="card_id" :key="app.id"/>
        </CategoryList>
    </AppsFrame>

</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity .3s ease;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
}
</style>
