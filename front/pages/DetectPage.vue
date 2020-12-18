<template>
    <div>
        <component :is="componentInstance" v-if="!$fetchState.pending"/>
    </div>
</template>

<script>
import modules from "../../modules_statuses.json";
import axios from "axios";
import {mapGetters} from "vuex";

export default {
    name: "DetectPage",
    computed: {
        ...mapGetters({
            module_name: 'page/module'
        }),
        componentInstance() {
            if (this.module_name)
                return () => import(`../../Modules/${this.module_name}/Resources/js/theme/${process.env.appTheme}/Page`)
        }
    },
    async fetch() {
        let nameModules = []
        for (let moduleName of Object.keys(modules)) {
            if (modules[moduleName])
                nameModules.push(moduleName)
        }

        let module = null;
        const pageSlug = typeof this.$route.params.page !== "undefined" ? this.$route.params.page : 'home'
        const {data} = await axios.get(`/find-page/${pageSlug}`)
        module = data.data.module || 'Page'

        if (data.data.method === 'not_found' || !nameModules.includes(module)) {
            return this.$nuxt.error({statusCode: 404, message: data.data.title, page: data.data})
        }

        await this.$store.dispatch('page/setModule', module)
        await this.$store.dispatch('page/setPage', data.data)
        await this.$store.dispatch('lang/setPageLang', data.page_lang)
    }
}
</script>