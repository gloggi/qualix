const loader = require('@kirschbaum-development/laravel-translations-loader/all')
const path = require('path')

module.exports = {
  process(src, filename, config, options) {
    return loader.bind({
      resource: filename,
      query: '',
      addDependency: () => {}
    })();
  },
};
