<template>
    <div itemscope itemtype="https://schema.org/WebSite" class="position-relative">
        <meta itemprop="url" :content="appUrl"/>
        <form class="d-flex" :action="searchAction" itemprop="potentialAction" itemscope
              itemtype="https://schema.org/SearchAction">
            <meta itemprop="target" :content="`${appUrl}?q={q}`"/>
            <input class="form-control me-2" name="q" type="search" autocomplete="off" v-model="q" @input="search()"
                   :placeholder="$t('search')" aria-label="Search" required itemprop="query" @focus="inputFocus()">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">
                {{ $t('search') }}
            </button>
        </form>
        <div :class="['collapse live_search', showResults ? 'show' : '']" id="liveSearch">
            <div class="list-group list-group-flush">
                <router-link :to="item.link" v-for="(item, key) in items" :key="key"
                             class="list-group-item list-group-item-action fw-bold">
                    {{ item.title }}
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {mapGetters} from "vuex";

export default {
    name: "SearchForm",
    computed: {
        ...mapGetters({
            locale: 'lang/locale'
        }),
        searchAction() {
            return '/' + this.locale + '/search'
        },
        searchResult() {
            return '/search-result'
        },
        showResults() {
            return !this.hideResult && this.items.length
        }
    },
    data() {
        return {
            appUrl: process.env.appUrl,
            q: this.$route.query.q,
            items: [],
            timeout: null,
            hideResult: true
        }
    },
    mounted() {
        this.$root.$el.addEventListener('click', () => {
            this.hideResult = true
        })
    },
    methods: {
        search() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.getItems()
            }, 800);
        },
        getItems() {
            this.items = [];
            this.hideResult = true;
            const data = {
                params: {
                    q: this.q,
                    limit: 5
                }
            };

            if (this.q) {
                axios.get(this.searchResult, data).then(response => {
                    this.items = response.data.data
                    this.hideResult = false;
                })
            }
        },
        inputFocus() {
            setTimeout(() => {
                if (this.items.length) {
                    this.hideResult = false;
                }
            }, 100)
        }
    }
}
</script>