import {
  translate,
  createCoreContext,
  registerMessageCompiler,
  compileToFunction,
  resolveValue,
} from '@intlify/core'
import translationData from '../../../../lang/index.js'

const createI18n = (language) => {
  registerMessageCompiler(compileToFunction)

  const context = createCoreContext({
    locale: language,
    fallbackLocale: 'de',
    messages: translationData,
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
