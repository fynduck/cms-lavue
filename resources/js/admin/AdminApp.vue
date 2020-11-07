<template>
    <el-main :class="['h-100', !user ? 'd-flex justify-content-center align-items-center' : '']">
        <el-header height="56px" class="p-0" v-if="user">
            <nav-bar :name="appName"></nav-bar>
        </el-header>
        <el-aside width="230" v-if="user">
            <side-bar ref="aside"></side-bar>
        </el-aside>
        <cl-canvasBg :is-color="true"></cl-canvasBg>
        <div id="content_wrapper" :class="[contentMin ? 'maxWidth' : '', !user ? 'container mx-auto' : '']">
            <transition name="slide" mode="out-in">
                <router-view :key="$route.path"></router-view>
            </transition>
        </div>
    </el-main>
</template>

<script>
    import CLCanvasBg from 'calamus-vue-canvas'
    import NavBar from "./components/NavBar";
    import SideBar from "./components/SideBar";
    import {mapGetters} from 'vuex'

    export default {
        name: "AdminApp",
        components: {
            CLCanvasBg,
            NavBar,
            SideBar
        },
        computed: mapGetters({
            appName: 'global/appName',
            contentMin: 'global/container',
            user: 'users/user'
        }),
        methods: {
            async transSlug(title) {
                return axios.get('/api/admin/trans-slug', {params: {txt: title}})
            }
        }
    }
</script>
<style lang="stylus" scoped>
    .slide-enter-active,
    .slide-leave-active
        transition opacity .5s

    .slide-enter,
    .slide-leave-to
        opacity 0
</style>