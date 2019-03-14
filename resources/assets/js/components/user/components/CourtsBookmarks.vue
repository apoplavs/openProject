<template>
  <div class="courtsBookmarks">
    <!-- Main list -->
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <span>
                  <i class="fa fa-bookmark " aria-hidden="true"></i>
                  Закладки судових установ
              </span>
        <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="search">
      </div>
      <div id="courts-list">
        <!--court-list-->
        <spinner v-if="!loadData" />
        <court-component v-if="loadData" :courtsList="filterBookmarks" @deleteBookmark="deleteBookmark" />
      </div>
    </div>
  </div>
</template>

<script>
  import _ from "lodash";
  import CourtComponent from "../../rating/courts/CourtComponent.vue";
  import Spinner from "../../shared/Spinner.vue";
  
  export default {
    name: "CourtsBookmarks",
    components: {
      CourtComponent,
      Spinner
    },
    data() {
      return {
        loadData: false,
        bookmarks: [],
        search: ''
      };
    },
    computed: {
      filterBookmarks() {
        //  живий пошук = фільтр
        return _.filter(this.bookmarks, el => {
          let arr = _.filter(Object.keys(el), key => {
            let regEx = new RegExp(`(${this.search})`, "i");
            return regEx.test(el.court_name) || this.search.length == 0;
          });
          return arr.length > 0 ? true : false;
        });
      }
    },
    created() {
      this.getCourtsList();
    },
    methods: {
      getCourtsList() {
        if (localStorage.getItem("token")) {
          axios
            .get("/api/v1/user/bookmarks/courts", {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                Authorization: localStorage.getItem("token")
              }
            })
            .then(response => {
              this.bookmarks = response.data;
              /* -------------------delete!!!!  ------------------------------------------*/
              // this.bookmarks.forEach(obj => {
              //   obj.is_bookmark = true;
              // });
              this.loadData = true;
              console.log("Courts Bookmarks", this.bookmarks);
            })
            .catch(error => {
              if (error && error.response && error.response.status === 401) {
                this.$router.push("/login");
              }
            });
        } else {
          this.$router.push("/login");
        }
      },
      deleteBookmark(court) {
        this.bookmarks = this.bookmarks.filter(e => {
          return e.court_code !== court.court_code;
        });
      }
    }
  };
</script>

<style scoped lang="scss">
  @import "../../../../sass/judges_coutrs_list.scss";
  .courtsBookmarks {
    margin-top: 3rem;
  }
  
  .card {
    background: none !important;
    box-shadow: none;
    .card-header {
      .fa-bookmark {
        color: #ffffff;
        font-size: 1.4rem;
        margin-right: 15px;
      }
    }
  }
  
  input[type="search"] {
    width: 200px;
  }
</style>