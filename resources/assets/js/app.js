
import Vue from 'vue';
import VueRouter from 'vue-router';
// import Datepicker from 'vuejs-datepicker';
import axios from 'axios';
import VeeValidate, { Validator } from 'vee-validate';
import uk from 'vee-validate/dist/locale/uk';
import Toasted from 'vue-toasted';
import Pagination from 'laravel-vue-pagination';


Vue.use(VeeValidate);
Vue.use(VueRouter);
Vue.use(Toasted);
Vue.use(Pagination);

Validator.localize('uk', uk);
window.axios = axios;


import AppComponent from './components/AppComponent.vue';
import HomeComponent from './components/main/HomeComponent.vue';
// import HeaderComponent from './components/main/HeaderComponent.vue';
import Login from './components/auth/Login.vue';
import Registration from './components/auth/Registration.vue';

import JudgesList from './components/rating/JudgesList.vue';
import UserProfile from './components/user/UserProfile.vue';

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
            path: '/user-profile',
            component: UserProfile,
            name: 'user-profile'
        },
        {
            path: '/judges-list',
            component: JudgesList,
            name: 'judges-list'
        },
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
