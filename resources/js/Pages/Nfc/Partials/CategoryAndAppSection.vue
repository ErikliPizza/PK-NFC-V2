<script setup>
// Vue Components
import CategoryItem from "@/Components/CategoryItem.vue";
import CategoryList from "@/Components/Card/CategoryList.vue";
import CategoriesFrame from "@/Components/Card/Frames/CategoriesFrame.vue";
import AppsFrame from "@/Components/Card/Frames/AppsFrame.vue";

// App Components
import whatsapp from "@/Components/Apps/whatsapp.vue";
import mail from "@/Components/Apps/mail.vue";
import telegram from "@/Components/Apps/telegram.vue";
import bank from "@/Components/Apps/bank.vue";
import cryptoc from "@/Components/Apps/cryptoc.vue";
import googleplay from "@/Components/Apps/googleplay.vue";
import viber from "@/Components/Apps/viber.vue";
import phone from "@/Components/Apps/phone.vue";
import Default from "@/Components/Apps/Default.vue";

// Vue Composition API
import { ref } from "vue";

const props = defineProps({
    informations: {
        required: true
    }
})

/**
 * Get the Vue component corresponding to the given app title.
 *
 * @param {string} appTitle - The title of the app.
 * @returns {object} The Vue component for the specified app, or the Default component.
 */
const getAppComponent = (appTitle) => {
    // Define a mapping between app titles and their corresponding components
    const appComponentMap = {
        whatsapp,
        mail,
        telegram,
        bank,
        cryptoc,
        googleplay,
        viber,
        phone,
        // Add more app titles and corresponding components here
    };

    // Retrieve the Vue component based on the app title (case-insensitive)
    return appComponentMap[appTitle.toLowerCase()] || Default;
};

const categories = ref({}); // A ref to store categorized information.

/**
 * Loop through each app information and organize into categorized groups.
 */
for (const info of props.informations) {
    // Check if the app has a category
    if (info.app.category) {
        // If the category doesn't exist in the 'categories' object, create it
        if (!categories.value[info.app.category.id]) {
            categories.value[info.app.category.id] = {
                id: info.app.category.id,
                title: info.app.category.title,
                icon: info.app.category.icon,
                order: info.app.category.order, // Add order property to the category
                infos: [],
            };
        }
        // Push the app information into the category's 'infos' array
        categories.value[info.app.category.id].infos.push(info);
    }
}

const orderedCategories = Object.values(categories.value).sort((a, b) => a.order - b.order); // An array of categories sorted based on the 'order' property.
const active = ref(0); // A ref representing the index of the active category.

</script>

<template>
    <CategoriesFrame v-if="informations.length >= 15">
        <!-- Display categorized buttons -->
        <CategoryItem
            v-for="item in orderedCategories"
            :key="item.id"
            :icon="item.icon"
            :title="item.title"
            @click="active = item.id"
            :class="{'bg-gray-300 transition duration-500 rounded-lg' : item.id === active}"
        />
    </CategoriesFrame>

    <AppsFrame>
        <!-- Display ALL informations -->
        <CategoryList v-show="0 === active">
            <!-- Display components for each app information -->
            <component v-for="info in informations" :is="getAppComponent(info.app.component)" :information="info" :key="info.id"/>
        </CategoryList>

        <!-- Loop through ordered categories and display apps for each category -->
        <CategoryList v-for="category in orderedCategories" :key="category.id" v-show="category.id === active">
            <!-- Display components for each app information within the category -->
            <component v-for="info in category.infos" :is="getAppComponent(info.app.component)" :information="info" :key="info.id"/>
        </CategoryList>
    </AppsFrame>

</template>
