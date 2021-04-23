<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <router-link :to="`/${locale}`" class="navbar-brand">
                <img :src="logo" :alt="appName" v-if="logo">
                <span v-else>{{ appName }}</span>
            </router-link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topNav">
                <ul class="navbar-nav me-auto" v-if="items.length">
                    <li :class="['nav-item', item.children.length ? 'dropdown' : '']" v-for="item in items">
                        <router-link :to="item.link" :data-toggle="[item.children.length ? 'dropdown' : '']"
                                     :class="['nav-link', item.children.length ? 'dropdown-toggle' : '']"
                                     v-bind="parseCustomAttributes(item.attributes)"
                                     v-if="!checkPro(item.link)" :target="item.target">
                            {{ item.title }}
                        </router-link>
                        <a :class="['nav-link', item.children.length ? 'dropdown-toggle' : '']" :href="item.link" v-else
                           :target="item.target" v-bind="parseCustomAttributes(item.attributes)"
                           :data-toggle="[item.children.length ? 'dropdown' : null]">
                            {{ item.title }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topNavChild" v-if="item.children.length">
                            <a class="dropdown-item" :href="child.link" :target="child.target"
                               v-bind="parseCustomAttributes(child.attributes)"
                               v-if="checkPro(child.link)" v-for="child in item.children">
                                {{ child.title }}
                            </a>
                            <router-link :to="child.link" class="dropdown-item" :target="child.target"
                                         v-bind="parseCustomAttributes(child.attributes)" v-else>
                                {{ child.title }}
                            </router-link>
                        </div>
                    </li>
                </ul>
                <search-form></search-form>
                <client-only>
                    <select-language></select-language>
                </client-only>
            </div>
        </div>
    </nav>
</template>

<script>
import axios from 'axios'
import {mapGetters} from "vuex";

export default {
    name: 'TopMenu',
    components: {
        SearchForm: () => import(`../../../../../Search/Resources/js/theme/${process.env.appTheme}/components/SearchForm`),
        SelectLanguage: () => import(`../../../../../Language/Resources/js/theme/${process.env.appTheme}/components/SelectLanguage`),
    },
    props: {
        source: {
            type: String,
            required: true
        },
        appName: {
            type: String,
            default: 'Name'
        }
    },
    data() {
        return {
            items: []
        }
    },
    computed: {
        ...mapGetters({
            locale: 'lang/locale',
            logo: 'settings/logo'
        })
    },
    async fetch() {
        try {
            let {data} = await axios.get(this.source)
            this.items = data.data
        } catch (error) {
        }
    },
    methods: {
        checkPro(link) {
            const pattern = /^((http|https|ftp):\/\/)/;

            return pattern.test(link)
        },
        parseCustomAttributes(attributes) {
            let data = [];
            if (attributes) {
                const arrAttributes = attributes.split(',');
                for (let item of arrAttributes) {
                    const keyAndValue = item.split('=')

                    if (keyAndValue.length > 1) {
                        data.push({
                            [keyAndValue[0]]: keyAndValue[1]
                        })
                    }
                }
            }

            return data
        },
    }
}
</script>
