<template>
    <section class="container">
        <div class="masonry">
            <article-item :item="item" v-for="item in items" :key="item.id"/>
        </div>
        <div class="d-flex justify-content-center my-4" v-if="loading">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </section>
</template>

<script>
import axios from 'axios'

import SkeletonArticle from './components/SkeletonArticle'
import ArticleItem from './components/ArticleItem';

export default {
    name: "Items",
    components: {
        SkeletonArticle,
        ArticleItem
    },
    props: {
        type: {
            type: String,
            required: true
        }
    },
    async fetch() {
        this.loading = true;
        let data = {
            params: {
                page: this.current_page,
                type: this.type
            }
        };
        await axios.get(this.source, data).then(response => {
            for (let i = 0; i < response.data.data.length; i++)
                this.items.push(response.data.data[i]);

            this.links = response.data.links;
            this.pageTitle = response.data.pageTitle;
            this.loading = false;
        }).catch(error => {
            this.loading = false;
        });
    },
    mounted() {
        window.onscroll = () => {
            if ((document.documentElement.scrollTop + window.innerHeight) + 200 >= document.documentElement.offsetHeight) {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.changePage()
                }, 1000);
            }
        }
    },
    data() {
        return {
            loading: true,
            items: [],
            show_more: '',
            past: null,
            timeout: null,
            links: {
                first: '',
                last: null,
                next: null,
                prev: null,
            },
            current_page: 1
        }
    },
    computed: {
        source() {
            return '/get-articles'
        }
    },
    methods: {
        changePage() {
            if (this.links.next) {
                this.current_page++;
                this.$fetch();
            }
        }
    }
}
</script>