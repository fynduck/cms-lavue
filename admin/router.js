import Vue from 'vue';
import Router from "vue-router";
import {moduleIconsSort} from '~/utils'

Vue.use(Router)

const page = path => () => import(`~/pages/${path}`).then(m => m.default || m)

let routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import(`../Modules/User/Resources/js/auth/login.vue`).then(m => m.default || m)
    },
    {
        path: '/register',
        name: 'register',
        component: () => import(`../Modules/User/Resources/js/auth/register.vue`).then(m => m.default || m)
    },
    {
        path: '/password/reset',
        name: 'password.request',
        component: () => import(`../Modules/User/Resources/js/auth/password/email.vue`).then(m => m.default || m)
    },
    {
        path: '/password/reset/:token',
        name: 'password.reset',
        component: () => import(`../Modules/User/Resources/js/auth/password/reset.vue`).then(m => m.default || m)
    },
    {
        path: '/email/verify/:id',
        name: 'verification.verify',
        component: () => import(`../Modules/User/Resources/js/auth/verification/verify.vue`).then(m => m.default || m)
    },
    {
        path: '/email/resend',
        name: 'verification.resend',
        component: () => import(`../Modules/User/Resources/js/auth/verification/resend.vue`).then(m => m.default || m)
    },
    {
        path: '/admin/dashboard',
        name: 'dashboard.index',
        component: page('dashboard.vue'),
        meta: {
            key_title: 'dashboard',
            iconClass: moduleIconsSort('dashboard', 'iconClass'),
            sort: 0,
        }
    }
];

import modules from '../modules_statuses.json'

for (let moduleName of Object.keys(modules)) {
    if (modules[moduleName]) {
        const moduleSplitWords = moduleName.replace(/([a-z0-9])([A-Z])/g, '$1 $2');
        const arrayWords = moduleSplitWords.split(' ');

        let children = []

        if (moduleName === 'Settings') {
            children.push({
                path: 'site',
                name: `${arrayWords.join('-').toLowerCase()}-site.index`,
                component: () => import(`../Modules/${moduleName}/Resources/js/admin/${moduleName}Site.vue`).then(m => m.default || m),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_site`,
                    iconClass: null,
                }
            })
            children.push({
                path: 'css',
                name: `${arrayWords.join('-').toLowerCase()}-css.index`,
                component: () => import(`../Modules/${moduleName}/Resources/js/admin/${moduleName}Css.vue`).then(m => m.default || m),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_css`,
                    iconClass: null,
                }
            })
            children.push({
                path: 'socials',
                name: `${arrayWords.join('-').toLowerCase()}-socials.index`,
                component: () => import(`../Modules/${moduleName}/Resources/js/admin/${moduleName}Socials.vue`).then(m => m.default || m),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_socials`,
                    iconClass: null,
                }
            })
            children.push({
                path: 'paginate',
                name: `${arrayWords.join('-').toLowerCase()}-paginate.index`,
                component: () => import(`../Modules/${moduleName}/Resources/js/admin/${moduleName}Paginate.vue`).then(m => m.default || m),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_paginate`,
                    iconClass: null,
                }
            })
            children.push({
                path: 'env',
                name: `${arrayWords.join('-').toLowerCase()}-env.index`,
                component: () => import(`../Modules/${moduleName}/Resources/js/admin/${moduleName}Env.vue`).then(m => m.default || m),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_env`,
                    iconClass: null,
                }
            })
        }
        let routeParams = {
            path: `/admin/${arrayWords.join('-').toLowerCase()}`,
            name: `${arrayWords.join('-').toLowerCase()}.index`,
            children: children,
            component: () => import(`../Modules/${moduleName}/Resources/js/admin/${moduleName}List.vue`).then(m => m.default || m),
            meta: {
                key_title: `${arrayWords.join('_').toLowerCase()}`,
                iconClass: moduleIconsSort(arrayWords.join('_').toLowerCase(), 'iconClass'),
                sort: moduleIconsSort(arrayWords.join('_').toLowerCase(), 'sort')
            }
        }

        if (children.length)
            routeParams.redirect = {name: children[0].name};

        routes.push(routeParams)

        if (!['Settings', 'Translate'].includes(moduleName)) {
            routes.push({
                path: `/admin/${arrayWords.join('-').toLowerCase()}/create`,
                name: `${arrayWords.join('-').toLowerCase()}.create`,
                component: () => import(`../Modules/${moduleName}/Resources/js/admin/${moduleName}Create.vue`).then(m => m.default || m),
                meta: {
                    key_title: `add_${arrayWords.join('_').toLowerCase()}`,
                }
            })
            routes.push({
                path: `/admin/${arrayWords.join('-').toLowerCase()}/:id`,
                name: `${arrayWords.join('-').toLowerCase()}.edit`,
                component: () => import(`../Modules/${moduleName}/Resources/js/admin/${moduleName}Create.vue`).then(m => m.default || m),
                meta: {
                    key_title: `edit_${arrayWords.join('_').toLowerCase()}`,
                }
            })
        }
    }
}

export function createRouter() {
    return new Router({
        routes,
        mode: 'history'
    })
}