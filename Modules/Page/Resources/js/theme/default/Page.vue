<template>
    <div v-if="page">
        <section class="container">
            <h1 class="text-center my-4 title_page">{{ page.title }}</h1>
            <v-runtime-template :template="description" v-if="page.description"/>
        </section>
        <home-articles type="articles" v-if="page.method === 'home'"/>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import VRuntimeTemplate from "v-runtime-template";

export default {
    name: "PagePage",
    components: {
        VRuntimeTemplate,
        HomeArticles: () => import(`../../../../../Article/Resources/js/theme/${process.env.appTheme}/HomeArticles`),
    },
    computed: {
        ...mapGetters({
            page: 'page/page'
        }),
        description() {
            return this.page.description.replace(/<p>\s*<\/p>/gi, "");
        }
    }
}
</script>