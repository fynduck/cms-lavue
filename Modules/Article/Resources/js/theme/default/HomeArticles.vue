<template>
    <section class="container">
        <div class="masonry">
            <article-item :item="item" v-for="item in items" :key="item.id"/>
            <article-item :item="item" v-for="item in items" :key="item.id"/>
        </div>
        <skeleton-article :loading="loading"/>
    </section>
</template>

<script>
import axios from 'axios'

import SkeletonArticle from './components/SkeletonArticle'
import ArticleItem from './components/ArticleItem';

export default {
    name: "HomeArticles",
    props: {
        type: {
            type: String,
            required: true
        }
    },
    components: {
        SkeletonArticle,
        ArticleItem
    },
    async fetch() {
        this.loading = true;
        let data = {
            params: {
                type: this.type
            }
        };
        await axios.get(this.source, data).then(response => {
            this.items = response.data.data;

            this.links = response.data.links;
            this.loading = false;
        }).catch(() => {
            this.loading = false;
        });
    },
    data() {
        return {
            loading: true,
            items: [],
            links: {
                first: '',
                last: null,
                next: null,
                prev: null,
            },
        }
    },
    computed: {
        source() {
            return '/get-articles'
        }
    }
}
</script>