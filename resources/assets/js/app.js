
import Vue from 'vue';
import VueRouter from 'vue-router';
import axios from 'axios';
import VeeValidate, { Validator } from 'vee-validate';
import uk from 'vee-validate/dist/locale/uk';
import Toasted from 'vue-toasted';
import Vuex from 'vuex';

import AppComponent from './components/AppComponent.vue';
import router from './scripts/router';
import GSignInButton from 'vue-google-signin-button';
import store from './store/store';
import VueMask from 'v-mask';
Vue.use(VueMask);

Vue.use(GSignInButton);
Vue.use(VeeValidate);
Vue.use(VueRouter);
Vue.use(Toasted);
Vue.use(Vuex);

Validator.localize('uk', uk);
window.axios = axios;

new Vue({
    el: '#app',
    render: h => h(AppComponent),
    router,
    store   
});
