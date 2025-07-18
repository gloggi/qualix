import { createApp, defineAsyncComponent } from 'vue';
import i18n from './i18n.js';
import './svg.js';
import * as Sentry from '@sentry/vue';
import './bootstrap.js';
import {
  BAlert,
  BBadge,
  BButton,
  BCard,
  BCardHeader,
  BCollapse,
  BContainer,
  BDropdownForm,
  BDropdownItem,
  BFormSelect,
  BFormSelectOption,
  BInputGroupText,
  BLink,
  BListGroup,
  BListGroupItem,
  BModal,
  BNavbar,
  BNavbarBrand,
  BNavbarNav,
  BNavbarToggle,
  BNavForm,
  BNavItem,
  BNavItemDropdown,
  createBootstrap,
  vBModal,
  vBToggle,
  vBTooltip
} from 'bootstrap-vue-next';

import.meta.glob([
  '../images/**',
  '../fonts/**',
], { eager: true });

const app = createApp({})

app.use(createBootstrap())

const element = document.getElementById('laravel-data')
window.Laravel = JSON.parse(element.getAttribute('data-laravel'))
element.remove()
if (window.onEnvLoaded) window.onEnvLoaded()

app.config.globalProperties.$window = window

app.config.globalProperties.routeUri = function (name, parameters) {
  if (window.Laravel.routes[name] === undefined) {
    console.error('Route not found ', name)
  } else {
    const uri = new URL(
      window.Laravel.routes[name].uri
        .replace(/\{(.*?)(\?)?\}/g, (match, p1) => parameters && parameters.hasOwnProperty(p1) ? parameters[p1] : match)
        .replace(/\{.*?\}/g, match => Array.isArray(parameters) ? parameters.shift() : match)
        .replace(/\{.*?\?\}/g, ''),
      window.location.origin)

    if (parameters) {
      const mentionedParameters = (window.Laravel.routes[name].uri.match(/\{.*?\??\}/g) ?? [])
        .map(match => match.replace(/^\{|\??\}$/g, ''))
      const unmentionedParameters = Object.entries(parameters)
        .filter(([key, _]) => !mentionedParameters.includes(key))
      const queryParams = new URLSearchParams(uri.search)
      unmentionedParameters.forEach(([key, value]) => queryParams.set(key, value))
      uri.search = queryParams.toString()
    }
    return uri.toString()
  }
}
app.config.globalProperties.routeMethod = function (name, parameters) {
  if (window.Laravel.routes[name] === undefined) {
    console.error('Route not found ', name)
  } else {
    return window.Laravel.routes[name].method
  }
}

const allComponents = import.meta.glob('./components/**/*.vue')
for (const path in allComponents) {
  const fileName = path.split('/').slice(-1)[0]
  app.component(fileName.split('.')[0], defineAsyncComponent(allComponents[path]))
}
const bootstrapComponentsUsedInBlade = {
  BAlert,
  BBadge,
  BButton,
  BCard,
  BCardHeader,
  BCollapse,
  BContainer,
  BDropdownForm,
  BDropdownItem,
  BFormSelect,
  BFormSelectOption,
  BInputGroupText,
  BLink,
  BListGroup,
  BListGroupItem,
  BModal,
  BNavbar,
  BNavbarBrand,
  BNavbarNav,
  BNavbarToggle,
  BNavForm,
  BNavItem,
  BNavItemDropdown,
}
Object.entries(bootstrapComponentsUsedInBlade).forEach(([name, component]) => {
  app.component(name, component);
});
app.directive('b-toggle', vBToggle);
app.directive('b-tooltip', vBTooltip);
app.directive('b-modal', vBModal);

/**
 * Fix autofocus on form elements inside the Vue.js area of the page by adding v-focus additionally to autofocus:
 * <input type="text" autofocus v-focus>
 */
app.directive('focus', {
  inserted: function (el) {
    if (el.value !== undefined) {
      el.focus()
      let caretPos = el.value.length
      if (el.createTextRange) {
        // <textarea>
        var range = el.createTextRange()
        range.move('character', caretPos)
        range.select()
      } else if (['text', 'search', 'url', 'tel', 'password'].includes(el.type)) {
        // <input type="...">
        // setSelectionRange is only supported for the mentioned input types.
        // It doesn't work for email and number inputs:
        // https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/setSelectionRange
        el.setSelectionRange(caretPos, caretPos)
      }
    } else {
      // We might be in a vue-multiselect, search for the contained div.multiselect
      let multiselect = el.querySelector('div.multiselect')
      if (multiselect) {
        multiselect.focus()
      }
    }
  }
})

app.use(i18n)

if (import.meta.env.VITE_SENTRY_VUE_DSN && import.meta.env.VITE_SENTRY_VUE_DSN !== 'null') {
  Sentry.init({
    app: app,
    dsn: import.meta.env.VITE_SENTRY_VUE_DSN,
    logErrors: true,
  })
}

app.mount('#app')
