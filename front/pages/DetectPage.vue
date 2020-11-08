<template>
    <component :is="componentInstance"/>
</template>

<script>
import modules from "../../modules_statuses.json";
import axios from "axios";
import {mapGetters} from "vuex";

export default {
    name: "DetectPage",
    computed: {
        ...mapGetters({
            page: 'page/page',
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
        if (!nameModules.includes(module))
            module = null;

        await this.$store.dispatch('page/setModule', module)
        await this.$store.dispatch('page/setPage', data.data)

        // if (typeof this.$attrs.category !== "undefined" && page) {
        //     // const res = await axios.get(`/find-page/${params.page}`)
        //     // module_name = res.data.data.module
        //     // page = res.data.data;
        // }
        //
        try {
            require(`../../Modules/${this.module_name}/Resources/js/theme/${process.env.appTheme}/Page`);
        } catch (e) {
            module = null
            await this.$store.dispatch('page/setModule', {module})
        }
    }
}
</script>