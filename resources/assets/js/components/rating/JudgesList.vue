<template>
  <div class="container mt-5">
    <div class="row min-width">
      <div class="col-3 filters">
        <div class="card">
          <h4 class="card-header"><i class="fa fa-filter" aria-hidden="true"></i> Фільтри</h4>
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <h6>Інстанція</h6>
                <ul class="list-unstyled mb-0">
                  <li><label><input type="checkbox" value="3" name="instance" v-model="params.instance"><span class="checkmark"></span> Перша</label></li>
                  <li><label><input type="checkbox" value="2" name="instance" v-model="params.instance"><span class="checkmark"></span> Апеляційна</label></li>
                  <li><label><input type="checkbox" value="1" name="instance" v-model="params.instance"><span class="checkmark"></span> Касаційна</label></li>
                </ul>
              </div>
            </div>
  
            <hr>
  
            <div class="row">
              <div class="col-lg-12">
                <h6>Юрисдикція</h6>
                <ul class="list-unstyled mb-0">
                  <li><label><input type="checkbox" value="3" name="jurisdiction" v-model="params.jurisdiction"><span class="checkmark"></span> Господарська</label></li>
                  <li><label><input type="checkbox" value="2" name="jurisdiction" v-model="params.jurisdiction"><span class="checkmark"></span> Адміністративна</label></li>
                  <li><label><input type="checkbox" value="1" name="jurisdiction" v-model="params.jurisdiction"><span class="checkmark"></span> Загальна</label></li>
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
                  <li><label><input type="checkbox" value="1" name="expired" v-model="params.expired"><span class="checkmark"></span>Закінчилися повноваження</label></li>
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
                <router-link to="/">
                  {{ el.surname }} {{ (el.name.length === 1) ? el.name + '.' : el.name }} {{ (el.patronymic.length === 1) ? el.patronymic + '.' : el.patronymic }}
                </router-link>
              </div>
            </div>
          </div>
          <div class="col-2 pl-0">
            <button type="button" class="btn btn-confirm w-100" @click="setFilters()"><i class="fa fa-search" aria-hidden="true"></i> знайти</button>
          </div>
        </div>
        <div class="card card-outline-secondary ">
          <div class="card-header d-flex justify-content-between">
            <h4 class="d-flex align-items-center">Список суддів</h4>
            <div class="d-flex align-items-center">
              <span class="mr-2"> сортувати за: </span>
              <select class="form-control select-sort" name="sorting" v-model="params.sort" @change="sortJudges()">
                                  <option value="1" selected>прізвищем (А->Я) <i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></option>
                                  <option value="2">прізвищем (Я->А)</option>
                                  <option value="3">рейтингом (низький->високий)</option>
                                  <option value="4">рейтингом (високий->низький)</option>
                                </select>
            </div>
          </div>
          <div id="judges-list">
            <!--judges-judges-list-->
            <judge-component :judgesList="judgesList.data"></judge-component>
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
  //
  import VueAdsPagination from 'vue-ads-pagination';
  import _ from 'lodash';
  
  export default {
    name: "judges-list",
    data() {
      return {
        params: {
          page: 0,
          regions: [],
          jurisdiction: [],
          instance: [],
          search: null,
          sort: 1,
          expired: 0
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
        console.log('Виклик автокомплит');
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
      sortJudges: _.debounce(function(event) {
        this.getJudgesList();
      }, 10),
  
      pageChange(page) {
        this.params.page = page + 1;
        this.getJudgesList();
      },
      setFilters() {
        this.$refs.pagins.currentPage = 0;
        this.params.page = 1; 
        this.getJudgesList();
      },
  
      getJudgesList() {
        console.log('getJudgesList()', this.params);
        this.autocomplete = []; // коли визиваємо цей метод liveSearch маємо закрити
        if (this.validateInputSearch() === false) { // !! = true
          this.params.search = null;
        }
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
              window.scrollTo(0, 0);
              console.log('getJudges Response', this.judgesList);
            })
            .catch(error => {
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
            })
            .catch(error => {
              console.log(error);
              console.log('Ну нє не логінився я ще');
            });
        }
      },
      resetFilters() {
        this.params.regions = [];
        this.params.instance = [];
        this.params.jurisdiction = [];
        this.params.expired = 0;
        this.params.search = null;
        this.autocomplete = [];
        this.getJudgesList(); // онуляємо всі фільтри і визиваємо функцію
      }
    },
    components: {
      JudgeComponent,
      VueAdsPagination
    }
  
  };
</script>

<style lang="scss">
  .pagination {
    .bg-active {
      background-color: #2b989b;
      border-color: #2b989b;
    }
    button{
      &:active,
      &:focus{
        background-color: #2b989b;
        border-color: #2b989b;
      }
    }
    div.pr-2.leading-loose {
      display: none !important;
    }
    .disabled {
      color: grey;
    }
    .dots {
      background-color: transparent;
      border: none !important;
    }
  }
</style>

<style lang="scss" scoped>
  @import "../../../sass/judges_coutrs_list.scss";
</style>