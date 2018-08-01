
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VueRouter from 'vue-router';
 
window.Vue.use(VueRouter);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

var Example = require('./components/ExampleComponent.vue')

Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue')
);

var passportClients = require('./components/passport/Clients.vue');

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue')
);

var passportAuthorizedClients = require('./components/passport/Clients.vue');

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue')
);


const routes = [
 // {
 //    path: '/',
 //    components: {
 //        examplecomponent: example-component
 //    }  
 // },
 //{path: '/passport-clients', component: passport-clients, name: 'passport-clients'},
 {path: '/', component: Example},
 {path: '/passport-clients', component: passportClients},
 {path: '/passport-authorized-clients', component: passportAuthorizedClients}
]
 
const router = new VueRouter({ routes })

const app = new Vue({
    el: '#app',
    router: router
});
