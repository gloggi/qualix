const loader = require('@kirschbaum-development/laravel-translations-loader/all')
const path = require('path')

module.exports = {
  process(src, filename, config, options) {
    return loader.call({
      resource: filename,
      query: '',
      addDependency: () => {}
    })
  },
}
