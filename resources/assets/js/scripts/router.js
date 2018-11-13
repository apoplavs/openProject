import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

import HomeComponent from '../components/main/HomeComponent.vue';
import Login from '../components/auth/Login.vue';
import Registration from '../components/auth/Registration.vue';
import JudgesList from '../components/rating/judges/JudgesList.vue';
import JudgeProfile from '../components/rating/judges/JudgeProfile.vue';

import CourtsList from '../components/rating/courts/CourtsList.vue';
import CourtProfile from '../components/rating/courts/CourtProfile.vue';
import UserProfile from '../components/user/UserProfile.vue';

export default new Router({
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
            path: '/judges',
            component: JudgesList,
            name: 'judges-list'
        },
        {
            path: '/judges/:id',
            component: JudgeProfile,
            name: 'judge-profile'
        },
        {
            path: '/courts',
            component: CourtsList,
            name: 'courts-list',
        },
        {
            path: '/courts/:id',
            component: CourtProfile,
            name: 'court-profile'
        },
    ]
});

