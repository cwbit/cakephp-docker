import Vue from 'vue';
import axios from 'axios';
import AppGreet from './components/AppGreet.vue';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Vue = Vue;

Vue.component('app-greet', AppGreet);

const app = new Vue({
  el: '#app'
});
