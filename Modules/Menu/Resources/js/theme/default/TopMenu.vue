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
                <form class="d-flex">
                    <input class="form-control me-2" type="search" :placeholder="$t('search')" aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
                <ul class="navbar-nav position-relative">
                    <li class="nav-item">
                        <a class="nav-link dropdown-toggle" href="#" id="languages" role="button" data-bs-toggle="dropdown">
                            {{ currentLang }}
                        </a>
                        <client-only>
                            <div class="dropdown-menu dropdown-menu-dark dropdown-menu-end" v-if="listLanguages.length > 0">
                                <a class="dropdown-item" href="#" v-for="lang in listLanguages" @click.prevent="setLocale(lang)">
                                    {{ lang.title }}
                                </a>
                            </div>
                        </client-only>
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
            show_child: false,
            open_lang: false
        }
    },
    computed: {
        ...mapGetters({
            locale: 'lang/locale',
            locales: 'lang/locales',
            languages: 'lang/languages',
            logo: 'settings/logo',
        }),
        currentLang() {
            const arrayLocales = Object.keys(this.locales)
            for (let i = 0; i < arrayLocales.length; i++) {
                if (this.locales[arrayLocales[i]].slug === this.locale)
                    return this.locales[arrayLocales[i]].name
            }

            return ''
        },
        listLanguages() {
            let list = [];
            if (Object.keys(this.languages).length > 0) {
                for (let key of Object.keys(this.languages)) {
                    list.push({
                        title: this.locales[key].name,
                        slug: this.locales[key].slug,
                        url: '/' + [this.locales[key].slug, this.languages[key]].join('/')
                    })
                }
            }

            return list;
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
        async setLocale(lang) {
            const locale = lang.slug;

            this.open_lang = false;
            if (this.locale !== locale) {
                await loadMessages(locale)

                await this.$store.dispatch('lang/setLocale', {locale})

                location.href = lang.url
            }
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
