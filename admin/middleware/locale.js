import axios from 'axios'
import { loadMessages } from '~/plugins/i18n'

export default async ({ store }) => {
  const locale = store.getters['lang/locale']
  if (process.server && locale) {
      axios.defaults.headers.common['Accept-Language'] = locale
  }

  await loadMessages(locale)
}