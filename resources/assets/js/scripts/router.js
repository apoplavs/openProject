import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

import HomeComponent from '../components/main/HomeComponent.vue';
import Login from '../components/auth/Login.vue';
import Registration from '../components/auth/Registration.vue';
import RecoverPassword from '../components/auth/RecoverPassword.vue';
import JudgesList from '../components/rating/judges/JudgesList.vue';
import JudgeProfile from '../components/rating/judges/JudgeProfile.vue';
import JudgeComparison from '../components/rating/judges/JudgeComparison.vue';

import CourtsList from '../components/rating/courts/CourtsList.vue';
import CourtProfile from '../components/rating/courts/CourtProfile.vue';
import UserProfile from '../components/user/UserProfile.vue';
import UserSettings from '../components/user/UserSettings.vue';
import ConfirmEmail from '../components/user/ConfirmEmail.vue';

import PrivacyPolicy from '../components/system/PrivacyPolicy.vue';
import UserAgreement from '../components/system/UserAgreement.vue';

// import CourtSessions from '../components/user/components/CourtSessions.vue';
// import CourtPractice from '../components/user/components/CourtPractice.vue';
// import CourtsBookmarks from '../components/user/components/CourtsBookmarks.vue';
// import ViewHistory from '../components/user/components/ViewHistory.vue';
// import JudgesBookmarks from '../components/user/components/JudgesBookmarks.vue';

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
            path: '/recover-password',
            component: RecoverPassword,
            name: 'recover-password'
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
            name: 'courts-list'
        },
        {
            path: '/courts/:id',
            component: CourtProfile,
            name: 'court-profile'
        },
        {
            path: '/user-profile',
            component: UserProfile,
            name: 'user-profile',
        },
        {
            path: '/settings',
            component: UserSettings,
            name: 'user-settings',
        },
        {
            path: '/privacy-policy',
            component: PrivacyPolicy,
            name: 'privacy-policy',
        },
        {
            path: '/user-agreement',
            component: UserAgreement,
            name: 'user-agreement',
        },
		{
			path: '/confirm-email',
			component: ConfirmEmail,
			name: 'confirm-email',
		},
		{
			path: '/judge-comparison',
			component: JudgeComparison,
			name: 'judge-comparison',
		},
    ]
});

