<template>
  <div class="court-profile">
    <spinner v-if="!loadData"/>
    <div v-else class="container content-wrapper">
      <div class="court-info">
        <div class="row court-component m-0">
          <div class="col-2 court-component_left">
            <i class="fa fa-university" aria-hidden="true"></i>
            <b>{{court.court_code}}</b>
          </div>
          <div class="col-10 court-component_right py-3">
            <div class="d-flex justify-content-between">
              <router-link :to="`/courts/${court.court_code}`">
                <div class="court-name">{{court.name}}</div>
              </router-link>
              <div class="bookmark pr-3">
                <span v-if="court.is_bookmark" @click="deleteBookmark()">
                  <i class="fa fa-bookmark" aria-hidden="true"></i>
                </span>
                <span v-if="!court.is_bookmark" @click="setBookmark()">
                  <i class="fa fa-bookmark-o" aria-hidden="true"></i>
                </span>
              </div>
            </div>
            <div class="part-1 mt-3">
              <div>
                <span>Голова суду:</span>
                {{court.head_judge ? court.head_judge : 'невідомо'}}
              </div>
              <div>
                <span>Інстанція суду:</span>
                {{court.instance ? court.instance : 'невідомо'}}
              </div>
              <div>
                <span>Юрисдикція суду:</span>
                {{court.jurisdiction ? court.jurisdiction : 'невідомо'}}
              </div>
              <div>
                <span>Регіон:</span>
                {{court.region ? court.region : 'невідомо'}}
              </div>
            </div>
            <div class="part-2 mt-3">
              <div class="detail-info mt-2">
                <div v-for="(location, ind_1) of court.address" :key="ind_1">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>{{ location }}</span>
                  <br>
                </div>
              </div>
              <div class="detail-info mt-1" v-if="court.phone">
                <i class="fas fa-phone"></i>
                <span>{{ court.phone }}</span>
              </div>
              <div class="detail-info mt-1" v-if="court.email">
                <i class="far fa-envelope"></i>
                <span>{{ court.email }}</span>
              </div>
              <div class="detail-info mt-1" v-if="court.site">
                <i class="fas fa-link"></i>
                <a target="_blank" :href="court.site">{{ court.site }}</a>
              </div>
            </div>

            <div class="part-3 mt-3">
              <div class="rating w-100 d-flex justify-content-between">
                <!-- <div>
                  <span class="like mr-4" @click="changeLikes">
                    <i class="fas fa-thumbs-up"></i>
                    {{ 12 }}
                  </span>
                  <span class="dislike" @click="changeUnlikes">
                    <i class="fas fa-thumbs-down"></i>
                    {{ 21 }}
                  </span>
                </div> -->
                <div>
                  <span class="line-chart">
                    <i class="fa fa-line-chart mr-1" aria-hidden="true"></i>
                    {{ court.rating + '%' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- --------------2222222---------------------- -->
        <div class="card mt-3 judges">
          <div class="card-header d-flex justify-content-between">
            <span>Судді</span>
            <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="searchJudges">
          </div>
          <div class="card-body p-0">
            <judge-component :judgesList="filterJudges" :littlePhoto="true"/>
          </div>
        </div>
<!-- --------------33333333---------------------- -->
        <div class="card mt-3 courtSessions">
          <div class="card-header d-flex justify-content-between">
            <span>Засідання</span>
            <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="searchSessions">
          </div>
          <div class="card-body">
            <div v-if="filterSessions.length" class="container-component">
              <div class="row header">
                <div class="col-1 pl-0">Дата розгляду</div>
                <div class="col-1">Номер справи</div>
                <div class="col-2">Судді</div>
                <div class="col-1">Форма</div>
                <div class="col-3">Сторони у справі</div>
                <div class="col-2">Суть справи</div>
                <div class="col-2 pr-0">Примітки</div>
              </div>
              <div class="row body" v-for="(session, i_el) in filterSessions" :key="i_el">
                <div class="col-1 pl-0">
                  <div>{{ session.date }}</div>
                </div>
                <div class="col-1">{{ session.number }}</div>
                <div class="col-2">{{ session.judges }}</div>
                <div class="col-1">{{ session.forma }}</div>
                <div class="col-3">{{ session.involved }}</div>
                <div class="col-2">{{ session.description }}</div>
                <div class="col-2 pr-0 text-center position-relative note-wrap">
                  <i class="fas fa-star" @click="showModalDelete(session)"></i>
                  <textarea class="note" maxlength="254" v-model.trim="session.note"></textarea>
                  <img class="checkmark" src="../../../../images/checkmark.png" @click="saveNote(session)">
                </div>
              </div>
            </div>
            <div v-else>За даними параметрами нічого не знайдено...</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import _ from 'lodash';
import JudgeComponent from '../../rating/judges/JudgeComponent.vue';
import StatusComponent from "../../shared/StatusComponent.vue";
import ChangeStatus from "../../shared/ChangeStatus.vue";
import Spinner from "../../shared/Spinner.vue";

export default {
  name: "CourtProfile",
  components: {
    Spinner,
    StatusComponent,
    ChangeStatus,
    JudgeComponent
  },
  data() {
    return {
      showModal: false,
      loadData: false,
      searchJudges: '',
      searchSessions: '',
      court: {},

    };
  },
   computed: {
      filterJudges() {
        //  живий пошук = фільтер
        return _.filter(this.court.judges, el => {
          let arr = _.filter(Object.keys(el), key => {
            let regEx = new RegExp(`(${this.searchJudges})`, "i");            
            return regEx.test(el.surname) || this.searchJudges.length == 0;
          });
          return arr.length > 0 ? true : false;
        });
      },
      filterSessions() {
        //  живий пошук = фільтер
        return _.filter(this.court.court_sessions, el => {
          let arr = _.filter(Object.keys(el), key => {
            let regEx = new RegExp(`(${this.searchSessions})`, "i");
            return regEx.test(el[key]) || this.searchSessions.length == 0;
          });
          return arr.length > 0 ? true : false;
        });
      },
    },
  created() {
      this.getCourtProfile();
  },
  methods: {
    getCourtProfile() {
      if (this.$store.getters.isAuth) {
        axios
          .get(`/api/v1/courts/${this.$route.params.id}`, {
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              Authorization: localStorage.getItem("token")
            }
          })
          .then(response => {
            this.court = response.data;              
            this.loadData = true;
            console.log("COURT ____ PROFILE", this.court);
          })
          .catch(error => {
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
            console.log("error");
          });
      } else {
        console.log("not log in");
        axios
          .get(`/api/v1/guest/courts/${this.$route.params.id}`, {
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest"
            }
          })
          .then(response => {
            this.court = response.data;
            this.loadData = true;
            console.log("COURT ____ PROFILE not log in", this.court);
          })
          .catch(error => {
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
            console.log(error);
          });
      }
    },
    setBookmark() {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }
      axios({
        method: "put",
        url: `/api/v1/courts/${this.$route.params.id}/bookmark`,
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          Authorization: localStorage.getItem("token")
        }
      })
      .then(response => {
        this.court.is_bookmark = 1;
      })
      .catch(error => {
        if (error.response && error.response.status === 401) {
          this.$router.push("/login");
        }
        console.log("Bookmark", error);
      });
    },
    deleteBookmark() {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }
      axios({
        method: "delete",
        url: `/api/v1/courts/${this.$route.params.id}/bookmark`,
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          Authorization: localStorage.getItem("token")
        }
      })
      .then(response => {
        this.court.is_bookmark = 0;
      })
      .catch(error => {
        if (error.response.status === 401) {
          this.$router.push("/login");
        }
        console.log("Bookmark", error.response);
      });
    }
  }
};
</script>

<style scoped lang='scss'>
@import "../../../../sass/_variables.scss";
@import "../../../../sass/_mixins.scss";
.court-profile {
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
      .bookmark {
        color: $warning;
      }
      .court-name {
        color: $primary;
        font-size: 1.5rem;
        &:hover {
          color: $primary;
          text-decoration: underline;
        }
      }
      .part-1 {
        line-height: 1.3rem;
        font-size: 0.9rem;
        span {
          color: $text-color;
          font-weight: 700;
        }
      }
      .part-2 {
        .detail-info {
          //   @include alignElement($justifyContent: start, $alignItems: start);
          color: $text-muted;
          font-size: 0.9rem;
          i[class^="fa"] {
            margin-right: 10px;
            padding-top: 5px;
          }
        }
      }
      .part-1 {
        & > div:first-child,
        & > div:last-child {
            color: $primary;
        }
      }
      .part-3 {
        //   @include alignElement($justifyContent: space-between);
        .rating {
          color: $main-color;
        }
        .like {
          color: green;
          font-size: .9rem;
          cursor: pointer;
        }
        .dislike {
          color: red;
          font-size: .9rem;
          cursor: pointer;
        }
      }
    }
  }
  .judges,
  .courtSessions {
      .card-body {
          max-height: 700px;
          overflow: hidden;
          overflow-y: scroll;
        //   padding: 0;
      }
  }
  input[type='search']{
    width: 200px;
  }



//   session 

.courtSessions {
  width: 100%;
  height: auto;
  margin-top: 50px;
  .card-header {
    .fa-bookmark {
      color: #ffffff;
      font-size: 1.4rem;
      margin-right: 15px;
    }
  }
  .infoCard {
    padding: 20px;
    > p:first-child {
      font-size: 1.1rem;
    }
  }
  .header {
    font-size: 0.9rem;
    font-weight: 700;
    align-items: center;
    line-height: 1.4;
  }
  .fa-star {
    color: $main-color;
    cursor: pointer;
    font-size: 15px;
    float: right;
    padding: 0 0px 0px 10px;
    margin-right: -10px;
  }
  input[type="search"] {
    width: 200px;
  }
  .note {
    height: calc(100% - 13.5px);
    width: 100%;
    border: none;
    resize: none;
    background-color: #fafa599c;
    padding: 3px 5px 15px 5px;
    font-size: 0.7rem;
    margin-top: -5px;
    color: #002366;
    font-style: italic;
    background: linear-gradient(-135deg, transparent 10px, #fafa599c 0);
  }
  textarea.note:before {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    border-top: 80px solid white;
    border-left: 80px solid rgba(0, 0, 0, 0);
    width: 0;
  }
  .checkmark {
    width: 25px;
    position: absolute;
    bottom: 5px;
    right: 0;
    cursor: pointer;
  }

  .container-component {
    padding: 0;
    background-color: #ffffff;
    .body {
      font-size: .8rem;
    }
  }

  .row {
    margin: 0;
    padding: 15px 0;
    &:not(:last-child) {
      border-bottom: 1px solid $text-muted;
    }
  }
  .note-wrap {
    min-height: 160px;
    max-height: 200px;
  }
  .col-1,
  .col-2,
  .col-3 {
    padding-right: 5px;
    padding-left: 5px;
  }
}
}




     
</style>