/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import Vue from 'vue';

import router from "./router";
import i18n from "./i18n";
import store from "./store"

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import locale from 'element-ui/lib/locale/lang/ru-RU'
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue);

// const moment = require('moment')
// require('moment/locale/es')
//
// Vue.use(require('vue-moment'), {
//   moment
// })


import AdminApp from "./AdminApp";

/**
 * Components
 * */

Vue.use(ElementUI, {locale});

const app = new Vue({
    router,
    i18n,
    store,
    metaInfo: {
        title: 'Dashboard',
        titleTemplate: `%s | ${store.getters['global/appName']}`
    },
    render: (h) => h(AdminApp)
}).$mount('#app');