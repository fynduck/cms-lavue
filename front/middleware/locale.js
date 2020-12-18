import axios from 'axios'
import {loadMessages} from '~/plugins/i18n'

export default async ({store, route, params, redirect}) => {
    let locale = store.getters['lang/locale']
    const locales = store.getters['lang/locales']

    if (typeof params.lang === "undefined") {
        return redirect(`/${locale}${route.path}`)
    }
    if (typeof params.lang !== "undefined" && params.lang !== locale) {
        let urlLocale = Object.values(locales).find(item => item.slug === params.lang)
        if (typeof urlLocale !== "undefined") {
            locale = urlLocale.slug;
            await store.dispatch('lang/setLocale', {locale})
        }
    }

    if (process.server && locale) {
        axios.defaults.headers.common['Accept-Language'] = locale
    }

    await loadMessages(locale)
}