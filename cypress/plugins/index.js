/// <reference types="cypress" />
// ***********************************************************
// This example plugins/index.js can be used to load plugins
//
// You can change the location of this file or turn off loading
// the plugins file with the 'pluginsFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/plugins-guide
// ***********************************************************

const pdf = require('pdf-parse')
const fs = require('fs');
const path = require('path')

// This function is called when a project is opened or re-opened (e.g. due to
// the project's config changing)

/**
 * @type {Cypress.PluginConfig}
 */
module.exports = (on, config) => {
    // `on` is used to hook into various events Cypress emits
    // `config` is the resolved Cypress config

    on('task', require('./swap-env'));
    on('task', {
      async findFiles (mask) {
        if (!mask) {
          throw new Error('Missing a file mask to search')
        }

        console.log('searching for files %s', mask)
        const globby = (await import('globby')).globby
        const list = await globby(mask)

        if (!list.length) {
          console.log('found no files')

          return null
        }

        console.log('found %d files, first one %s', list.length, list[0])

        return list[0]
      },
      async parsePdf (filename) {
        if (!filename) {
          throw new Error('Missing filename of pdf file to parse')
        }

        return new Promise((resolve) => {
          const resolvedPath = path.resolve(filename)
          const dataBuffer = fs.readFileSync(resolvedPath)
          pdf(dataBuffer).then(result => resolve(result))
        })
      },
      async deleteFiles (mask) {
        if (!mask) {
          throw new Error('Missing a file mask to search')
        }

        console.log('searching for files %s', mask)
        const globby = (await import('globby')).globby
        const list = await globby(mask)

        const fs = await import('fs')
        list.forEach(file => {
          fs.rmSync(file)
        })
        console.log('deleted %d files', list.length)
        return list.length
      }
    })
};
