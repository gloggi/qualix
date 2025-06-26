import {
  translate,
  createCoreContext,
  registerMessageCompiler,
  compileToFunction,
  resolveValue,
} from '@intlify/core'

const createI18n = (language) => {
  registerMessageCompiler(compileToFunction)

  const context = createCoreContext({
    locale: language,
    fallbackLocale: 'de',
    messages: import.meta.env.VITE_LARAVEL_TRANSLATIONS,
    missingWarn: false,
    fallbackWarn: false,
    messageResolver: resolveValue,
  })

  return {
    translate: (...args) => {
      return translate(context, ...args)
    },
  }
}

export default createI18n
