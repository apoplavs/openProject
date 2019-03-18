<template>
  <div>
    <div class="card-body">
      <div
        v-if="!this.courtsList || this.courtsList.length == 0"
      >За заданими параметрами нічого не знайдено...</div>
      <div v-if="this.courtsList && this.courtsList.length > 0">
        <div
          class="court-component row mb-3"
          v-for="(court, ind_1) of this.courtsList"
          :key="ind_1"
        >
          <div class="col-2 court-component_left">
            <i class="fa fa-university" aria-hidden="true"></i>
            <b>{{court.court_code}}</b>
          </div>
          <div class="col-10 court-component_right py-3">
            <router-link :to="`/courts/${court.court_code}`">
              <div class="court-name">{{court.court_name}}</div>
            </router-link>
            <div class="part-1 mt-3">
              <div>
                <span>Голова суду:</span>
                {{court.head_judge ? court.head_judge : 'невідомо'}}
              </div>
              <div>
                <span>Інстанція суду:</span>
                {{court.instance ? court.instance : 'невідомо'}}
              </div>
            </div>
            <div class="part-2 mt-3">
              <div>
                <span>Регіон:</span>
                {{court.region ? court.region : 'невідомо'}}
              </div>
              <div>
                <span>Адреса:</span>
                {{court.address ? court.address : 'невідомо'}}
              </div>
            </div>
            <div class="part-3 mt-3">
              <div class="rating">
                <i class="fa fa-line-chart mr-1" aria-hidden="true"></i>
                {{ court.rating + '%' }}
              </div>
              <div class="bookmark">
                <span v-if="court.is_bookmark" @click="deleteBookmark(court)">
                  <i class="fa fa-bookmark" aria-hidden="true"></i>
                </span>
                <span v-if="!court.is_bookmark" @click="setBookmark(court)">
                  <i class="fa fa-bookmark-o" aria-hidden="true"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CourtComponent",
  props: {
    courtsList: Array
  },
  data() {
    return {
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        Authorization: localStorage.getItem("token")
      }
    };
  },
  methods: {
    setBookmark(court) {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }
      court.is_bookmark = 1;
      axios({
        method: "put",
        url: `/api/v1/courts/${court.court_code}/bookmark`,
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          Authorization: localStorage.getItem("token")
        }
      })
      .catch(error => {
          court.is_bookmark = 0;
          if (error.response.status === 401) {
            this.$router.push("/login");
          }
          console.log("Bookmark", error);
        });
    },
    deleteBookmark(court) {
      
      this.$emit("deleteBookmark", court);
      court.is_bookmark = 0;
      axios({
        method: "delete",
        url: `/api/v1/courts/${court.court_code}/bookmark`,
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          Authorization: localStorage.getItem("token")
        }
      })
        .then(response => { 
          
        })
        .catch(error => {
          court.is_bookmark = 1;
          if (error.response.status === 401) {
            this.$router.push("/login");
          }
          console.log("Bookmark", error);
        });
    }
  }
};
</script>

<style scoped lang="scss">
@import "../../../../sass/_variables.scss";
@import "../../../../sass/_mixins.scss";

.court-component {
  .court-component_left {
    background-color: $body-bg;
    @include boxShadow($shadow-header);
    @include alignElement(column);
    .fa-university {
      color: $primary;
      font-size: 1.8rem;
      margin-bottom: 10px;
    }
  }
  .court-component_right {
    @include boxShadow($shadow-header);
    background-color: #ffffff;
    .court-name {
      color: $primary;
      font-size: 1.3rem;
      &:hover {
        color: $primary;
        text-decoration: underline;
      }
    }
    .part-1,
    .part-2 {
      line-height: 1.3rem;
      font-size: 0.9rem;
      span {
        color: $text-color;
        font-weight: 700;
      }
    }
    .part-1 > div:last-child,
    .part-2 > div:first-child {
      color: $primary;
    }
    .part-3 {
      @include alignElement($justifyContent: space-between);
    }
  }
}
</style>