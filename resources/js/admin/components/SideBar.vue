<template>
    <div class="aside" :class="{affix: affix, hideScroll: overflowInit}">
        <skeleton-sideBar v-show="loading"></skeleton-sideBar>
        <ul class="nav_side sidebar-menu" v-show="items.length">
            <li v-for="item in items">
                <router-link :to="item.path">
                    <i :class="[item.iconClass || 'fas fa-align-justify']"></i>
                    <span class="sidebar-title">{{ $t(item.key_title) }}</span>
                    <span class="caret" v-show="item.children.length"></span>
                </router-link>
                <ul class="nav_side" v-if="item.children.length">
                    <li v-for="child in item.children">
                        <router-link :to="`${item.path}/${child.path}`">
                            <i :class="[child.iconClass || 'fas fa-align-justify']"></i>
                            {{ $t(child.key_title) }}
                        </router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</template>

<script>
    import SkeletonSideBar from './SkeletonSideBar';
    import {mapGetters} from 'vuex'

    export default {
        name: "SideBar",
        components: {
            SkeletonSideBar,
        },
        computed: {
            ...mapGetters({
                items: 'index/routes',
                affix: 'index/affix'
            })
        },
        data() {
            return {
                overflowInit: false,
                loading: false
            };
        }
    }
</script>