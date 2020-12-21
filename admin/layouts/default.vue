<template>
    <div>
        <header v-if="authenticated">
            <nav-bar/>
        </header>
        <side-bar ref="aside" v-if="authenticated"></side-bar>
        <main class="container-fluid position-relative" :class="{minWidth: contentMin && authenticated}">
            <nuxt/>
        </main>
    </div>
</template>

<script>
    import NavBar from '~/components/NavBar'
    import SideBar from "../components/SideBar";
    import {mapGetters} from "vuex";

    export default {
        components: {
            NavBar,
            SideBar
        },
        computed: mapGetters({
            contentMin: 'container',
            authenticated: 'auth/check'
        }),
    }
</script>