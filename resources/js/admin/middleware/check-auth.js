export default function checkAuth({next, store, Cookies}) {

    let token = store.getters['users/token'] || Cookies.get('token')
    if (!token)
        delete axios.defaults.headers.common.Authorization

    store.dispatch('users/saveToken', {
        token: token
    })

    if (!store.getters['users/check'] && token) {
        axios.defaults.headers.common.Authorization = `Bearer ${token}`
        store.dispatch('users/fetchUser').then(() => {
            store.dispatch('global/saveRoutes')
            return next()
        }).catch(() => {
            store.dispatch('users/refreshToken')
        })
    } else {
        return next({
            name: 'login'
        })
    }
}