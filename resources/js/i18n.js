import Vue from 'vue'
import languageBundle from '../../lang/index.js'
import VueI18n from 'vue-i18n'
import LaravelTranslationFormatter from './laravel-translation-formatter'

Vue.use(VueI18n)

export default new VueI18n({
  locale: document.documentElement.lang,
  fallbackLocale: 'de',
  messages: languageBundle,
  formatter: new LaravelTranslationFormatter({ locale: document.documentElement.lang })
})
