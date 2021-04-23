<template>
    <div>
        <button type="button" class="btn-search" data-toggle="modal" data-target="#search"><i class="fa fa-search"></i></button>
        <div class="modal fade" id="search" tabindex="-1" role="dialog" aria-labelledby="search" aria-hidden="true">
            <div class="modal-dialog modal-lg search-block" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="input-group">
                            <input type="search" class="form-control" v-model="q" :placeholder="placeholder" required>
                            <div class="input-group-prepend">
                                <span class="input-group-text" onclick="location.href = 'search'">
                                    <i class="fas fa-spinner fa-pulse" v-if="loading"></i>
                                    <i class="fas fa-search" v-else></i>
                                </span>
                            </div>
                        </div>
                        <ul class="search_list">
                            <li v-for="(group, type) in items">
                                {{ trans[type] }}
                                <ul>
                                    <li v-for="item in group">
                                        <a :href="item.link">
                                            <img :src="item.image" :alt="item.title" v-if="item.image">
                                            {{ item.title }}
                                        </a>
                                    </li>
                                </ul>
                                <hr>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SearchLive",
        props: {
            placeholder: {
                type: String,
                default: 'Запрос'
            },
            source: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                q: '',
                items: [],
                trans: [],
                loading: false,
                timeout: null,
            }
        },
        watch: {
            q() {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    if (this.q.length > 1)
                        this.search();
                }, 1000);
            }
        },
        methods: {
            search() {
                this.loading = true;
                axios.get(this.source, {params: {q: this.q}}).then((response) => {
                    this.items = response.data.items;
                    this.trans = response.data.trans;
                    this.loading = false;
                }).catch((error) => {
                    console.log(error);
                })
            }
        }
    }
</script>

<style lang="stylus" scoped>
    .search_list
        list-style none
        padding-left 0
        overflow-y scroll
        max-height 500px

        > li
            font-weight bold

        ul
            list-style none

        a
            color #3c3c3c
            text-decoration none
            margin-top 1rem
            display block

        img
            margin-right .5rem
</style>
