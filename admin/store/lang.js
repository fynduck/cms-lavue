import Cookies from 'js-cookie'
import axios from "axios";

// state
export const state = () => ({
    locale: process.env.appLocale,
    fallback: null,
    locales: {}
})

// getters
export const getters = {
    locale: state => state.locale,
    locales: state => state.locales,
    checkLocales: state => Object.keys(state.locales).length
}

// mutations
export const mutations = {
    SET_LOCALE(state, {locale}) {
        state.locale = locale;
    },
    SET_FALLBACK(state, fallback) {
        state.fallback = fallback
    },
    SET_LOCALES(state, locales) {
        state.locales = locales
    }
}

// actions
export const actions = {
    setLocale({commit}, {locale}) {
        commit('SET_LOCALE', {locale})

        Cookies.set('prefer_lang', locale, {expires: 365})
    },
    async getConfig({commit}) {

        try {
            const {data} = await axios.get(this.route('app-admin-data'))

            commit('SET_LOCALE', {locale: data.locale})
            commit('SET_FALLBACK', data.fallback)
            commit('SET_LOCALES', data.locales)
        } catch (e) {
            console.log(e)
        }
    }
}
