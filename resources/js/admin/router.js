import Vue from 'vue';
import VueRouter from "vue-router";
import store from "./store";
import Cookies from 'js-cookie'
import VueMeta from 'vue-meta'

Vue.use(VueRouter)
Vue.use(VueMeta)

import locale from "./middleware/locale";
import checkAuth from "./middleware/check-auth";
import admin from "./middleware/admin";
import middlewarePipeline from "./middlewarePipeline";
import moduleIconsSort from "./iconSortModule";

function checkModuleSetting(name, key) {
    if (typeof moduleIconsSort[name] !== "undefined" && typeof moduleIconsSort[name][key] !== "undefined")
        return moduleIconsSort[name][key]

    return null;
}

const authPage = path => () => import(`../auth/${path}`).then(m => m.default || m)

let routes = [
    {
        path: '/login',
        name: 'login',
        component: authPage('login.vue'),
        meta: {
            title: 'Login',
            metaTags: [
                {
                    name: 'description',
                    content: 'The about page of our example app.'
                },
                {
                    property: 'og:description',
                    content: 'The about page of our example app.'
                }
            ],
            middleware: [
                locale,
                checkAuth
            ]
        }
    },
    {
        path: '/register',
        name: 'register',
        component: authPage('register.vue'),
        meta: {
            title: 'Register',
            middleware: [
                locale
            ]
        }
    },
    {
        path: '/password/reset',
        name: 'password.request',
        component: authPage('password/email.vue'),
        meta: {
            title: 'Password reset',
            middleware: [
                locale
            ]
        }
    },
    {
        path: '/password/reset/:token',
        name: 'password.reset',
        component: authPage('password/reset.vue'),
        meta: {
            title: 'Password reset',
            middleware: [
                locale
            ]
        }
    },
    {
        path: '/email/verify/:id',
        name: 'verification.verify',
        component: authPage('verification/verify.vue'),
        meta: {
            title: 'Verification',
            middleware: [
                locale
            ]
        }
    },
    {
        path: '/email/resend',
        name: 'verification.resend',
        component: authPage('verification/resend.vue'),
        meta: {
            title: 'Verification resend',
            middleware: [
                locale
            ]
        }
    },
    {
        path: '/admin/dashboard',
        name: 'dashboard',
        component: () => import('./pages/Dashboard'),
        meta: {
            key_title: 'dashboard',
            iconClass: checkModuleSetting('dashboard', 'iconClass'),
            sort: 0,
            middleware: [
                locale,
                checkAuth,
                admin
            ]
        }
    }
];

import modules from '../../../modules_statuses.json'

for (let moduleName of Object.keys(modules)) {
    if (modules[moduleName]) {
        const moduleSplitWords = moduleName.replace(/([a-z0-9])([A-Z])/g, '$1 $2');
        const arrayWords = moduleSplitWords.split(' ');

        let children = []

        if (moduleName === 'Settings') {
            children.push({
                path: 'site',
                name: `${arrayWords.join('-').toLowerCase()}-site.index`,
                component: () => import(`../../../Modules/${moduleName}/Resources/js/admin/${moduleName}Site.vue`),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_site`,
                    iconClass: null,
                    middleware: [
                        locale,
                        checkAuth,
                        admin
                    ]
                }
            })
            children.push({
                path: 'css',
                name: `${arrayWords.join('-').toLowerCase()}-css.index`,
                component: () => import(`../../../Modules/${moduleName}/Resources/js/admin/${moduleName}Css.vue`),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_css`,
                    iconClass: null,
                    middleware: [
                        locale,
                        checkAuth,
                        admin
                    ]
                }
            })
            children.push({
                path: 'socials',
                name: `${arrayWords.join('-').toLowerCase()}-socials.index`,
                component: () => import(`../../../Modules/${moduleName}/Resources/js/admin/${moduleName}Socials.vue`),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_socials`,
                    iconClass: null,
                    middleware: [
                        locale,
                        checkAuth,
                        admin
                    ]
                }
            })
            children.push({
                path: 'paginate',
                name: `${arrayWords.join('-').toLowerCase()}-paginate.index`,
                component: () => import(`../../../Modules/${moduleName}/Resources/js/admin/${moduleName}Paginate.vue`),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_paginate`,
                    iconClass: null,
                    middleware: [
                        locale,
                        checkAuth,
                        admin
                    ]
                }
            })
            children.push({
                path: 'env',
                name: `${arrayWords.join('-').toLowerCase()}-env.index`,
                component: () => import(`../../../Modules/${moduleName}/Resources/js/admin/${moduleName}Env.vue`),
                meta: {
                    key_title: `${arrayWords.join('_').toLowerCase()}_env`,
                    iconClass: null,
                    middleware: [
                        locale,
                        checkAuth,
                        admin
                    ]
                }
            })
        }
        let routeParams = {
            path: `/admin/${arrayWords.join('-').toLowerCase()}`,
            name: `${arrayWords.join('-').toLowerCase()}.index`,
            children: children,
            component: () => import(`../../../Modules/${moduleName}/Resources/js/admin/${moduleName}List.vue`),
            meta: {
                key_title: `${arrayWords.join('_').toLowerCase()}`,
                iconClass: checkModuleSetting(arrayWords.join('_').toLowerCase(), 'iconClass'),
                sort: checkModuleSetting(arrayWords.join('_').toLowerCase(), 'sort'),
                middleware: [
                    locale,
                    checkAuth,
                    admin
                ]
            }
        }

        if (children.length)
            routeParams.redirect = {name: children[0].name};
        
        routes.push(routeParams)

        if (!['Settings', 'Translate'].includes(moduleName)) {
            routes.push({
                path: `/admin/${arrayWords.join('-').toLowerCase()}/create`,
                name: `${arrayWords.join('-').toLowerCase()}.create`,
                component: () => import(`../../../Modules/${moduleName}/Resources/js/admin/${moduleName}Create`),
                meta: {
                    key_title: `add_${arrayWords.join('_').toLowerCase()}`,
                    middleware: [
                        locale,
                        checkAuth,
                        admin
                    ]
                }
            })
            routes.push({
                path: `/admin/${arrayWords.join('-').toLowerCase()}/:id`,
                name: `${arrayWords.join('-').toLowerCase()}.edit`,
                component: () => import(`../../../Modules/${moduleName}/Resources/js/admin/${moduleName}Create`),
                meta: {
                    key_title: `edit_${arrayWords.join('_').toLowerCase()}`,
                    middleware: [
                        locale,
                        checkAuth,
                        admin
                    ]
                }
            })
        }
    }
}

const router = new VueRouter({
    routes: routes,
    mode: 'history'
})

router.beforeEach((to, from, next) => {
    if (!to.meta.middleware)
        return next()

    const middleware = to.meta.middleware
    const context = {
        to,
        from,
        next,
        store,
        Cookies
    }
    return middleware[0]({
        ...context,
        next: middlewarePipeline(context, middleware, 1)
    })
})

export default router