<template>
    <ul class="navbar-nav position-relative" v-if="showLanguages">
        <li class="nav-item">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                {{ currentLang }}
            </a>
            <div class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                <a class="dropdown-item" href="#" v-for="lang in listLanguages" @click.prevent="setLocale(lang)">
                    {{ lang.title }}
                </a>
            </div>
        </li>
    </ul>
</template>

<script>
import {mapGetters} from "vuex";
import {loadMessages} from '~/plugins/i18n'

export default {
    name: "SelectLanguage",
    computed: {
        ...mapGetters({
            locale: 'lang/locale',
            locales: 'lang/locales',
            languages: 'lang/languages'
        }),
        currentLang() {
            const arrayLocales = Object.keys(this.locales)
            for (const item of arrayLocales) {
                if (this.locales[item].slug === this.locale)
                    return this.locales[item].name
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
        },
        showLanguages() {
            return this.listLanguages.length
        }
    },
    data() {
        return {
            items: [],
            open_lang: false
        }
    },
    methods: {
        async setLocale(lang) {
            const locale = lang.slug;

            this.open_lang = false;
            if (this.locale !== locale) {
                await loadMessages(locale)

                await this.$store.dispatch('lang/setLocale', {locale})

                location.href = lang.url
            }
        },
    }
}
</script>