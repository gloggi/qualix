import Vue from 'vue'
import languageBundle from '../../lang/index.js'
import VueI18n from 'vue-i18n'
import LaravelTranslationFormatter from './laravelTranslationFormatter'

Vue.use(VueI18n)

export default new VueI18n({
  locale: document?.documentElement.lang || 'de',
  fallbackLocale: 'de',
  messages: languageBundle,
  formatter: new LaravelTranslationFormatter({ locale: document.documentElement.lang })
})
