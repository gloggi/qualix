import Vue from 'vue'
import {kebabCase} from 'lodash'

Vue.filter('kebabCase', value => kebabCase(value))
