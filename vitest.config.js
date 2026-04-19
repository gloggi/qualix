import { defineConfig, mergeConfig } from 'vitest/config'
import viteConfig from './vite.config.js'
import laravelTranslations from 'vite-plugin-laravel-translations'

const translationsPlugin = await laravelTranslations({
  namespace: false,
  absoluteLanguageDirectory: 'lang',
  includeJson: true,
  assertJsonImport: true,
})
const translationsConfig = await translationsPlugin.config()
const translations = translationsConfig.define['import.meta.env.VITE_LARAVEL_TRANSLATIONS']

const injectTranslationsPlugin = {
  name: 'inject-laravel-translations',
  enforce: 'pre',
  transform(code, id) {
    if (!id.includes('resources/js/i18n.js')) return
    return code.replace(
      /import\.meta\.env\.VITE_LARAVEL_TRANSLATIONS/g,
      `(${JSON.stringify(translations)})`
    )
  },
}

export default mergeConfig(viteConfig, defineConfig({
  plugins: [injectTranslationsPlugin],
  resolve: {
    extensions: ['.mjs', '.js', '.mts', '.ts', '.jsx', '.tsx', '.json', '.vue'],
  },
  test: {
    environment: 'jsdom',
    globals: true,
    include: ['tests/Vue/**/*.spec.js'],
    setupFiles: ['tests/Vue/setup.js'],
    testTimeout: 60000,
  },
}))
