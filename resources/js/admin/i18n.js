import Vue from 'vue'
import VueI18n from 'vue-i18n'
import store from "./store";

Vue.use(VueI18n)

function collectTransFiles(messages, currentLocale) {
    const modules = require('../../../modules_statuses.json');
    if (currentLocale) {
        for (let moduleName of Object.keys(modules)) {
            if (modules[moduleName]) {
                try {
                    const moduleTrans = require(`../../../Modules/${moduleName}/Resources/lang/${currentLocale}.json`);
                    messages[currentLocale] = Object.assign(moduleTrans, messages[currentLocale])
                } catch (e) {
                    console.log(e)
                }
            }
        }
    }
}

function loadLocaleMessages(currentLocale) {

    let messages = {};
    try {
        const locale = require(`./locales/${currentLocale}.json`)
        messages[currentLocale] = Object.assign(locale, messages[currentLocale])
    } catch (e) {
    }

    collectTransFiles(messages, currentLocale)

    return messages;
}

function getDefaultLocale() {
    store.dispatch('lang/getConfig').then(() => {
        return store.getters['lang/locale']
    })
}

const i18n = new VueI18n({
    locale: getDefaultLocale(),
    fallbackLocale: store.getters['lang/fallback'],
    messages: loadLocaleMessages(store.getters['lang/locale'])
});

export async function loadMessages(locale) {
    if (Object.keys(i18n.getLocaleMessage(locale)).length === 0) {
        const messages = loadLocaleMessages(locale)
        i18n.setLocaleMessage(locale, messages[locale])
    }

    if (i18n.locale !== locale)
        i18n.locale = locale
}

export default i18n