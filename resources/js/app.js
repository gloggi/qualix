import Vue from 'vue'
import languageBundle
  from '@kirschbaum-development/laravel-translations-loader!@kirschbaum-development/laravel-translations-loader'
import VueI18n from 'vue-i18n'
import {kebabCase} from 'lodash'
import LaravelTranslationFormatter from './laravel-translation-formatter'
import * as Sentry from "@sentry/browser";
import { Vue as VueIntegration } from "@sentry/integrations";
import { Integrations } from "@sentry/tracing";

require('./bootstrap')

window.Vue = Vue

var {BootstrapVue, IconsPlugin} = require('bootstrap-vue')
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(VueI18n)

const element = document.getElementById('laravel-data')
window.Laravel = JSON.parse(element.getAttribute('data-laravel'))
element.remove()
if (window.onEnvLoaded) window.onEnvLoaded()

Vue.prototype.$window = window

Vue.prototype.routeUri = function (name, parameters) {
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
Vue.prototype.routeMethod = function (name, parameters) {
  if (window.Laravel.routes[name] === undefined) {
    console.error('Route not found ', name)
  } else {
    return window.Laravel.routes[name].method
  }
}

require.context('./', true, /\.vue$/i, 'lazy').keys().forEach(file => {
  Vue.component(file.split('/').pop().split('.')[0], () => import(`${file}` /*webpackChunkName: "[request]" */))
})

/**
 * Fix autofocus on form elements inside the Vue.js area of the page by adding v-focus additionally to autofocus:
 * <input type="text" autofocus v-focus>
 */
Vue.directive('focus', {
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

Vue.filter('kebabCase', value => kebabCase(value))

const i18n = new VueI18n({
  locale: document.documentElement.lang,
  fallbackLocale: 'de',
  messages: languageBundle,
  formatter: new LaravelTranslationFormatter({ locale: document.documentElement.lang })
})

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
  el: '#app',
  i18n
})


if (process.env.MIX_SENTRY_VUE_DSN && process.env.MIX_SENTRY_VUE_DSN !== 'null') {
  Sentry.init({
    dsn: process.env.MIX_SENTRY_VUE_DSN,
    integrations: [
      new VueIntegration({
        Vue,
        tracing: true,
        logErrors: true,
      }),
      new Integrations.BrowserTracing(),
    ],
    tracesSampleRate: 1,
  })
}
