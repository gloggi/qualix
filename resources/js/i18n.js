import { createI18n } from 'vue-i18n'
import laravelTranslationCompiler from './laravelTranslationCompiler.js'

const i18n = createI18n({
  legacy: false,
  global: true,
  locale: document?.documentElement.lang || 'de',
  fallbackLocale: 'de',
  messages: import.meta.env.VITE_LARAVEL_TRANSLATIONS,
  messageCompiler: laravelTranslationCompiler,
})

export default i18n
