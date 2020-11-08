<template>
    <section v-if="item">
        <h1 class="my-4 title_page">{{ item.title }}</h1>
        <v-runtime-template :template="description" v-if="item.description"/>
        <time>{{ item.show_date }}</time>
    </section>
</template>

<script>
import VRuntimeTemplate from "v-runtime-template";
import axios from "axios";

export default {
    name: "Articles",
    components: {
        VRuntimeTemplate,
    },
    head() {
        return {
            title: this.item ? this.item.meta_title : '',
            meta: [
                {hid: 'description', name: 'description', content: this.item ? this.item.meta_description : ''},
                {hid: 'keywords', name: 'keywords', content: this.item ? this.item.meta_keywords : ''}
            ]
        }
    },
    async fetch() {
        if (this.$route.params.category) {
            const {data} = await axios.get(`/articles/${this.$route.params.category}`)

            this.item = data.data
        }
    },
    computed: {
        description() {
            return this.item.description.replace(/<p>\s*<\/p>/gi, "");
        }
    },
    data() {
        return {
            item: null
        }
    }
}
</script>