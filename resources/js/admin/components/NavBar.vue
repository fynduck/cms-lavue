<template>
    <b-navbar type="dark" variant="dark" fixed="top" toggleable="lg">
        <div class="navbar-branding" :class="{open : hideAside}">
            <router-link class="navbar-brand text-success" :to="{name: 'dashboard'}">
                <b>{{ name }}</b>
            </router-link>
            <i class="fas fa-outdent text-success toggle_aside" @click="toggleAside()"></i>
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
                    <b-dropdown-item href="#" v-for="(lang, lang_id) in locales" @click.prevent="setLocale(lang.slug)" :key="lang_id">
                        {{ lang.name }}
                    </b-dropdown-item>
                </b-nav-item-dropdown>
                <b-navbar-nav>
                    <b-nav-item href="/" v-b-tooltip.hover title="На сайт" target="_blank">
                        <i class="fa fa-home"></i>
                    </b-nav-item>
                    <b-nav-item @click.stop.prevent="logoutForm" v-b-tooltip.hover title="Выход">
                        <i class="fa fa-power-off"></i>
                    </b-nav-item>
                </b-navbar-nav>
            </b-navbar-nav>
        </b-collapse>
    </b-navbar>
</template>
<script>
    import {createNamespacedHelpers} from 'vuex';
    import {loadMessages} from "../i18n";

    const {mapGetters} = createNamespacedHelpers('lang');
    const {mapActions} = createNamespacedHelpers('global');

    export default {
        name: "NavBar",
        props: {
            name: {
                type: String,
                default: 'Name'
            }
        },
        data() {
            return {
                hideAside: false,
                nav_items: []
            };
        },
        computed: mapGetters({
            locale: 'locale',
            locales: 'locales'
        }),
        mounted() {
            if (window.innerWidth >= 1500)
                this.toggleAside(true)
        },
        methods: {
            ...mapActions(['saveContainerWidth', 'saveAffix']),
            async logoutForm() {
                this.$store.dispatch('users/logout').then(() => {
                    this.$router.push({name: 'login'})
                })
            },
            toggleAside(hide = false) {
                if (hide)
                    this.hideAside = true;
                else
                    this.hideAside = !this.hideAside;

                this.saveAffix(this.hideAside);

                this.saveContainerWidth(this.hideAside)
            },
            setLocale(locale) {
                if (this.$i18n.locale !== locale) {
                    loadMessages(locale)

                    this.$store.dispatch('lang/setLocale', locale)
                }
            }
        }
    }
</script>