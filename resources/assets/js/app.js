
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
import JudgesList from './components/main/JudgesList.vue';


import PassportClients from './components/passport/PassportClients.vue';
import PassportAuthorizedClients from './components/passport/PassportAuthorizedClients.vue';

Vue.component('app-component', require('./components/AppComponent.vue'));
Vue.component('header-component', require('./components/HeaderComponent.vue'));
Vue.component('home-component', require('./components/HomeComponent.vue'));

// auth
Vue.component('login', require('./components/auth/Login.vue'));
Vue.component('registration', require('./components/auth/Registration.vue'));

// main
Vue.component('judges-list', require('./components/main/JudgesList.vue'));

Vue.component('passport-clients', require('./components/passport/PassportClients.vue'));
Vue.component('passport-authorized-clients', require('./components/passport/PassportAuthorizedClients.vue'));

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
            path: '/judges-list',
            component: JudgesList,
            name: 'judges-list'
        },

        {
            path: '/passport-clients',
            component: PassportClients,
            name: 'passport-clients'
        },
        {
            path: '/passport-authorized-clients',
            component: PassportAuthorizedClients,
            name: 'passport-authorized-clients'
        }
    ]
});


// const date_picker = new Vue({
//     el: '#date_picker',
//     components: {
//         Datepicker
//     },
//     data: {
//         date: new Date()
//     }
// });
//
// const validate = new VeeValidate({
//     // import VeeValidate from 'vee-validate';
//     // this.$validator.localize('fr', french)
//
//
// })

const app = new Vue({
    el: '#app',
    render: h => h(AppComponent),
    router
        
});
