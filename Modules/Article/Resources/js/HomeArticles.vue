<template>
    <section class="container" v-if="items.length">
        <div class="row">
            <div class="col-lg-3 d-none d-lg-block">
                <div class="block_home_articles">
                    <span class="title">{{ title }}</span>
                    <a :href="link" class="all_articles" v-if="link && link_title">{{ link_title }}</a>
                    <i class="fas fa-chevron-left nav_slide" @click="slideCarousel('prev')"></i>
                    <i class="fas fa-chevron-right nav_slide" @click="slideCarousel('next')"></i>
                </div>
            </div>
            <div class="col-lg-9">
                <carousel
                    :autoplay="true"
                    :loop="true"
                    :perPageCustom="responsive"
                    :navigationEnabled="enableNavigation"
                    navigationPrevLabel="<i class='fas fa-chevron-left'></i>"
                    navigationNextLabel="<i class='fas fa-chevron-right'></i>"
                    :paginationEnabled="false"
                    ref="homePromo"
                    class="home_articles"
                    v-if="items.length && !loading">
                    <slide v-for="(item, index) in items" :key="index">
                        <div class="article">
                            <div class="title">
                                <a :href="item.link">
                                    {{ item.title }}
                                </a>
                            </div>
                            <div class="desc">{{ item.description }}</div>
                            <div class="d-flex justify-content-between mt-4">
                                <time pubdate :datetime="item.show_date">{{ item.show_date }}</time>
                                <a :href="item.link" class="further underline">{{ trans.further }}</a>
                            </div>
                        </div>
                    </slide>
                </carousel>
            </div>
        </div>
    </section>
</template>

<script>
    import {Carousel, Slide} from 'vue-carousel';

    export default {
        name: "HomeArticles",
        components: {
            Carousel,
            Slide
        },
        props: {
            source: {
                type: String,
                required: true
            },
            title: String,
            link: String,
            link_title: String,
            limit: {
                type: Number,
                default: 3
            }
        },
        data() {
            return {
                loading: true,
                items: [],
                trans: [],
                responsive: [
                    [0, 1],
                    [1200, 2]
                ]
            }
        },
        computed: {
            enableNavigation() {
                return document.documentElement.clientWidth <= 960
            }
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems() {
                this.loading = true;
                let data = {
                    params: {
                        limit: this.limit,
                        show_home: 1
                    }
                };
                axios.get(this.source, data).then((response) => {
                    this.items = response.data.data;
                    this.trans = response.data.trans;
                    this.$nextTick(() => {
                        this.loading = false;
                    })
                }).catch(function (error) {
                    console.log(error);
                });
            },
            changePage() {
                this.current_page++;

                this.getItems();
            },
            slideCarousel(value) {
                const carousel = this.$refs.homePromo;
                const currentPage = carousel.currentPage;
                const pageCount = carousel.pageCount;
                if (value === 'prev')
                    currentPage !== 0 ? carousel.goToPage(currentPage - 1) : carousel.goToPage(pageCount - 1);
                else
                    currentPage < pageCount - 1 ? carousel.goToPage(currentPage + 1) : carousel.goToPage(0);
            },
        }
    }
</script>