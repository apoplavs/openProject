<template>
  <div class="history">
    <div class="row">
      <div class="col-12 list-data-container">
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <span>Історія переглядів</span>
            <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="search">
          </div>
          <div>
            <spinner v-if="!loadData" />
            <judge-component v-if="loadData" :judgesList="filterJudges"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import JudgeComponent from '../../rating/judges/JudgeComponent.vue';
  import Spinner from '../../shared/Spinner.vue';
  import _ from 'lodash';
  
  export default {
    name: "ViewHistory",
    components: {
      JudgeComponent,
      Spinner
    },
    data() {
      return {
        loadData: false,
        search: '',
        judgesList: [],
      }
    },
    computed: {
      filterJudges() {
        //  живий пошук = фільтер
        return _.filter(this.judgesList, el => {
          let arr = _.filter(Object.keys(el), key => {
            let regEx = new RegExp(`(${this.search})`, "i");            
            return regEx.test(el.surname) || this.search.length == 0;
          });
          return arr.length > 0 ? true : false;
        });
      },
    },
    created() {
      if (!this.$store.getters.isAuth) {
          thi.$router.push('/login');
      } else {
          this.getJudgesList();
      } 
    },

    methods: {
      getJudgesList() {   
          axios
            .get('/api/v1/user/history', {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "Authorization": localStorage.getItem('token')
              },
            })
            .then(response => {
              this.judgesList = response.data;
              this.loadData = true;  
              console.log('user profile HISTORY', this.judgesList);
            })
            .catch(error => {
               if (error.response.status === 401) {
                  this.$router.push('/login');
              }
              console.log(error);
            });
        }
      },
     
      changeStatus: function(data){
        this.judgesList.data.forEach(element => {
          if (element.id === data.id){ 
            element.status = data.status.set_status;
            element.due_date_status = data.status.due_date;
          }          
        });
      } 
  };
</script>

<style lang="scss" scoped>
  @import "../../../../sass/judges_coutrs_list.scss";
  .history {
    margin-top: 3rem;
  }
  .min-width{
    display: flex;
    justify-content: center;
  }
  input[type='search']{
    width: 200px;
  }
  .card {
    margin-top: 0 !important;
  }
</style>