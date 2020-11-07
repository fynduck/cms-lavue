<template>
    <section class="container" v-if="items.length">
        <div class="fs-26 fw600 text-uppercase my-5">{{ title }}</div>
        <carousel :autoplay="true"
                  :responsive="responsive"
                  :dots="false"
                  :nav="true"
                  v-if="items.length && !loading">
            <promotion :item="item" :trans="trans" v-for="(item, index) in items" :key="index"/>
        </carousel>
        <div class="text-center my-4" v-if="link && link_title">
            <a :href="link" class="btn-custom">{{ link_title }}</a>
        </div>
    </section>
</template>

<script>
    import Promotion from "./components/Promotion";
    import carousel from 'v-owl-carousel'

    export default {
        name: "HomePromo",
        components: {
            carousel,
            Promotion
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
                responsive: {
                    0: {
                        items: 1
                    }
                },
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
            }
        }
    }
</script>