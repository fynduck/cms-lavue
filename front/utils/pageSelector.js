// const loadComponent = import(`../../Modules/${module_name}/Resources/js/theme/${process.env.appTheme}/Page`);
// const loadComponent = require(`../../Modules/${module_name}/Resources/js/theme/${process.env.appTheme}/Page`)
import modules from '../../modules_statuses.json'

import axios from 'axios';

let module_name = null;

export default {
    functional: true,
    async validate({params}) {
        let nameModules = []
        for (let moduleName of Object.keys(modules)) {
            if (modules[moduleName])
                nameModules.push(moduleName)
        }

        let page = null;
        if (params.page) {
            const res = await axios.get(`/find-page/${params.page}`)
            module_name = res.data.data.module
            page = res.data.data;
        }

        if (!nameModules.includes(module_name))
            return false;

        if (params.category && page) {
            module_name = page.module
            console.log('cat', module_name, nameModules, nameModules.includes(module_name))
            if (!nameModules.includes(module_name)) {
                console.log('false')
                return false;
            }

            // const res = await axios.get(`/find-page/${params.page}`)
            // module_name = res.data.data.module
            // page = res.data.data;
        }

        let load = false;
        try {
            require(`../../Modules/${module_name}/Resources/js/theme/${process.env.appTheme}/Page`);
            load = true;
        } catch (e) {
        }

        return module_name && load
    },
    render(h) {
        // const test = import(`../../Modules/${module_name}/Resources/js/theme/${process.env.appTheme}/Page.vue`)
        const test = () => import(`../../Modules/${module_name}/Resources/js/theme/${process.env.appTheme}/Page`);
        console.log(test)
        return h(() => import(`../../Modules/${module_name}/Resources/js/theme/${process.env.appTheme}/Page.vue`))
    },
}