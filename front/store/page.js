export const state = () => ({
    page: null,
    module_name: null,
    item: null,
    meta: {}
})

// getters
export const getters = {
    page: state => state.page,
    module: state => state.module_name,
    item: state => state.item,
    meta: state => state.meta
}

// mutations
export const mutations = {
    SET_PAGE(state, {page}) {
        state.page = page;
    },
    SET_MODULE(state, {module}) {
        state.module_name = module;
    },
    SET_ITEM(state, {item}) {
        state.item = item;
    },
    SET_META(state, {meta}) {
        state.meta = meta;
    },
}

// actions
export const actions = {
    setPage({commit}, page) {
        commit('SET_PAGE', {page})
    },
    setModule({commit}, module) {
        commit('SET_MODULE', {module})
    },
    setItem({commit}, item) {
        commit('SET_ITEM', {item})
    },
    setMeta({commit}, meta) {
        commit('SET_META', {meta})
    }
}