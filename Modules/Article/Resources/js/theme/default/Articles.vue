<template>
    <section v-if="item" class="container">
        <h1 class="my-4 title_page">{{ item.title }}</h1>
        <v-runtime-template :template="description" v-if="item.description"/>
        <time>{{ item.show_date }}</time>
    </section>
</template>

<script>
import VRuntimeTemplate from "v-runtime-template";
import axios from "axios";
import {mapGetters} from "vuex";

export default {
    name: "Articles",
    components: {
        VRuntimeTemplate,
    },
    head() {
        return {
            title: this.meta.title,
            meta: [
                {hid: 'description', name: 'description', content: this.meta.description},
                {hid: 'keywords', name: 'keywords', content: this.meta.keywords}
            ]
        }
    },
    data() {
        return {
            meta: {
                title: '',
                description: '',
                keywords: ''
            }
        }
    },
    async fetch() {
        if (this.$route.params.category) {
            await this.$store.dispatch('page/setItem', null)
            await this.$store.dispatch('lang/setPageLang', {})
            const {data} = await axios.get(`/articles/${this.$route.params.category}`)
            if (data.data.method === 'not_found') {
                return this.$nuxt.error({statusCode: 404, message: data.data.title, page: data.data})
            }

            await this.$store.dispatch('page/setItem', data.data)

            if (typeof data.meta !== "undefined") {
                await this.$store.dispatch('page/setMeta', data.meta)
            }
            if (typeof data.meta !== "undefined") {
                this.meta.title = data.meta.meta_title
                this.meta.description = data.meta.meta_description
                this.meta.keywords = data.meta.meta_keywords
            }

            if (typeof data.page_lang !== "undefined") {
                await this.$store.dispatch('lang/setPageLang', data.page_lang)
            }
        }
    },
    computed: {
        ...mapGetters({
            item: 'page/item'
        }),
        description() {
            return this.item.description.replace(/<p>\s*<\/p>/gi, "");
        }
    }
}
</script>