/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import VuePapaParse from 'vue-papa-parse';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
window.Vue = require('vue').default;
export const bus = new Vue();
window.Vue.use(VuePapaParse);
// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
    // Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
    /**
     * The following block of code may be used to automatically register your
     * Vue components. It will recursively scan this directory for the Vue
     * components and automatically register them with their "basename".
     *
     * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
     */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('BottomNavigation', require('./components/BottomNavigation.vue').default);
Vue.component('Inputs', require('./components/Inputs.vue').default);
Vue.component('Summary', require('./components/Summary.vue').default);
Vue.component('Modal', require('./components/Modal.vue').default);
Vue.component('Map', require('./components/Map.vue').default);
Vue.component('solarsize', require('./components/App.vue').default);




//import App from "./App"

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});