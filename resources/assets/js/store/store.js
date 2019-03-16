import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    token: localStorage.getItem('token') || '',
    user: JSON.parse(localStorage.getItem('user')) || {},
    judge_compare: JSON.parse(sessionStorage.getItem('judge_compare')) || [],
  },
  getters : {
    isAuth: state => state.token !== '' ? true : false,
    getUser: state => state.user,
    judge_compare: state => state.judge_compare,
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
    updateJudgeToCompare(state, judge_compare) {
      sessionStorage.setItem('judge_compare', JSON.stringify(judge_compare));
      state.judge_compare = judge_compare; 
    },
  },
  actions: {},
})