import axios from 'axios'
import {loadMessages} from '../i18n'
import Cookies from "js-cookie";

export default function locale({next, store}) {
    let locale = store.getters['lang/locale']
    if (!locale)
        locale = Cookies.get('prefer_lang')

    if (locale) {
        axios.defaults.headers.common['Accept-Language'] = locale
        document.querySelector('html').setAttribute('lang', locale)
        loadMessages(locale).then(() => next)
    }

    return next()
}