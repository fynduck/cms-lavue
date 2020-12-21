<template>
    <div>
        <header>
            <top-menu :app-name="title" :source="source_menu"/>
        </header>
        <main class="container">
            <nuxt/>
        </main>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
import axios from "axios";

export default {
    head() {
        return {
            htmlAttrs: {
                lang: this.locale
            },
            link: [
                {rel: 'preload', href: `/css/custom.css`, as: 'style'},
                {rel: 'stylesheet', href: `/css/custom.css`}
            ]
        };
    },
    async fetch() {
        const {data} = await axios.get(`/get-settings`)
        await this.$store.dispatch('settings/setSettings', data)
    },
    components: {
        TopMenu: () => import(`../../Modules/Menu/Resources/js/theme/${process.env.appTheme}/TopMenu`)
    },
    data: () => ({
        title: process.env.appName,
        source_menu: '/get-menu?position=top_menu'
    }),
    computed: {
        ...mapGetters({
            locale: 'lang/locale'
        })
    }
}
</script>
