<template>
    <div class="search-page">
        <div class="mb-4" v-for="(group, typeKey) in items" v-if="items.length">
            <div class="mb-3" v-for="item in group">
                <a :href="item.link" class="title">{{ item.title }}</a>
                <div class="description">{{ item.description }}</div>
            </div>
            <p class="text-right" v-if="!by">
                <a :href="generateLink(groupKeys[typeKey])" class="see_more">{{ trans.show_more }} {{ trans[groupKeys[typeKey]].toLowerCase() }}</a>
            </p>
        </div>
        <div v-if="!items.length && !loading" class="not_results" v-html="trans.not_match_any_results"></div>
        <skeleton-search :loading="loading" :rows="10"></skeleton-search>
        <p class="text-center" v-if="by && pagination.next">
            <a href="#" class="btn-custom" @click.prevent="getItems(pagination.current_page+1)">{{ trans.show_more }}</a>
        </p>
    </div>
</template>

<script>
    import SkeletonSearch from "./components/SkeletonSearch";

    export default {
        name: "Search",
        components: {
            SkeletonSearch
        },
        props: {
            source: {
                type: String,
                required: true
            },
            query: String,
            by: String
        },
        data() {
            return {
                q: this.query,
                items: [],
                loading: false,
                trans: [],
                groupKeys: [],
                pagination: {
                    next: 0,
                    current_page: 1
                },
            }
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems(page) {
                this.loading = true;
                let data = {
                    params: {
                        page: page,
                        q: this.q,
                        by: this.by
                    }
                };
                axios.get(this.source, data).then((response) => {
                    if (typeof response.data.data !== "undefined") {
                        this.groupKeys = Object.keys(response.data.data);
                        for (let key of Object.keys(response.data.data))
                            this.items.push(response.data.data[key]);

                        if (typeof response.data.pagination !== "undefined")
                            this.pagination = response.data.pagination;

                        this.trans = response.data.trans;
                    }
                    this.loading = false;
                }).catch((error) => {
                    console.log(error);
                })
            },
            generateLink(by) {
                let url = location.href;

                return url += '&by=' + by;
            }
        }
    }
</script>
<style lang="stylus" scoped>
    .description
        color #666

    .title
        font-size 20px

    .see_more
        color #666
        text-decoration underline

    .not_results
        color #666
        font-size 20px
        margin 3rem auto
        text-align center
</style>
