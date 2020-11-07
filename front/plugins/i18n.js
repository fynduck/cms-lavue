import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

const i18n = new VueI18n({
    locale: 'ru',
    messages: {}
})

export default async ({app, store}) => {
    if (!store.getters['lang/checkLocales'])
        await store.dispatch('lang/getConfig');

    if (process.client) {
        await loadMessages(store.getters['lang/locale'])
    }

    app.i18n = i18n
}

/**
 * @param {String} locale
 */
export async function loadMessages(locale) {
    if (Object.keys(i18n.getLocaleMessage(locale)).length === 0) {
        const messages = await loadLocaleMessages(locale)
        i18n.setLocaleMessage(locale, messages[locale])
    }

    if (i18n.locale !== locale) {
        i18n.locale = locale
    }
}


function collectTransFiles(messages, currentLocale) {
    const modules = require('../../modules_statuses.json');
    if (currentLocale) {
        for (let moduleName of Object.keys(modules)) {
            if (modules[moduleName]) {
                try {
                    const moduleTrans = require(`../../Modules/${moduleName}/Resources/lang/${currentLocale}.json`);
                    messages[currentLocale][moduleName] = Object.assign(moduleTrans, messages[currentLocale])
                } catch (e) {
                }
            }
        }
    }
}

function loadLocaleMessages(currentLocale) {

    let messages = {};
    try {
        const locale = require(`~/lang/${currentLocale}.json`)
        messages[currentLocale] = Object.assign(locale, messages[currentLocale])
    } catch (e) {
    }

    collectTransFiles(messages, currentLocale)

    return messages;
}