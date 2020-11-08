<template>
    <section v-if="loadArticles && page">
        <h1 class="text-center my-4 title_page">{{ page.title }}</h1>
        <v-runtime-template :template="description" v-if="page.description"/>
        <items :type="page.method" v-if="loadArticles"/>
    </section>
    <component :is="page.method" v-else></component>
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
            title: this.page ? this.page.meta_title : '',
            meta: [
                {hid: 'description', name: 'description', content: this.page ? this.page.meta_description : ''},
                {hid: 'keywords', name: 'keywords', content: this.page ? this.page.meta_keywords : ''}
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