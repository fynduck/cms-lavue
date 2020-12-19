import Vue from 'vue';
import Router from "vue-router";
import DetectPage from "./pages/DetectPage";

Vue.use(Router)

let routes = [
    {
        path: '/:lang/:page?/:category?/:slug?',
        name: 'PageModule',
        component: DetectPage,
        props: true
    }
];

export function createRouter() {
    return new Router({
        routes,
        mode: 'history'
    })
}
