<template>
    <div class="courtsList" @keyup.enter="setFilters()">
      <div class="row">
        <div class="col-3 filters">
          <!-- filters -->
          <filters :filters="filters" @resetFilters="resetFilters" @setFilters="setFilters" :expired="false"/> 
        </div>
        <!-- Main list -->
        <div class="col-9 list-data-container">
          <div class="row">
            <div class="col-10 autocomplete">
              <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="filters.search" @keyup="liveSearch()">
              <div class="autocomplete-block-result" v-if="autocomplete.length">
                <div class="autocomplete-block-result_element" v-for="(el, ind_2) in autocomplete" :key="ind_2">
                  <router-link :to="`/courts/${el.court_code}`">
                    {{ el.name }}
                  </router-link>
                </div>
              </div>
            </div>
            <div class="col-2 pl-0">
              <button type="button" class="btn b-confirm w-100" @click="setFilters()"><i class="fa fa-search" aria-hidden="true"></i> знайти</button>
            </div>
          </div>
          <div class="card courts-card">
            <div class="card-header">
              <span>Список судів</span>
              <div class="d-flex align-items-center">
                <span class="mr-2 sort"> сортувати за: </span>
                <select class="form-control select-sort" name="sorting" v-model="filters.sort" @change="sortList()">
                  <option value="1">назвою (А->Я) <i class="fa fa-sort-alpha-asc" aria-hidden="true"></i></option>
                  <option value="2">назвою (Я->А)</option>
                  <option value="3">рейтингом (низький->високий)</option>
                  <option value="4">рейтингом (високий->низький)</option>
                </select>
              </div>
            </div>
            <div id="courts-list">
              <!--court-list-->
              <spinner v-if="!loadData" />
              <court-component v-if="loadData" :courtsList="courtsList.data" />
            </div>
          </div>
          <div class="pagination mb-5">
            <vue-ads-pagination ref="pagins" @page-change="pageChange" :total-items="courtsList.total" :max-visible-pages="5" :button-classes="buttonClasses" :loading="false"/>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
  import VueAdsPagination from 'vue-ads-pagination';
  import _ from 'lodash';

  import CourtComponent from './CourtComponent.vue';
  import Spinner from '../../shared/Spinner.vue';
  import Filters from '../../shared/Filters.vue';
  
  
  export default {
    name: "CourtsList",
    components: {
      CourtComponent,
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
        autocomplete: [],
        courtsList: {
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
      let initialFilters = JSON.parse(sessionStorage.getItem('courts-filters'));
      if (initialFilters) {
        this.filters = initialFilters;
      }
      this.getCourtsList();
    },
    methods: {
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
          axios.get('/api/v1/courts/autocomplete', {
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
      sortList: _.debounce(function(event) {
        this.loadData = false;
        window.scrollTo(0, 0);
        this.getCourtsList();
        sessionStorage.setItem('courts-filters', JSON.stringify(this.filters));
      }, 10),
  
      pageChange(page) {
        window.scrollTo(0, 0);
        this.loadData = false;
        this.filters.page = page + 1;
        this.getCourtsList();        
      },
   
      getCourtsList() {
        this.autocomplete = []; // коли визиваємо цей метод liveSearch маємо закрити
        this.filters.expired = (this.filters.expired === true || this.filters.expired === 1) ? 1 : 0; 
        if (this.validateInputSearch() === false) { // !! = true
          this.filters.search = null;
        }
        if (localStorage.getItem('token')) {
          axios
            .get('/api/v1/courts/list', {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "Authorization": localStorage.getItem('token')
              },
              params: this.filters
            })
            .then(response => {
              this.courtsList = response.data;
              this.loadData = true;
            })
            .catch(error => {
              if (error.response.status === 401) {
                this.$router.push('/login');
              }
              console.log(error);
            });
        } else {
          axios
            .get("/api/v1/guest/courts/list", {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
              },
              params: this.filters
            })
            .then(response => {
              this.courtsList = response.data;
              this.loadData = true;
            })
            .catch(error => {
              console.log(error);
            });
        }
      },
      setFilters() {
        window.scrollTo(0, 0);
        this.loadData = false;
        this.$refs.pagins.currentPage = 0;
        this.filters.page = 1;
        this.getCourtsList();
        sessionStorage.setItem('courts-filters', JSON.stringify(this.filters));
      },
  
      resetFilters() {
        this.autocomplete = [];
        this.loadData = false;   
        this.getCourtsList(); // онуляємо всі фільтри і визиваємо функцію
        sessionStorage.removeItem('courts-filters');
      }
    },
  };
</script>

<style lang="scss">
  @import "../../../../sass/_variables.scss";
  .pagination {
    .bg-active {
      background-color: $main-color;
      border-color: $main-color;
    }
    button {
      &:active,
      &:focus {
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
  .courts-card {
    background: none !important;
    box-shadow: none;
  }
</style>