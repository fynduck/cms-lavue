<template>
    <component :is="componentInstance"/>
</template>

<script>
    import modules from "../../modules_statuses.json";
    import axios from "axios";

    export default {
        name: "DetectPage",
        computed: {
            componentInstance() {
                if (this.module_name)
                    return () => import(`../../Modules/${this.module_name}/Resources/js/theme/${process.env.appTheme}/Page`)
            }
        },
        watch: {
            '$route.query': '$fetch'
        },
        async fetch() {
            let nameModules = []
            for (let moduleName of Object.keys(modules)) {
                if (modules[moduleName])
                    nameModules.push(moduleName)
            }
            console.log(this.$attrs.page)

            let page = null;
            const pageSlug = typeof this.$attrs.page !== "undefined" ? this.$attrs.page : 'home'
            const {data} = await axios.get(`/find-page/${pageSlug}`)
            this.module_name = data.data.module || 'Page'
            page = data.data;
            this.$store.dispatch('page/setPage', { page })

            if (!nameModules.includes(this.module_name))
                this.module_name = null

            // if (typeof this.$attrs.category !== "undefined" && page) {
            //     // const res = await axios.get(`/find-page/${params.page}`)
            //     // module_name = res.data.data.module
            //     // page = res.data.data;
            // }
            //
            try {
                require(`../../Modules/${this.module_name}/Resources/js/theme/${process.env.appTheme}/Page`);
            } catch (e) {
                this.module_name = null
            }
        },
        data() {
            return {
                module_name: null
            }
        },
        mounted() {
            console.log(1)
        }
    }
</script>