import '@testing-library/jest-dom'
import './custom-matchers'
import { config } from '@vue/test-utils'
import i18n from '../../resources/js/i18n'

config.mocks['$t'] = (...args) => i18n.t(...args)
config.mocks['$te'] = (...args) => i18n.te(...args)
config.mocks['$tc'] = (...args) => i18n.tc(...args)
