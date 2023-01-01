module.exports = {
  testRegex: 'tests/Vue/.*.spec.js$',
  moduleFileExtensions: [
    'js',
    'json',
    'vue'
  ],
  transform: {
    'lang/index\\.js$' : '<rootDir>/tests/Vue/laravel-translations-loader.js',
    '^.+\\.js$': 'babel-jest',
    '.*\\.(vue)$': '@vue/vue2-jest',
  },
  setupFilesAfterEnv: ['<rootDir>/tests/Vue/jest-setup.js'],
  testEnvironment: 'jsdom',
}
