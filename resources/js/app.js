require('./bootstrap');

window.Vue = require('vue');

var datePicker = require('vue-bootstrap-datetimepicker');
jQuery.extend(true, jQuery.fn.datetimepicker.defaults, {
    icons: {
        time: 'fas fa-clock',
        date: 'fas fa-calendar',
        up: 'fas fa-arrow-up',
        down: 'fas fa-arrow-down',
        previous: 'fas fa-chevron-left',
        next: 'fas fa-chevron-right',
        today: 'fas fa-calendar-check',
        clear: 'fas fa-trash-alt',
        close: 'fas fa-times-circle'
    }
});
Vue.component('date-picker', datePicker);

require.context('./', true, /\.vue$/i, 'lazy').keys().forEach(file => {
    Vue.component(file.split('/').pop().split('.')[0], () => import(`${file}`));
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

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});
