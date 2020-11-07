import Vue from 'vue'
import Vuex from 'vuex'
import users from './../../../../Modules/User/Resources/js/users'
import global from "./global";
import lang from "../../../../Modules/Language/Resources/js/lang";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    users,
    global,
    lang
  }
})