
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

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

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Fix autofocus on form elements inside the Vue.js area of the page by adding v-focus additionally to autofocus:
 * <input type="text" autofocus v-focus>
 */
Vue.directive('focus', {
    inserted: function (el) {
        if (el.value !== undefined) {
            el.focus()
            let caretPos = el.value.length;
            if (el.createTextRange) {
                console.log('createTextRange available')
                var range = el.createTextRange();
                range.move('character', caretPos);
                range.select();
            } else {
                el.setSelectionRange(caretPos, caretPos);
            }
        } else {
            console.log(el)
            console.log(el.querySelector('.multiselect'))
            // We might be in a vue-multiselect, search for the contained div.multiselect
            let multiselect = el.querySelector('div.multiselect');
            if (multiselect) {
                console.log(2)
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
