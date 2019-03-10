<template>
  <div class="judgesList" @keyup.enter="setFilters()">
    <div class="row">
      <div class="col-3 filters">
        <!-- filters -->
        <filters :filters="filters" @resetFilters="resetFilters" @setFilters="setFilters" />
      </div>
      <!-- Main list -->
      <div class="col-9 list-data-container">
        <div class="row">
          <div class="col-10 autocomplete">
            <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="filters.search" @keyup="liveSearch()">
            <div class="autocomplete-block-result" v-if="autocomplete.length">
              <div class="autocomplete-block-result_element" v-for="(el, ind_1) in autocomplete" :key="ind_1">
                <router-link :to="`/judges/${el.id}`">
                  {{ el.surname }} {{ (el.name.length === 1) ? el.name + '.' : el.name }} {{ (el.patronymic.length === 1) ? el.patronymic + '.' : el.patronymic }}
                </router-link>
              </div>
            </div>
          </div>
          <div class="col-2 pl-0">
            <button type="button" class="btn b-confirm w-100" @click="setFilters()"><i class="fa fa-search" aria-hidden="true"></i> знайти</button>
          </div>
        </div>
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <span>Список суддів</span>
            <div class="d-flex align-items-center">
              <span class="mr-2 sort"> сортувати за: </span>
              <select class="form-control select-sort" name="sorting" v-model="filters.sort" @change="sortList()">
                    <option value="1" selected>прізвищем (А->Я) <i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></option>
                    <option value="2">прізвищем (Я->А)</option>
                    <option value="3">рейтингом (низький->високий)</option>
                    <option value="4">рейтингом (високий->низький)</option>
                  </select>
            </div>
          </div>
          <div>
            <!--judges-judges-list-->
            <spinner v-if="!loadData" />
            <!-- <moon-loader :loading="!loadData" :color="color" :size="size"></moon-loader> -->
            <judge-component v-if="loadData" :judgesList="judgesList.data" @status="changeStatus" @addToCompare="addToCompare" />
          </div>
  
        </div>
        <div class="pagination mb-5 mt-3">
          <vue-ads-pagination ref="pagins" @page-change="pageChange" :total-items="judgesList.total" :max-visible-pages="5" :button-classes="buttonClasses" :loading="false" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import VueAdsPagination from 'vue-ads-pagination';
  import _ from 'lodash';
  
  import JudgeComponent from './JudgeComponent.vue';
  import Spinner from '../../shared/Spinner.vue';
  import Filters from '../../shared/Filters.vue';
  
  export default {
    name: "judges-list",
    components: {
      JudgeComponent,
      VueAdsPagination,
      Spinner,
      Filters
    },
    data() {
      return {
        loadData: false,
        filters: {
          page: 0,
          regions: [],
          jurisdictions: [],
          instances: [],
          search: null,
          sort: 1,
        },
        // judgeComparation: sessionStorage.judge_compare,
        autocomplete: [],
        judgesList: {
          total: 0
        },
        'buttonClasses': {
          'default': ['border-none', 'bg-grey-lightest'],
          'active': ['bg-active', 'border-none'],
          'dots': ['dots'],
          'disabled': ['disabled'],
        },
      }
    },
    created() {
      let initialFilters = JSON.parse(sessionStorage.getItem('judges-filters'));
      if (initialFilters) {
        this.filters = initialFilters;
      }
      this.getJudgesList();
    },
    methods: {
      // порівняння суддів
      addToCompare(judge_id) {
  
        let judge_compare = this.$store.getters.judge_compare;
        console.log('judge_compare', judge_compare);
        console.log('judge_id', judge_id);
  
        // якщо суддя вже був доданий раніше
        if (judge_compare.indexOf(judge_id) != -1) {
          this.$toasted.error("Цей суддя вже доданий для порівняння", {
            theme: "outline",
            position: "top-right",
            duration: 3000
          });
          return;
        }
  
        // якщо занадто багато додається для порівняння
        if (judge_compare.length + 1 > 5) {
          this.$toasted.error("Можна порівнювати одночасно до 5 суддів", {
            theme: "outline",
            position: "top-right",
            duration: 3000
          });
          return;
        }
  
        judge_compare.push(judge_id);
        this.$store.commit('updateJudgeToCompare', judge_compare);
  
        //this.judgeComparation = true;
        this.$toasted.success("Додано до порівняння", {
          theme: "outline",
          position: "top-right",
          duration: 3000
        });
      },
  
      validateInputSearch() {
        const regexp = new RegExp(/^[а-щА-ЩЬьЮюЯяЇїІіЄєҐґ']+$/iu);
        let str = _.trim(this.filters.search);
        if (str.search(regexp) === -1 || str === '') {
          if (str === '') {
            this.filters.search = null;
          }
          this.autocomplete = [];
          return false;
        }
        return true;
      },
      liveSearch: _.debounce(function(event) {
        if (this.validateInputSearch()) {
          axios
            .get('/api/v1/judges/autocomplete', {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
              },
              params: {
                search: this.filters.search
              }
            })
            .then(response => {
              this.autocomplete = response.data;
            })
            .catch(error => {
              console.log('Live search', error);
            });
        }
      }, 1000),
  
      getJudgesList() {
        this.autocomplete = []; // коли визиваємо цей метод liveSearch маємо закрити
        this.filters.expired = (this.filters.expired === true || this.filters.expired === 1) ? 1 : 0;
        if (this.validateInputSearch() === false) { // !! = true
          this.filters.search = null;
        }
        if (this.$store.getters.isAuth) {
          axios
            .get('/api/v1/judges/list', {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "Authorization": localStorage.getItem('token')
              },
              params: this.filters
            })
            .then(response => {
              this.judgesList = response.data;
              this.loadData = true;
              // console.log('getJudges Response', this.judgesList);
            })
            .catch(error => {
              if (error.response.status === 401) {
                this.$router.push('/login');
              }
              // console.log('Каже що не авторизований пффф та Канеха');
            });
        } else {
          // console.log('no token')
          axios
            .get("/api/v1/guest/judges/list", {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
              },
              params: this.filters
            })
            .then(response => {
              this.judgesList = response.data;
              this.loadData = true;
              console.log('getJudges Response', this.judgesList);
            })
            .catch(error => {
              console.log(error);
              // console.log('Ну нє не логінився я ще');
            });
        }
      },
  
      sortList: _.debounce(function(event) {
        this.loadData = false;
        window.scrollTo(0, 0);
        this.getJudgesList();
        sessionStorage.setItem('judges-filters', JSON.stringify(this.filters));
      }, 10),
  
      pageChange(page) {
        this.loadData = false;
        window.scrollTo(0, 0);
        this.filters.page = page + 1;
        this.getJudgesList();
      },
  
      setFilters() {
        window.scrollTo(0, 0);
        this.loadData = false;
        this.$refs.pagins.currentPage = 0;
        this.filters.page = 1;
        this.getJudgesList();
        sessionStorage.setItem('judges-filters', JSON.stringify(this.filters));
      },
  
      resetFilters() {
        this.autocomplete = [];
        this.loadData = false;
        this.getJudgesList(); // онуляємо всі фільтри і визиваємо функцію
        sessionStorage.removeItem('judges-filters');
      },
  
      changeStatus: function(data) {
        this.judgesList.data.forEach(element => {
          if (element.id === data.id) {
            element.status = data.status.set_status;
            element.due_date_status = data.status.due_date;
          }
        });
      }
    },
  };
</script>

<style lang="scss" scoped>
  @import "../../../../sass/judges_coutrs_list.scss";
  .fa-balance-scale {
    color: #ffa726;
    cursor: pointer;
    margin-left: 15%;
  }
</style>