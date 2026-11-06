import {
  translate,
  createCoreContext,
  resolveValue,
} from '@intlify/core'
import laravelTranslationCompiler from '../../laravelTranslationCompiler.js'

const createI18n = (language) => {
  const context = createCoreContext({
    locale: language,
    fallbackLocale: 'de',
    messages: import.meta.env.VITE_LARAVEL_TRANSLATIONS,
    missingWarn: false,
    fallbackWarn: false,
    messageCompiler: laravelTranslationCompiler,
    messageResolver: resolveValue,
  })

  return {
    translate: (...args) => {
      return translate(context, ...args)
    },
  }
}

export default createI18n
