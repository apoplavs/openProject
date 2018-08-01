
import Vue from 'vue';
import VueRouter from 'vue-router';
// import Datepicker from 'vuejs-datepicker';
import axios from 'axios';


window.axios = axios;
require('./bootstrap');

Vue.use(VueRouter);

import AppComponent from './components/AppComponent.vue';

import HomeComponent from './components/HomeComponent.vue';
// import HeaderComponent from './components/HeaderComponent.vue';




import PassportClients from './components/passport/PassportClients.vue';
import PassportAuthorizedClients from './components/passport/PassportAuthorizedClients.vue';

Vue.component('app-component', require('./components/AppComponent.vue'));
Vue.component('header-component', require('./components/HeaderComponent.vue'));
Vue.component('home-component', require('./components/HomeComponent.vue'));




const router = new VueRouter({
    mode: 'history',
    // base: __dirname,
    routes: [
        {
            path: '/',
            component: AppComponent,
            name: ''
        },
        {
            path: '/home',
            component: HomeComponent,
            name: ''
        },
        {
            path: '/passport-clients',
            component: PassportClients,
            name: ''
        },
        {
            path: '/passport-authorized-clients',
            component: PassportAuthorizedClients,
            name: ''
        }
    ]
})


// const date_picker = new Vue({
//     el: '#date_picker',
//     components: {
//         Datepicker
//     },
//     data: {
//         date: new Date()
//     }
// });

const app = new Vue({
    el: '#app',
    render: h => h(AppComponent),
    router
});
