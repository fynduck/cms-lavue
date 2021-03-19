<template>
    <aside class="aside" :class="{affix: affix, hideScroll: overflowInit}">
        <skeleton-sideBar v-show="loading"></skeleton-sideBar>
        <b-list-group>
            <b-list-group-item v-for="(route, index) in routes" :key="index">
                <router-link :to="{name: route.name}">
                    <fa :icon="route.meta.iconClass" v-if="route.meta.iconClass"/>
                    <fa :icon="['fas', 'align-justify']" v-else/>
                    <span class="sidebar-title">{{ $t(generateTransKey(route)) }}</span>
                    <fa :icon="['fas', 'chevron-right']" class="float-right"
                        v-show="typeof route.children !== 'undefined' && route.children.length"/>
                </router-link>
                <b-list-group v-if="typeof route.children !== 'undefined' && route.children.length">
                    <router-link class="list-group-item" :to="{name: child.name}" v-for="(child, key) in route.children"
                                 :key="key">
                        <fa :icon="child.meta.iconClass" v-if="child.meta.iconClass"/>
                        <fa :icon="['fas', 'align-justify']" v-else/>
                        {{ $t(generateTransKey(child)) }}
                    </router-link>
                </b-list-group>
            </b-list-group-item>
        </b-list-group>
    </aside>
</template>

<script>
import {mapGetters} from 'vuex'
import SkeletonSideBar from './SkeletonSideBar';
import {checkPermission} from "../utils";

export default {
    name: "SideBar",
    components: {
        SkeletonSideBar,
    },
    computed: {
        ...mapGetters({
            affix: 'affix',
            user: 'auth/user',
            locale: 'lang/locale',
            checkLocales: 'lang/checkLocales'
        }),
        routes() {
            let routers = [];
            this.$router.options.routes.forEach(router => {
                if ((typeof router.meta !== "undefined" && typeof router.meta.sort !== "undefined") &&
                    (this.user.admin || checkPermission(router.name, this.user.permissions))) {

                    if (!this.user.admin && router.children) {
                        let routeWithChild = router;
                        router.children.forEach(child => {
                            if (child.name !== 'settings-site.index' && !checkPermission(child.name, this.user.permissions)) {
                                let keyItem = routeWithChild.children.findIndex(item => item.name === child.name)
                                routeWithChild.children.splice(keyItem, 1)
                            }
                        })

                        routers.push(routeWithChild)

                    } else {
                        routers.push(router)
                    }
                }
            });

            return routers.sort(function (a, b) {
                return a.meta.sort - b.meta.sort
            });
        }
    },
    data() {
        return {
            overflowInit: false,
            loading: false,
        };
    },
    methods: {
        generateTransKey(route) {
            let moduleName = route.name.split('.')[0];

            if (moduleName.split('-')[0] === 'settings') {
                moduleName = moduleName.split('-')[0].replace(/^\w/, (c) => c.toUpperCase());
            } else {
                moduleName = moduleName.split('-').join(' ').replace(/\w\S*/g, (w) => (w.replace(/^\w/, (c) => c.toUpperCase())));
            }

            let title = '';
            if (route.meta.key_title !== 'dashboard') {
                title = moduleName.replace(' ', '') + '.' + route.meta.key_title;

                if (route.meta.key_title.search('settings') < 0 &&
                    route.meta.key_title.search('menu') < 0 &&
                    route.meta.key_title.search('translate') < 0 &&
                    title.charAt(title.length - 1) !== 's') {
                    title += 's'
                }
            } else {
                title = moduleName.toLowerCase()
            }

            return title;
        }
    }
}
</script>