<template>
  <div class="container">
  
    <!-- Search -->
  
    <div class="container mt-5">
      <div class="row">
        <div class="offset-lg-3 col-lg-9">
          <form method="get" autocomplete="off">
            <div class="form-row">
              <div class="col-12 col-md-9 mb-2 mb-md-0 autocomplete">
                <input type="search" class="form-control" id="search-input" placeholder="Пошук..." v-model="params.search">
              </div>
              <div class="col-6 col-md-3">
                <button type="button" class="btn btn-block btn btn-primary" onclick="findJudge()"><i class="fa fa-search" aria-hidden="true"></i> знайти</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  
    <!-- Filters -->
  
    <div class="container">
      <div class="row">
        <div class="col-lg-3" id="filters">
          <div class="card my-4">
            <h4 class="card-header"><i class="fa fa-filter" aria-hidden="true"></i> Фільтри</h4>
            <div class="card-body">
              <form method="get" id="form-filters" name="filters">
  
                <div class="row">
                  <div class="col-lg-12">
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
                <div class="row">
                  <div class="col-lg-12">
                    <input type="checkbox" v-model="params.expired">Закінчились повноваження
                  </div>
                </div>
  
                <hr id="apply-filters-mark">
  
                <div id="apply-filters">
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
              </form>
            </div>
          </div>
          <!-- Card -->
          <div>Регіон------------- {{params.regions }}</div>
          <div>Регіон------------- {{ params.instance }}</div>
          <div>Регіон------------- {{ params.jurisdiction }}</div>
          <div>Регіон------------- {{ params.search}}</div>
          <div>bool------------- {{ params.expired}}</div>
        </div>
        <!--  col-3 Filters  -->

        <!-- Main list -->
        <div class="col-lg-9">
          <div class="card card-outline-secondary my-4">
            <div class="card-header">
              Список суддів
              <span class="ml-5"> сортувати: 
                  <label id="sorting-type">
                    <input type="checkbox" @change="changeSorting()" form="form-filters" name="sorting">
                    <i class="fa fa-sort-alpha-asc" aria-hidden="true"></i>
                  </label>
                </span>
            </div>
            <div id="judges-list">
              <!--judges-judges-list-->
              <judge-component :list="judgesList"></judge-component>
            </div>
            <div class="pagination">
              <pagination :data="judgesList" @pagination-change-page="paginationChangePage()"></pagination>
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- col-lg-9 -->
      </div>
      <!-- row -->
    </div>
    <!-- container -->
  
  </div>
</template>

<script>
  import JudgeComponent from './JudgeComponent.vue';
  
  export default {
    name: "judges-list",
    data: () => {
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
        message: "",
        judgesList: {},
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        }
      }
    },
    mounted() {
      this.getJudgesList();
    },
    methods: {
      paginationChangePage(el) {
        console.log(el);
  
      },
      changeSorting(e) {
        console.log('checkbox ', e);
        
      },
      getJudgesList() {
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
              this.judgesList = response;
              console.log('Response',judgesList);
            })
            .catch(error => {
              console.log('Каже що не авторизований пффф та Канеха');
            });
        } else {
          console.log('no token')
          // debugger;
          axios
            .get("/api/v1/guest/judges/list", {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
              },
              params: this.params
            })
            .then(response => {
              this.judgesList = response;
              console.log('response--',response);
            })
            .catch(error => {
              console.log(error);
              console.log('Ну нє не логінився я ще');
            });
        }
      },
      resetFilters() {
        this.params.regions = [];
        this.params.instance = [],
          this.params.jurisdiction = []
        // this.params.expired = 0
      }
  
    },
    components: {
      JudgeComponent
  }
  
  };
</script>

<style scoped lang="scss">
  /* Стилі для фільтрів */
  
  #filters {
    font-size: 0.9em;
  }
  
  #filters h6 {
    color: #4c88bd;
  }
  
  
  /* Customize the label (the container) */
  
  #filters ul label {
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
  
  #filters ul input {
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
  
  #filters ul label input:checked~.checkmark {
    background-color: #2b989b;
  }
  
  
  /* Create the checkmark/indicator (hidden when not checked) */
  
  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
  }
  
  
  /* Show the checkmark when checked */
  
  #filters ul label input:checked~.checkmark:after {
    display: block;
  }
  
  
  /* Style the checkmark/indicator */
  
  #filters ul label .checkmark:after {
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
  
  #apply-filters {
    background-color: white;
    position: sticky;
    bottom: 0;
  }
  
  
  /* if display is bigger then 1200px, correct position */
  
  @media (min-width: 1200px) {}
  
  
  /* styles for page elements */
  
  div.col-12.col-lg-2>img {
    width: 120px;
    height: 120px;
  }
  
  #sorting-type,
  .bookmark,
  .fa-pencil {
    cursor: pointer;
  }
  
  #sorting-type input {
    display: none;
  }
  
  div.card-body.p-2 h5 a {
    color: #3d7ee5;
  }
  
  
  /* if display is small then 992 hide part of pagination */
  
  @media (max-width: 992px) {
    .pagination li:nth-child(even) {
      display: none;
    }
  }
  
  
  /* styles for font awesome */
  
  .fa-filter {
    font-size: 21px;
    color: #6291ba;
  }
  
  .fa-sort-alpha-asc,
  .fa-sort-alpha-desc {
    font-size: 18px;
    color: #3d7ee5;
  }
  
  .fa-bookmark-o,
  .fa-bookmark {
    font-size: 18px;
    color: #2b989b;
  }
  
  .fa-briefcase {
    color: green;
    font-size: 15px;
  }
  
  .fa-line-chart {
    color: #6291ba;
  }
  
  .fa-medkit {
    font-size: 17px;
    color: red;
  }
  
  .fa-calendar-check-o,
  .fa-calendar-minus-o,
  .fa-calendar-times-o {
    font-size: 17px;
    color: #2b989b;
  }
  
  .fa-pencil {
    color: #6c757d;
    -webkit-transition-duration: 0.3s;
    -o-transition-duration: 0.3s;
    -moz-transition-duration: 0.3s;
    transition-duration: 0.3s;
  }
  
  .fa-pencil:hover {
    color: #6291ba;
    box-shadow: 0 0 3px rgba(0, 0, 0, 0.5), inset 0 0 1px rgba(0, 0, 0, 0.7);
  }
  
  
  /* styles for autocomplete field  */
  
  .autocomplete {
    /*the container must be positioned relative:*/
    position: relative;
    display: inline-block;
  }
  
  .autocomplete-items {
    position: absolute;
    font-size: 0.9em;
    border: 1px solid #d4d4d4;
    border-bottom: none;
    border-top: none;
    z-index: 99;
    /*position the autocomplete items to be the same width as the container:*/
    top: 100%;
    left: 0;
    right: 0;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.5);
  }
  
  .autocomplete-items div {
    padding: 10px;
    cursor: pointer;
    background-color: #fff;
    border-bottom: 1px solid #d4d4d4;
  }
  
  .autocomplete-items div:hover {
    /*when hovering an item:*/
    background-color: #e9e9e9;
  }
  
  .autocomplete-active {
    /*when navigating through the items using the arrow keys:*/
    background-color: DodgerBlue !important;
    color: #ffffff;
  }
  
  
  /* styles for pop-up form changing status */
  
  select#chooser-judge-status {
    font-family: "FontAwesome", Helvetica, serif;
  }
  
  select#chooser-judge-status option:first-child {
    color: green;
  }
  
  select#chooser-judge-status option:nth-child(2) {
    color: red;
  }
  
  select#chooser-judge-status option:nth-child(3) {
    color: #2b989b;
  }
  
  select#chooser-judge-status option:nth-child(4) {
    color: #2b989b;
  }
  
  select#chooser-judge-status option:last-child {
    color: #6291ba;
  }
</style>