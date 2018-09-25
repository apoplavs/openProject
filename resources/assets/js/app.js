
import Vue from 'vue';
import VueRouter from 'vue-router';
// import Datepicker from 'vuejs-datepicker';
import axios from 'axios';
import VeeValidate, { Validator } from 'vee-validate';
import uk from 'vee-validate/dist/locale/uk';
import Toasted from 'vue-toasted';


Vue.use(VeeValidate);
Vue.use(VueRouter);
Vue.use(Toasted);

Validator.localize('uk', uk);


window.axios = axios;
// require('./bootstrap');


import AppComponent from './components/AppComponent.vue';

import HomeComponent from './components/HomeComponent.vue';
// import HeaderComponent from './components/HeaderComponent.vue';
import Login from './components/auth/Login.vue';
import Registration from './components/auth/Registration.vue';
import JudgesList from './components/views/JudgesList.vue';
import PersonalCabinet from './components/views/PersonalCabinet.vue';



Vue.component('app-component', require('./components/AppComponent.vue'));
Vue.component('header-component', require('./components/HeaderComponent.vue'));
Vue.component('home-component', require('./components/HomeComponent.vue'));

// auth
Vue.component('login', require('./components/auth/Login.vue'));
Vue.component('registration', require('./components/auth/Registration.vue'));

// views
Vue.component('judges-list', require('./components/views/JudgesList.vue'));
Vue.component('personal-cabinet', require('./components/views/PersonalCabinet.vue'));

Vue.component('pagination', require('laravel-vue-pagination'));





const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        {
            path: '/',
            component: HomeComponent,
            name: 'home-component'
        },
        {
            path: '/login',
            component: Login,
            name: 'login'
        },
        {
            path: '/registration',
            component: Registration,
            name: 'registration'
        },
        {
            path: '/personal-cabinet',
            component: PersonalCabinet,
            name: 'personal-cabinet'
        },

        {
            path: '/judges-list',
            component: JudgesList,
            name: 'judges-list'
        }
    ]
});



const app = new Vue({
    el: '#app',
    render: h => h(AppComponent),
    router
        
});
