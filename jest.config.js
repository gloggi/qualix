module.exports = {
  testRegex: 'tests/Vue/.*.spec.js$',
  moduleFileExtensions: [
    'js',
    'json',
    'vue'
  ],
  'transform': {
    '^.+\\.js$': '<rootDir>/node_modules/babel-jest',
    '.*\\.(vue)$': '<rootDir>/node_modules/vue-jest'
  },
  setupFilesAfterEnv: ['<rootDir>/tests/Vue/jest-setup.js'],
}
