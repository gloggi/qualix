import '@testing-library/jest-dom'
import './custom-matchers'
import { config } from '@vue/test-utils'
import i18n from '../../resources/js/i18n'
import Vue from 'vue'
import {BootstrapVue} from 'bootstrap-vue'

config.mocks['$t'] = (...args) => i18n.t(...args)
config.mocks['$te'] = (...args) => i18n.te(...args)

window.Laravel = {
  errors: {},
  oldInput: {},
}

Vue.use(BootstrapVue)
