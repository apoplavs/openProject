import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    token: localStorage.getItem('token') || '',
    user: JSON.parse(localStorage.getItem('user')) || {},
  },
  getters : {
    isAuth: state => state.token !== '' ? true : false,
    getUser: state => state.user,
  },
  mutations: {
    auth_success(state){      
      state.token = localStorage.getItem('token');
      state.user =  JSON.parse(localStorage.getItem('user'));
    },
    logout(state){
      state.token = '';
      localStorage.clear();   
    },
  },
  actions: {},
})