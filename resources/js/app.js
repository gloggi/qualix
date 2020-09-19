import languageBundle from '@kirschbaum-development/laravel-translations-loader?parameters={$1}!@kirschbaum-development/laravel-translations-loader'
import VueI18n from 'vue-i18n'
import { kebabCase } from 'lodash'

require('./bootstrap');

window.Vue = require('vue');

var { BootstrapVue, IconsPlugin } = require('bootstrap-vue');
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueI18n);

Vue.prototype.$window = window;

require.context('./', true, /\.vue$/i, 'lazy').keys().forEach(file => {
    Vue.component(file.split('/').pop().split('.')[0], () => import(`${file}` /*webpackChunkName: "[request]" */));
});

/**
 * Fix autofocus on form elements inside the Vue.js area of the page by adding v-focus additionally to autofocus:
 * <input type="text" autofocus v-focus>
 */
Vue.directive('focus', {
    inserted: function (el) {
        if (el.value !== undefined) {
            el.focus();
            let caretPos = el.value.length;
            if (el.createTextRange) {
                // <textarea>
                var range = el.createTextRange();
                range.move('character', caretPos);
                range.select();
            } else if (['text', 'search', 'url', 'tel', 'password'].includes(el.type)) {
                // <input type="...">
                // setSelectionRange is only supported for the mentioned input types.
                // It doesn't work for email and number inputs:
                // https://developer.mozilla.org/en-US/docs/Web/API/HTMLInputElement/setSelectionRange
                el.setSelectionRange(caretPos, caretPos);
            }
        } else {
            // We might be in a vue-multiselect, search for the contained div.multiselect
            let multiselect = el.querySelector('div.multiselect');
            if (multiselect) {
                multiselect.focus();
            }
        }
    }
});

Vue.filter('kebabCase', value => kebabCase(value));
Vue.filter('append', (value, suffix) => value + suffix);

const i18n = new VueI18n({
    locale: document.documentElement.lang,
    messages: languageBundle
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    i18n
});
