export const state = () => ({
    settings: []
})

// getters
export const getters = {
    settings: state => state.settings,
    logo: state => {
        const item = state.settings.find(item => item.key === 'logo')
        let logo = '';
        if (item) {
            logo = item.value;
        }

        return logo;
    }
}

// mutations
export const mutations = {
    SET_SETTINGS(state, {settings}) {
        state.settings = settings;
    },
    SET_LOGO(state, {logo}) {
        state.logo = logo;
    },
}

// actions
export const actions = {
    setSettings({commit}, settings) {
        commit('SET_SETTINGS', {settings})
    },
    setLogo({commit}, logo) {
        commit('SET_LOGO', {logo})
    }
}