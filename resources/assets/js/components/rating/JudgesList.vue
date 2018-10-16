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
                <div class="col-6 pl-1">
                  <button type="reset" @click="resetFilters()" class="btn btn-outline-info">Скинути</button>
                </div>
                <div class="col-6">
                  <button type="button" @click="getJudgesList()" class="btn btn-primary">Показати</button>
                </div>
              </div>
            </div>
            <!-- apply-filters -->
            <!-- </form> -->
          </div>
        </div>
        <!-- Card -->
        <!-- <div>Регіон-------- {{params.regions }}</div>
              <div>інстанція--------- {{ params.instance }}</div>
              <div>юрисдикція--------- {{ params.jurisdiction }}</div>
              <div>пошук-------- {{ params.search}}</div>
              <div>sort-------- {{ params.sort}}</div>
              <div> expired-------- {{ params.expired}}</div> -->
      </div>
  
      <!-- Main list -->
      <div class="col-9 list-data-container">
        <div class="row">
          <div class="col-10 autocomplete">
            <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="params.search" @keyup="liveSearch()">
            <div class="autocomplete-block-result" v-if="autocomplete.length">
              <div v-for="(el, ind_1) in autocomplete" :key="ind_1">
                <router-link to="/">
                  {{ el.surname }} {{ (el.name.length === 1) ? el.name + '.' : el.name }} {{ (el.patronymic.length === 1) ? el.patronymic + '.' : el.patronymic }}
                </router-link>
                <hr>
              </div>
            </div>
          </div>
          <div class="col-2 pl-0">
            <button type="button" class="btn btn-block btn btn-primary" @click="getJudgesList()"><i class="fa fa-search" aria-hidden="true"></i> знайти</button>
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
          <vue-ads-pagination @page-change="pageChange" :total-items="judgesList.total" :max-visible-pages="5" :button-classes="buttonClasses" :loading="false">
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
          page: 1,
          regions: [],
          jurisdiction: [],
          instance: [],
          search: null,
          sort: 1,
          expired: 0
        },
        autocomplete: [],
        // message: "",
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
  
      getJudgesList() {
        console.log('getJudgesList()');
        this.autocomplete = []; // коли визиваємо цей метод liveSearch маємо закрити
        if (this.validateInputSearch() === false) { // !! = true
          this.params.search = null;
        }
        if (localStorage.getItem('token')) {
          console.log('have token')
          //this.params.page = page + 1;
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

<style>
  @import "../../../sass/_variables.scss";
  
  /* styles for pagination must be not scoped */
  
  button {
    cursor: pointer;
  }
  
  .bg-active {
    background-color: #2b989b;
  }
  
  .button:active {
    outline: none;
  }
  
  .button:focus {
    outline: none;
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
</style>


<style scoped lang="scss">
  @import "../../../sass/_variables.scss";
  .min-width {
    min-width: 1200px !important;
  }
  
  
  /* Стилі для фільтрів */
  
  // styles filters 
  .filters {
    margin-top: 58px;
    font-size: 0.9em;
    h6 {
      color: #4c88bd;
    }
    /* Customize the label (the container) */
    ul label {
      display: block;
      position: relative;
      padding-left: 35px;
      margin-bottom: 10px;
      cursor: pointer;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }
    /* Hide the browser's default checkbox */
    ul input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }
    /* Create a custom checkbox */
    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      height: 21px;
      width: 21px;
      background-color: #eee;
    }
    /* When the checkbox is checked, add a blue background */
    ul label input:checked~.checkmark {
      background-color: #2b989b;
    }
    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }
    /* Show the checkmark when checked */
    ul label input:checked~.checkmark:after {
      display: block;
    }
    /* Style the checkmark/indicator */
    ul label .checkmark:after {
      left: 9px;
      top: 5px;
      width: 5px;
      height: 10px;
      border: solid white;
      border-width: 0 3px 3px 0;
      -webkit-transform: rotate(45deg);
      -ms-transform: rotate(45deg);
      transform: rotate(45deg);
    }
    .apply-filters {
      background-color: white;
      position: sticky;
      bottom: 0;
    }
    .fa-filter {
      font-size: 21px;
      color: #6291ba;
    }
  }
  
  .list-data-container {
    .card {
      margin-top: 20px;
    }
    .pagination {
      display: flex;
      justify-content: center;
    }
    .select-sort {
      width: 290px;
    }
    /* styles for autocomplete field  */
    .autocomplete {
      /*the container must be positioned relative:*/
      position: relative;
      display: inline-block;
    }
    .autocomplete-block-result {
      position: absolute;
      font-size: 0.9rem;
      border: none;
      z-index: 99;
      top: 100%;
      left: 16px;
      right: 16px;
      padding: 10px;
      -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.5);
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.5);
      border-radius: 4px;
      border-top-left-radius: 0px;
      border-top-right-radius: 0;
      background-color: #f7f7f7;
    }
  }
  
  
  /* styles for page elements */
  
  // div.card-body.p-2 h5 a {
  //   color: #3d7ee5;
  // }
</style>