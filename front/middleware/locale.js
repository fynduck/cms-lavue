import axios from 'axios'
import {loadMessages} from '~/plugins/i18n'

export default async ({store, route, params, redirect}) => {
    const locale = store.getters['lang/locale']
    if (process.server && locale) {
        axios.defaults.headers.common['Accept-Language'] = locale
    }

    await loadMessages(locale)

    if (params.lang !== locale && Object.keys(store.getters['lang/locales']).length > 1) {
        return redirect(`/${locale}${route.path}`)
    }
}