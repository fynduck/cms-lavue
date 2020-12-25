<template>
    <section class="container">
        <div class="row my-4">
            <div class="col-md-6 col-lg-4 mb-4" v-for="item in items">
                <article-item :item="item"/>
            </div>
        </div>
        <skeleton-article :loading="loading"/>
        <p class="text-center" v-if="links.next">
            <a href="#" class="btn-custom px-5" @click.prevent="changePage">{{ $t('Article.show_more') }}</a>
        </p>
    </section>
</template>

<script>
    import axios from 'axios'

    import SkeletonArticle from '../../components/SkeletonArticle'
    import ArticleItem from '../../components/ArticleItem';
    import Promotion from "../../components/Promotion";
    import SkeletonPromo from "../../components/SkeletonPromo";
    export default {
        name: "Items",
        components: {
            SkeletonPromo,
            SkeletonArticle,
            ArticleItem,
            Promotion
        },
        head() {
            return {
                link: [
                    {rel: 'preload', href: `/css/theme/default/articles.css`, as: 'style'},
                    {rel: 'stylesheet', href: `/css/theme/default/articles.css`}
                ]
            }
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
                    type: this.type,
                    past: this.past
                }
            };
           await axios.get(this.source, data).then((response) => {
                for (let i = 0; i < response.data.data.length; i++)
                    this.items.push(response.data.data[i]);

                this.links = response.data.links;
                this.pageTitle = response.data.pageTitle;
                this.loading = false;
            }).catch(function (error) {
                console.log(error);
            });
        },
        data() {
            return {
                loading: true,
                items: [],
                show_more: '',
                past: null,
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
            changeShow(show) {
                let locate = location.pathname;
                if (show === 'past') {
                    this.past = true;
                    locate += '?show=past';
                } else {
                    this.past = null;
                }

                window.history.pushState('', '', locate);
                this.items = [];
                this.getItems();
            },
            changePage() {
                this.current_page++;

                this.getItems();
            }
        }
    }
</script>