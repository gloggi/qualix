import Vue from 'vue'
import VueI18n from 'vue-i18n'
import LaravelTranslationFormatter from './laravelTranslationFormatter'

Vue.use(VueI18n)

export default new VueI18n({
  locale: document?.documentElement.lang || 'de',
  fallbackLocale: 'de',
  messages: import.meta.env.VITE_LARAVEL_TRANSLATIONS,
  formatter: new LaravelTranslationFormatter({ locale: document.documentElement.lang })
})
