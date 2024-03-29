import Cookies from 'js-cookie'
import {cookieFromRequest} from '~/utils'

export const state = () => ({
    contentMin: false,
    affix: false,
    baseAPI: '',
    logo: process.env.app_name
})

// getters
export const getters = {
    container: state => state.contentMin,
    affix: state => state.affix,
    base: state => state.baseAPI,
    logo: state => state.logo,
}

// mutations
export const mutations = {
    SET_BASE(state, base) {
        state.baseAPI = base
    },
    SET_ROUTES(state, routes) {
        state.routes = routes
    },
    UPDATE_ROUTES(state, route) {
        state.routes.push(route)
    },
    SET_CONTAINER_WIDTH(state, width) {
        state.contentMin = width
    },
    SET_AFFIX(state, affix) {
        state.affix = affix
    },
    SET_LOGO(state, logo) {
        state.logo = logo
    }
}

export const actions = {
    saveContainerWidth({commit}, width) {
        commit('SET_CONTAINER_WIDTH', width)
    },
    saveAffix({commit}, affix) {
        commit('SET_AFFIX', affix)
    },
    saveLogo({commit}, logo) {
        commit('SET_LOGO', logo)
    },
    nuxtServerInit({commit}, {req}) {
        const token = cookieFromRequest(req, 'token')
        if (token) {
            commit('auth/SET_TOKEN', token)
        }

        const locale = cookieFromRequest(req, 'prefer_lang')
        if (locale) {
            commit('lang/SET_LOCALE', {locale})
        }

        commit('SET_BASE', process.env.apiUrl)
    },

    nuxtClientInit({commit}) {
        const token = Cookies.get('token')
        if (token) {
            commit('auth/SET_TOKEN', token)
        }

        const locale = Cookies.get('prefer_lang')
        if (locale) {
            commit('lang/SET_LOCALE', {locale})
        }

        commit('SET_BASE', process.env.apiUrl)
    }
}