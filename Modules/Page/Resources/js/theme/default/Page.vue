<template>
    <div v-if="page">
        <slider-top :page-id="page.id"></slider-top>
        <section class="container">
            <h1 class="text-center my-4 title_page">{{ page.title }}</h1>
            <v-runtime-template :template="description" v-if="page.description"/>
        </section>
        <home-articles type="articles" v-if="page.method === 'home'"/>
        <section class="container" v-if="page.description_footer">
            <v-runtime-template :template="descriptionFooter"/>
        </section>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import VRuntimeTemplate from "v-runtime-template";

export default {
    name: "PagePage",
    components: {
        VRuntimeTemplate,
        SliderTop: () => import(`../../../../../Banner/Resources/js/theme/${process.env.appTheme}/SliderTop`),
        HomeArticles: () => import(`../../../../../Article/Resources/js/theme/${process.env.appTheme}/HomeArticles`)
    },
    computed: {
        ...mapGetters({
            page: 'page/page'
        }),
        description() {
            return this.page.description.replace(/<p>\s*<\/p>/gi, "");
        },
        descriptionFooter() {
            return this.page.description_footer.replace(/<p>\s*<\/p>/gi, "");
        }
    }
}
</script>