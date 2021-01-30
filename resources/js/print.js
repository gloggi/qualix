window.onEnvLoaded = () => {
  window.PagedConfig = { auto: false, settings: { nonce: window.Laravel.nonce } }
  require('./paged.polyfill')
}
