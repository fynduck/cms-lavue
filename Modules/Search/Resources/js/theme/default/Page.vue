<template>
    <div v-if="page" class="search_page">
        <section class="container">
            <h1 class="text-center my-4 title_page">{{ page.title }}</h1>
            <v-runtime-template :template="description" v-if="page.description"/>
        </section>
        <div class="text-center" v-if="$fetchState.pending">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <search-result :items="items"></search-result>
        <section class="container" v-if="page.description_footer">
            <v-runtime-template :template="descriptionFooter"/>
        </section>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import VRuntimeTemplate from "v-runtime-template";
import axios from "axios";
import SearchResult from "./components/SearchResult";

export default {
    name: "PagePage",
    components: {
        VRuntimeTemplate,
        SearchResult
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
        },
        source() {
            return '/search-result'
        }
    },
    async fetch() {
        let params = {
            params: {
                page: this.current_page,
                limit: 10,
                q: this.$route.query.q
            }
        };

        try {
            const {data} = await axios.get(this.source, params);
            for (let i = 0; i < data.data.length; i++)
                this.items.push(data.data[i]);
        } catch (e) {
        }
    },
    data() {
        return {
            items: [],
            current_page: 1
        }
    },
}
</script>