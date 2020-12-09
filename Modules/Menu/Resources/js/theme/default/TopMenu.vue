<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <router-link :to="`/${locale}`" class="navbar-brand">
                {{ appName }}
            </router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topNav"
                    aria-controls="topNav"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topNav">
                <ul class="navbar-nav mr-auto" v-if="items.length">
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
                <form class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="languages" role="button" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            {{ currentLang }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languages"
                             v-if="Object.keys(locales).length > 1">
                            <a class="dropdown-item" href="#" v-for="lang in locales"
                               @click.prevent="setLocale(lang.slug)">
                                {{ lang.name }}
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
import axios from 'axios'
import {mapGetters} from "vuex";
import {loadMessages} from '~/plugins/i18n'

export default {
    name: 'TopMenu',
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
            items: [],
            show_child: false
        }
    },
    computed: {
        ...mapGetters({
            locale: 'lang/locale',
            locales: 'lang/locales',
        }),
        currentLang() {
            const arrayLocales = Object.keys(this.locales)
            for (let i = 0; i < arrayLocales.length; i++) {
                if (this.locales[arrayLocales[i]].slug === this.locale)
                    return this.locales[arrayLocales[i]].name
            }

            return ''
        }
    },
    async fetch() {
        let {data} = await axios.get(this.source)
        this.items = data.data
    },
    methods: {
        checkPro(link) {
            const pattern = /^((http|https|ftp):\/\/)/;

            return pattern.test(link)
        },
        async setLocale(locale) {
            if (this.locale !== locale) {
                await loadMessages(locale)

                await this.$store.dispatch('lang/setLocale', {locale})
                const route = Object.assign({}, this.$route);
                route.params.lang = locale;

                this.$router.push(route)

                this.$nextTick(function () {
                    this.$fetch()
                })
            }
        },
        parseCustomAttributes(attributes) {
            if (attributes) {
                let data = [];
                const arrAttributes = attributes.split(',');
                for (let item of arrAttributes) {
                    const keyAndValue = item.split('=')

                    if (keyAndValue.length > 1) {
                        data.push({
                            [keyAndValue[0]]: keyAndValue[1]
                        })
                    }
                }

                return data
            }

            return [];
        },
    }
}
</script>
