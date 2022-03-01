<template>
    <div>
        <header>
            <top-menu :app-name="title"/>
        </header>
        <main>
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
        try {
            const {data} = await axios.get(`/get-settings`)
            await this.$store.dispatch('settings/setSettings', data)
        } catch (e) {
        }
    },
    components: {
        TopMenu: () => import(`../../Modules/Menu/Resources/js/theme/${process.env.appTheme}/TopMenu`)
    },
    data: () => ({
        title: process.env.appName
    }),
    computed: {
        ...mapGetters({
            locale: 'lang/locale'
        })
    }
}
</script>
