<template>
    <b-navbar type="dark" variant="dark" fixed="top" toggleable="lg">
        <div class="navbar-branding" :class="{open : hideAside}">
            <router-link class="navbar-brand text-success" :to="{name: 'dashboard.index'}">
                <b>{{ appName }}</b>
            </router-link>
            <fa :icon="['fas', 'outdent']" class="text-success toggle_aside" @click="toggleAside()"/>
        </div>
        <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>
        <b-collapse is-nav id="nav_collapse">
            <b-navbar-nav v-if="nav_items">
                <b-nav-item v-for="(item, index) in nav_items" :key="index" :href="item.route">
                    {{ item.title }}
                </b-nav-item>
            </b-navbar-nav>
            <b-navbar-nav class="ml-auto">
                <b-nav-item-dropdown class="text-capitalize" :text="locale" right>
                    <b-dropdown-item href="#" v-for="(lang, lang_id) in locales" @click.prevent="setLocale(lang.slug)"
                                     :key="lang_id">
                        {{ lang.name }}
                    </b-dropdown-item>
                </b-nav-item-dropdown>
                <b-navbar-nav>
                    <b-nav-item href="/" v-b-tooltip.hover :title="$t('to_site')" target="_blank">
                        <fa :icon="['fa', 'home']"/>
                    </b-nav-item>
                    <b-nav-item @click.stop.prevent="logoutForm" v-b-tooltip.hover :title="$t('logout')">
                        <fa :icon="['fa', 'power-off']"/>
                    </b-nav-item>
                </b-navbar-nav>
            </b-navbar-nav>
        </b-collapse>
    </b-navbar>
</template>
<script>
    import {mapGetters} from 'vuex'
    import { loadMessages } from '~/plugins/i18n'

    export default {
        data() {
            return {
                hideAside: false,
                nav_items: [],
                appName: process.env.appName
            };
        },
        computed: mapGetters({
            locale: 'lang/locale',
            locales: 'lang/locales'
        }),
        mounted() {
            if (window.innerWidth >= 1500)
                this.toggleAside(true)
        },
        methods: {
            async logoutForm() {
                this.$store.dispatch('auth/logout').then(() => {
                    this.$router.push({name: 'login'})
                })
            },
            toggleAside(hide = false) {
                if (hide)
                    this.hideAside = true;
                else
                    this.hideAside = !this.hideAside;

                this.$store.dispatch('global/saveAffix', this.hideAside)
                this.$store.dispatch('global/saveContainerWidth', this.hideAside)
            },
            setLocale(locale) {
                if (this.$i18n.locale !== locale) {
                    loadMessages(locale)

                    this.$store.dispatch('lang/setLocale', { locale })
                }
            }
        }
    }
</script>