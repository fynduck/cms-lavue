export const state = () => ({
    page: null
})

// getters
export const getters = {
    page: state => state.page
}

// mutations
export const mutations = {
    SET_PAGE (state, { page }) {
        state.page = page;
    },
}

// actions
export const actions = {
    setPage ({ commit }, { page }) {
        commit('SET_PAGE', { page })
    }
}