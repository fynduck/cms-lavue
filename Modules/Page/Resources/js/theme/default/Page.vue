<template>
    <section v-if="page">
        <h1 class="text-center my-4 title_page">{{ page.title }}</h1>
        <v-runtime-template :template="description" v-if="page.description"/>

        <articles type="articles" v-if="page.method === 'home'"/>
    </section>
</template>

<script>
import {mapGetters} from "vuex";
import VRuntimeTemplate from "v-runtime-template";
import Articles from "../../../../../Article/Resources/js/theme/default/Articles";

export default {
    name: "PagePage",
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
        Articles
    },
    computed: {
        ...mapGetters({
            page: 'page/page',
        }),
        description() {
            // return this.page.description.replace(/<[^/>][^>]*><\/[^>]+>/gim, "");
            return this.page.description.replace(/<p>\s*<\/p>/gi, "");
        }
    }
}
</script>