<template>
  <div class="container mt-5">
    <div class="row min-width">
      <div class="col-3 filters">
        <div class="card">
          <div class="card-header">
            <div>
               <i class="fa fa-filter" aria-hidden="true"></i> 
              <span>Фільтри</span>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <h6>Інстанція</h6>
                <ul class="list-unstyled mb-0">
                  <li><label><input type="checkbox" value="3" name="instances" v-model="params.instances"><span class="checkmark"></span> Перша</label></li>
                  <li><label><input type="checkbox" value="2" name="instances" v-model="params.instances"><span class="checkmark"></span> Апеляційна</label></li>
                  <li><label><input type="checkbox" value="1" name="instances" v-model="params.instances"><span class="checkmark"></span> Касаційна</label></li>
                </ul>
              </div>
            </div>
  
            <hr>
  
            <div class="row">
              <div class="col-lg-12">
                <h6>Юрисдикція</h6>
                <ul class="list-unstyled mb-0">
                  <li><label><input type="checkbox" value="3" name="jurisdictions" v-model="params.jurisdictions"><span class="checkmark"></span> Господарська</label></li>
                  <li><label><input type="checkbox" value="2" name="jurisdictions" v-model="params.jurisdictions"><span class="checkmark"></span> Адміністративна</label></li>
                  <li><label><input type="checkbox" value="1" name="jurisdictions" v-model="params.jurisdictions"><span class="checkmark"></span> Загальна</label></li>
                </ul>
              </div>
            </div>
  
            <hr>
  
            <div class="row">
              <div class="col-lg-12">
                <h6>Регіон суду</h6>
                <ul class="list-unstyled mb-0">
                  <li><label><input type="checkbox" value="2" name="region" v-model="params.regions"><span class="checkmark"></span> Вінницька</label></li>
                  <li><label><input type="checkbox" value="3" name="region" v-model="params.regions"><span class="checkmark"></span> Волинська</label></li>
                  <li><label><input type="checkbox" value="4" name="region" v-model="params.regions"><span class="checkmark"></span> Дніпропетровська</label></li>
                  <li><label><input type="checkbox" value="5" name="region" v-model="params.regions"><span class="checkmark"></span> Донецька</label></li>
                  <li><label><input type="checkbox" value="6" name="region" v-model="params.regions"><span class="checkmark"></span> Житомирська</label></li>
                  <li><label><input type="checkbox" value="7" name="region" v-model="params.regions"><span class="checkmark"></span> Закарпатська</label></li>
                  <li><label><input type="checkbox" value="8" name="region" v-model="params.regions"><span class="checkmark"></span> Запорізька</label></li>
                  <li><label><input type="checkbox" value="9" name="region" v-model="params.regions"><span class="checkmark"></span> Івано-Франківська</label></li>
                  <li><label><input type="checkbox" value="10" name="region" v-model="params.regions"><span class="checkmark"></span> Київська</label></li>
                  <li><label><input type="checkbox" value="11" name="region" v-model="params.regions"><span class="checkmark"></span> Кіровоградська</label></li>
                  <li><label><input type="checkbox" value="12" name="region" v-model="params.regions"><span class="checkmark"></span> Луганська</label></li>
                  <li><label><input type="checkbox" value="13" name="region" v-model="params.regions"><span class="checkmark"></span> Львівська</label></li>
                  <li><label><input type="checkbox" value="14" name="region" v-model="params.regions"><span class="checkmark"></span> Миколаївська</label></li>
                  <li><label><input type="checkbox" value="15" name="region" v-model="params.regions"><span class="checkmark"></span> Одеська</label></li>
                  <li><label><input type="checkbox" value="16" name="region" v-model="params.regions"><span class="checkmark"></span> Полтавська</label></li>
                  <li><label><input type="checkbox" value="17" name="region" v-model="params.regions"><span class="checkmark"></span> Рівненська</label></li>
                  <li><label><input type="checkbox" value="18" name="region" v-model="params.regions"><span class="checkmark"></span> Сумська</label></li>
                  <li><label><input type="checkbox" value="19" name="region" v-model="params.regions"><span class="checkmark"></span> Тернопільська</label></li>
                  <li><label><input type="checkbox" value="20" name="region" v-model="params.regions"><span class="checkmark"></span> Харківська</label></li>
                  <li><label><input type="checkbox" value="21" name="region" v-model="params.regions"><span class="checkmark"></span> Херсонська</label></li>
                  <li><label><input type="checkbox" value="22" name="region" v-model="params.regions"><span class="checkmark"></span> Хмельницька</label></li>
                  <li><label><input type="checkbox" value="23" name="region" v-model="params.regions"><span class="checkmark"></span> Черкаська</label></li>
                  <li><label><input type="checkbox" value="24" name="region" v-model="params.regions"><span class="checkmark"></span> Чернівецька</label></li>
                  <li><label><input type="checkbox" value="25" name="region" v-model="params.regions"><span class="checkmark"></span> Чернігівська</label></li>
                  <li><label><input type="checkbox" value="26" name="region" v-model="params.regions"><span class="checkmark"></span> м. Київ</label></li>
                </ul>
              </div>
            </div>
  
            <hr>
  
            <div class="row">
              <div class="col-lg-12">
                <ul class="list-unstyled mb-0">
                  <li><label><input type="checkbox" value="1" v-model="params.expired"><span class="checkmark"></span>Закінчилися повноваження</label></li>
                </ul>
              </div>
            </div>
  
            <div class="apply-filters" id="apply-filters">
              <hr>
              <div class="row">
                <div class="col-6">
                  <button type="reset" @click="resetFilters()" class="btn btn-outline-info">Скинути</button>
                </div>
                <div class="col-6">
                  <button type="button" @click="setFilters()" class="btn btn-primary">Показати</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Main list -->
      <div class="col-9 list-data-container">
        <div class="row">
          <div class="col-10 autocomplete">
            <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="params.search" @keyup="liveSearch()">
            <div class="autocomplete-block-result" v-if="autocomplete.length">
              <div class="autocomplete-block-result_element" v-for="(el, ind_1) in autocomplete" :key="ind_1">
                <router-link :to="`/judge-profile/${el.id}`">
                  {{ el.surname }} {{ (el.name.length === 1) ? el.name + '.' : el.name }} {{ (el.patronymic.length === 1) ? el.patronymic + '.' : el.patronymic }}
                </router-link>
              </div>
            </div>
          </div>
          <div class="col-2 pl-0">
            <button type="button" class="btn btn-confirm w-100" @click="setFilters()"><i class="fa fa-search" aria-hidden="true"></i> знайти</button>
          </div>
        </div>
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <span>Список суддів</span>
            <div class="d-flex align-items-center">
              <span class="mr-2"> сортувати за: </span>
              <select class="form-control select-sort" name="sorting" v-model="params.sort" @change="sortList()">
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
            <judge-component v-if="loadData" :judgesList="judgesList.data" />
          </div>
  
        </div>
        <div class="pagination mb-5">
          <vue-ads-pagination  ref="pagins" @page-change="pageChange" :total-items="judgesList.total" :max-visible-pages="5" :button-classes="buttonClasses" :loading="false">
          </vue-ads-pagination>
        </div>
      </div>
    </div>
    <!-- </div> -->
    <!-- contaner -->
  
  </div>
</template>

<script>
  import JudgeComponent from './JudgeComponent.vue';
  import Spinner from '../../shared/Spinner.vue';
  import VueAdsPagination from 'vue-ads-pagination';
  import _ from 'lodash';
  
  export default {
    name: "judges-list",
    data() {
      return {
        loadData: false,
        params: {
          page: 0,
          regions: [],
          jurisdictions: [],
          instances: [],
          search: null,
          sort: 1,
          expired: 1
        },
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
    methods: {
      validateInputSearch() {
        const regexp = new RegExp(/^[а-щА-ЩЬьЮюЯяЇїІіЄєҐґ']+$/iu);
        let str = _.trim(this.params.search);
        if (str.search(regexp) === -1 || str === '') {
          if (str === '') {
            this.params.search = null;
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
                search: this.params.search
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
      sortList: _.debounce(function(event) {
        this.loadData = false;
        window.scrollTo(0, 0);
        this.getJudgesList();
      }, 10),
  
      pageChange(page) {
        this.loadData = false;
        window.scrollTo(0, 0);
        this.params.page = page + 1;
        this.getJudgesList();
      },
      setFilters() {
        this.loadData = false;
        window.scrollTo(0, 0);
        this.$refs.pagins.currentPage = 0;
        this.params.page = 1; 
        console.log('PARAMS ЗНАЙТИ', this.params);
        this.getJudgesList();
      },
  
      getJudgesList() {    
        this.autocomplete = []; // коли визиваємо цей метод liveSearch маємо закрити
        this.params.expired = (this.params.expired === true || this.params.expired === 1) ? 1 : 0; 
        if (this.validateInputSearch() === false) { // !! = true
          this.params.search = null;
        }
         console.log('getJudgesList-------PARAMS', this.params);
        if (localStorage.getItem('token')) {
          console.log('have token')
          axios
            .get('/api/v1/judges/list', {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "Authorization": localStorage.getItem('token')
              },
              params: this.params
            })
            .then(response => {
              this.judgesList = response.data;
              this.loadData = true;  
              console.log('getJudges Response', this.judgesList);
            })
            .catch(error => {
               if (error.response.status === 401) {
                  this.$router.push('/login');
              }
              console.log('Каже що не авторизований пффф та Канеха');
            });
        } else {
          console.log('no token')
          axios
            .get("/api/v1/guest/judges/list", {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
              },
              params: this.params
            })
            .then(response => {
              this.judgesList = response.data;
              this.loadData = true;
              console.log('getJudges Response', this.judgesList);
            })
            .catch(error => {
              console.log(error);
              console.log('Ну нє не логінився я ще');
            });
        }
      },
      resetFilters() {
        this.params.regions = [];
        this.params.instances = [];
        this.params.jurisdictions = [];
        this.params.expired = 1;
        this.params.search = null;
        this.autocomplete = [];
        this.loadData = false;
        this.loadData = false;
        window.scrollTo(0, 0);
        this.getJudgesList(); // онуляємо всі фільтри і визиваємо функцію
      }
    },
    components: {
      JudgeComponent,
      VueAdsPagination,
      Spinner
    }
  };
</script>

<style lang="scss">
  @import "../../../../sass/_variables.scss";
  .pagination {
    .bg-active {
      background-color: $main-color;
      border-color: $main-color;
    }
    button{
      &:active,
      &:focus{
        background-color: $main-color;
        border-color: $main-color;
      }
    }
    div.pr-2.leading-loose {
      display: none !important;
    }
    .disabled {
      color: $input-placeholder-color;
      cursor: no-drop;
    }
    .dots {
      background-color: transparent;
      border: none !important;
    }
  }
</style>

<style lang="scss" scoped>
  @import "../../../../sass/judges_coutrs_list.scss";
</style>