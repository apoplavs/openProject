
import Vue from 'vue';
import VueRouter from 'vue-router';
require('./bootstrap');

Vue.use(VueRouter);

import HomeComponent from './components/HomeComponent.vue';
import AppComponent from './components/AppComponent.vue';


import PassportClients from './components/passport/PassportClients.vue';
import PassportAuthorizedClients from './components/passport/PassportAuthorizedClients.vue';

Vue.component('app-component', require('./components/AppComponent.vue'));

const routes = [
    {path: '/', component: AppComponent},
    {path: '/passport-clients', component: PassportClients},
    {path: '/passport-authorized-clients', component: PassportAuthorizedClients}
]

const router = new VueRouter({
    mode: 'history',
    // base: __dirname,
    routes
})

const app = new Vue({
    el: '#app',
    router
});
