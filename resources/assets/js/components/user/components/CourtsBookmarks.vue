<template>
  <div class="courtsBookmarks">
    <!-- Main list -->
    <div class="col-12 list-data-container">
      <div class="card courts-card">
        <div class="card-header">
          <span>Список судів</span>
        </div>
        <div id="courts-list">
          <!--court-list-->
          <spinner v-if="!loadData"/>
          <court-component v-if="loadData" :courtsList="bookmarks"/>
        </div>
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
      bookmarks: []
    };
  },
  created() {
    this.getCourtsList();
  },
  methods: {
    getCourtsList() {
      if (localStorage.getItem("token")) {
        axios
          .get("/api/v1/user/bookmarks", {
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              Authorization: localStorage.getItem("token")
            }
          })
          .then(response => {
            this.bookmarks = response.data.courts;
            this.loadData = true;
            console.log('Courts Bookmarks', this.bookmarks);
          })
          .catch(error => {
            if (error.response.status === 401) {
              this.$router.push("/login");
            }
          });
      } else {
          this.$router.push("/login");
      }
    }
  }
};
</script>

<style scoped lang="scss">
    @import "../../../../sass/judges_coutrs_list.scss";
    .courts-card {
        background: none !important;
        box-shadow: none;
    }
</style>