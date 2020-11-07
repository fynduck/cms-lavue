export const state = () => ({
    contentMin: false,
    affix: false
})

// getters
export const getters = {
    container: state => state.contentMin,
    affix: state => state.affix,
}

// mutations
export const mutations = {
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
export const actions = {
    saveContainerWidth({commit}, width) {
        commit('SET_CONTAINER_WIDTH', width)
    },
    saveAffix({commit}, affix) {
        commit('SET_AFFIX', affix)
    }
}