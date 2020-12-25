<template>
    <section v-if="loadArticles && page">
        <div class="container">
            <h1 class="text-center my-4 title_page">{{ page.title }} Sukas</h1>
            <v-runtime-template :template="description" v-if="page.description"/>
        </div>
        <items :type="page.method" v-if="loadArticles"/>
    </section>
    <component :is="page.method" v-else-if="page"></component>
</template>

<script>
import VRuntimeTemplate from "v-runtime-template";
import Items from "../../../../../Article/Resources/js/theme/default/Items";
import Articles from "../../../../../Article/Resources/js/theme/default/Articles";
import {mapGetters} from "vuex";

export default {
    name: "ArticlePage",
    head() {
        return {
            title: this.meta.title,
            meta: [
                {hid: 'description', name: 'description', content: this.meta.description},
                {hid: 'keywords', name: 'keywords', content: this.meta.keywords}
            ]
        }
    },
    components: {
        VRuntimeTemplate,
        Items,
        Articles
    },
    computed: {
        ...mapGetters({
            page: 'page/page',
            meta: 'page/meta'
        }),
        loadArticles() {
            return typeof this.$route.params.category === "undefined"
        },
        description() {
            return this.page.description.replace(/<p>\s*<\/p>/gi, "");
        }
    }
}
</script>