import '@testing-library/jest-dom'
import './custom-matchers'
import { config } from '@vue/test-utils'
import i18n from '../../resources/js/i18n'
import { createBootstrap } from 'bootstrap-vue-next'

config.global.plugins = [i18n, createBootstrap()]

// Stub bootstrap-vue-next directives that access DOM internals not available in jsdom.
// These override locally-registered directives auto-imported by unplugin-vue-components.
const noopDirective = { beforeMount() {}, mounted() {}, updated() {}, beforeUnmount() {}, unmounted() {} }
config.global.directives = {
  'b-tooltip': noopDirective,
  bTooltip: noopDirective,
}

window.Laravel = {
  errors: {},
  oldInput: {},
}

// jsdom does not implement HTMLFormElement.prototype.submit; mock it so that
// tests which trigger programmatic form submission do not throw.
HTMLFormElement.prototype.submit = function () {
  this.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }))
}
