<template>
    <div v-if="page">
        <section class="container">
            <h1 class="text-center my-4 title_page">{{ page.title }}</h1>
            <v-runtime-template :template="description" v-if="page.description"/>
        </section>
        <items type="articles" v-if="page.method === 'home'"/>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import VRuntimeTemplate from "v-runtime-template";
import Items from "../../../../../Article/Resources/js/theme/default/Items";

export default {
    name: "PagePage",
    components: {
        VRuntimeTemplate,
        Items
    },
    computed: {
        ...mapGetters({
            page: 'page/page'
        }),
        description() {
            // return this.page.description.replace(/<[^/>][^>]*><\/[^>]+>/gim, "");
            return this.page.description.replace(/<p>\s*<\/p>/gi, "");
        }
    }
}
</script>