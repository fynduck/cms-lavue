/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import {Select, Option, Dialog, Slider} from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import lang from 'element-ui/lib/locale/lang/ru-RU'
import locale from 'element-ui/lib/locale'

locale.use(lang);

Vue.use(Select);
Vue.use(Option);
Vue.use(Dialog);
Vue.use(Slider);

import ru from 'vee-validate/dist/locale/ru';
import VeeValidate, {Validator} from 'vee-validate';

Vue.use(VeeValidate);
Validator.localize('ru', ru);

import VueLazyload from 'vue-lazyload';

Vue.use(VueLazyload);

// const moment = require('moment-timezone');
// require('moment/locale/ru');
//
// Vue.use(require('vue-moment'), {
//     moment
// });

import ScrollTop from './components/ScrollTop';
import TimeDown from "./components/TimeDown";
import TopMenu from "../../Modules/Menu/Resources/js/theme/default/TopMenu";
import Articles from "../../Modules/Article/Resources/js/theme/default/Articles";

const app = new Vue({
    el: '#app',
    components: {
        TimeDown,
        ScrollTop,
        TopMenu,
        Articles
    },
    data() {
        return {
            loading: false,
            timeout: null
        }
    },
    created() {
        // if (typeof appLocale != "undefined")
        //     this.$moment.locale(appLocale);
    },
    mounted() {
        //
    },
    methods: {
        //
    }
});
