<template>
    <div class="container">
        <div v-if="type === 'promotions'">
            <div class="row">
                <div class="col mb-4">
                    <h1 class="title_form">{{ title }}</h1>
                </div>
                <div class="col d-flex align-items-center justify-content-end mb-4">
                    <label class="title_show">{{ trans.show }}</label>
                    <div class="dropdown change_show">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ past ? trans.past : trans.actives }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#" @click.prevent="changeShow()" v-if="past">
                                {{ trans.actives }}
                            </a>
                            <a class="dropdown-item" href="#" @click.prevent="changeShow('past')" v-else>
                                {{ trans.past }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <promotion :item="item" :trans="trans" v-for="(item, index) in items" :key="index"/>
            </div>
        </div>
        <div class="row my-4" v-else>
            <div class="col-md-6 col-lg-4 mb-4" v-for="item in items">
                <article-item :item="item" :trans="trans"/>
            </div>
        </div>
        <skeleton-promo :loading="loading" v-if="type === 'promotions'"/>
        <skeleton-article :loading="loading" v-else/>
        <p class="text-center" v-if="links.next">
            <a href="#" class="btn-custom px-5" @click.prevent="changePage">{{ trans.show_more }}</a>
        </p>
    </div>
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
                this.trans = response.data.trans;
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
                trans: [],
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
        // mounted() {
        //     this.getItems()
        // },
        methods: {
            getItems() {
                this.loading = true;
                let data = {
                    params: {
                        page: this.current_page,
                        type: this.type,
                        past: this.past
                    }
                };
                axios.get(this.source, data).then((response) => {
                    for (let i = 0; i < response.data.data.length; i++)
                        this.items.push(response.data.data[i]);

                    this.links = response.data.links;
                    this.trans = response.data.trans;
                    this.pageTitle = response.data.pageTitle;
                    this.loading = false;
                }).catch(function (error) {
                    console.log(error);
                });
            },
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