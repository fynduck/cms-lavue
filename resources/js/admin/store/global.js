import router from "../router";

// state
const state = {
    appName: document.documentElement.getAttribute('data-name'),
    routes: [],
    contentMin: false,
    affix: false
}

// getters
const getters = {
    appName: state => state.appName,
    routes: state => state.routes,
    container: state => state.contentMin,
    affix: state => state.affix,
}

// mutations
const mutations = {
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
    }
}

// actions
const actions = {
    async saveRoutes({commit, rootGetters}) {
        let routes = {};
        commit('SET_ROUTES', [])
        router.options.routes.forEach(route => {
            if (route.path.startsWith('/admin') && typeof route.meta.sort !== "undefined" && rootGetters['users/check']) {
                const arrayName = route.name.split('.');
                const right = arrayName[arrayName.length - 1];
                arrayName.pop()

                if (!route.meta.middleware ||
                    rootGetters['users/user'].admin ||
                    (typeof rootGetters['users/user'].permissions[arrayName.join('.')] !== "undefined" && rootGetters['users/user'].permissions[arrayName.join('.')].includes(right))) {
                    let children = [];
                    if (typeof route.children !== "undefined" && route.children.length) {
                        route.children.forEach(child => {
                                children.push({
                                    path: child.path,
                                    name: child.name,
                                    key_title: child.meta.key_title,
                                    iconClass: child.meta.iconClass
                                })
                        })
                    }
                    routes[route.meta.sort] = {
                        name: route.name,
                        key_title: route.meta.key_title,
                        iconClass: route.meta.iconClass,
                        path: route.path,
                        children: children
                    }
                }
            }
        })

        for (let key of Object.keys(routes))
            commit('UPDATE_ROUTES', routes[key])
    },
    saveContainerWidth({commit}, width) {
        commit('SET_CONTAINER_WIDTH', width)
    },
    saveAffix({commit}, affix) {
        commit('SET_AFFIX', affix)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}