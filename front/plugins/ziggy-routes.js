import Vue from 'vue'
import route from 'ziggy-js';
import {Ziggy} from "./../../resources/js/ziggy";

Vue.mixin({
    methods: {
        route: (name, params, absolute) => route(name, params, absolute, Ziggy),
    }
});