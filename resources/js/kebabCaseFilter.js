import Vue from 'vue'
import kebabCase from 'lodash/kebabCase'

Vue.filter('kebabCase', value => kebabCase(value))
