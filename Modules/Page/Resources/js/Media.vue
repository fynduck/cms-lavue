<template>
    <div class="list_media">
        <div class="row justify-content-end my-4" v-if="filter">
            <div class="col-md-3">
                <select class="form-control" v-model="option">
                    <option :value="option.value" v-for="option in options">{{ option.title }}</option>
                </select>
            </div>
        </div>
        <div class="row" v-if="items.length">
            <div class="col-sm-6 col-lg-4 mb-4" v-for="media in items">
                <div class="media_item">
                    <a :href="media.link">
                        <figure>
                            <img :src="media.src_m" :alt="media.title"/>
                            <i class="fas fa-play-circle position-absolute" v-if="media.type === 'video'"></i>
                            <div class="hover_detail" v-else>{{ trans.show_detail }}</div>
                        </figure>
                    </a>
                    <h2 class="title">
                        <a :href="media.link">{{ media.title }}</a>
                    </h2>
                </div>
            </div>
        </div>
        <skeleton-media :loading="loading" :rows="6"></skeleton-media>
        <div class="text-center py-5" v-if="!loading && pagination.current_page < pagination.last_page">
            <a href="#" class="btn-custom" @click.prevent="getItems(pagination.current_page + 1)">{{ trans.show_more }}</a>
        </div>
    </div>
</template>

<script>
    import SkeletonMedia from './SkeletonMedia';

    export default {
        name: "Media",
        components: {
            SkeletonMedia
        },
        props: {
            source: {
                type: String,
                required: true
            },
            filter: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                items: [],
                trans: [],
                pagination: {
                    total: 0,
                    per_page: 0,
                    from: 1,
                    to: 0,
                    current_page: 1
                },
                option: null,
                options: [],
                loading: false
            }
        },
        watch: {
            option: function () {
                this.getItems();
                this.items = [];
                this.pagination.current_page = 1;
            },
        },
        mounted() {
            this.getItems();
        },
        methods: {
            getItems(page = 1) {
                this.loading = true;
                axios.get(this.source, {params: {page: page, type: this.option}}).then((response) => {
                    for (let i = 0; i < response.data.items.data.length; i++) {
                        this.items.push(response.data.items.data[i]);
                    }

                    this.trans = response.data.trans;
                    this.options = response.data.options;
                    this.pagination = response.data.pagination;

                    this.$nextTick(() => {
                        this.loading = false;
                    })
                }).catch((error) => {
                    console.log(error);
                })
            }
        }
    }
</script>

<style scoped>

</style>
