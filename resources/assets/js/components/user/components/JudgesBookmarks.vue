<template>
  <div class="JudgesBookmarks">
    <!-- Main list -->
    <div class="col-12 list-data-container">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
          <span><i class="fa fa-bookmark" aria-hidden="true"></i>Закладки суддів</span>
          <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="search">
        </div>
        <div class="jugdes-list">
          <!--court-list-->
          <spinner v-if="!loadData"/>
          <div class="p-3" v-if="filterBookmarks && !filterBookmarks.length && loadData">За заданими параметрами нічого не знайдено</div>
          <div v-else class="judge-card" v-for="(judge, ind) of filterBookmarks" :key="ind">
            
            <div class="body">
              <div class="name pb-1 text-center">
                <router-link :to="`/judges/${judge.id}`">
                    {{ judge.surname }} {{ (judge.name.length != 1) ? judge.name : judge.name + '.' }} 
                    {{ judge.patronymic.length != 1 ? judge.patronymic : judge.patronymic + '.' }}
                </router-link>
              </div>
              <div class="photo">
                <router-link :to="`/judges/${judge.id}`">
                    <img class="avatar" :src="judge.photo" alt="фото">
                </router-link>
              </div>
              <div class="court-name text-center py-2">{{ judge.court_name }}</div>
            </div>
            <div class="footer">
              <div class="row mt-2">
                <div class="col-10 status">
                  <!-- status-component -->
                  <status-component :judgeData="judge"/>
                  <i
                    class="fa fa-edit pl-3 float-right"
                    aria-hidden="true"
                    @click="showModal(judge)"
                  ></i>
                </div>
                <div class="col-2 bookmark">
                  <span @click="deleteBookmark(judge)">
                    <i class="fa fa-bookmark" aria-hidden="true"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- modal change status -->
    <change-status
      v-if="isModalVisible"
      :judgeData="currentJudge"
      @closeModal="isModalVisible = !isModalVisible"
    />
  </div>
</template>

<script>
import _ from "lodash";
import Spinner from "../../shared/Spinner.vue";
import StatusComponent from "../../shared/StatusComponent.vue";
import ChangeStatus from "../../shared/ChangeStatus.vue";

export default {
  name: "JudgesBookmarks",
  components: {
    Spinner,
    StatusComponent,
    ChangeStatus
  },
  data() {
    return {
      loadData: false,
      bookmarks: [],
      search: "",
      isModalVisible: false,
      currentJudge: {}
    };
  },
  computed: {
    filterBookmarks() {
      //  живий пошук = фільтер
      return _.filter(this.bookmarks, el => {
        let arr = _.filter(Object.keys(el), key => {
          let regEx = new RegExp(`(${this.search})`, "i");
          return regEx.test(el.surname) || this.search.length == 0;
        });
        return arr.length > 0 ? true : false;
      });
    }
  },
  created() {
    this.getJudgesList();
  },
  methods: {
    getJudgesList() {
      if (localStorage.getItem("token")) {
        axios
          .get("/api/v1/user/bookmarks/judges", {
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              Authorization: localStorage.getItem("token")
            }
          })
          .then(response => {
            this.bookmarks = response.data;
            /* -------------------delete!!!!  ------------------------------------------*/
            this.bookmarks.forEach(obj => {
              obj.is_bookmark = true;
            });
            this.loadData = true;
            console.log("Judges Bookmarks", this.bookmarks);
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
    deleteBookmark(judge) {
      this.bookmarks = _.filter(this.bookmarks, e => {
        return e.id !== judge.id;
      });
    },

    showModal(judge) {
      this.currentJudge = judge;
      this.isModalVisible = true;
    }
  }
};
</script>

<style scoped lang="scss">
@import "../../../../sass/judges_coutrs_list.scss";
input[type="search"] {
  width: 200px;
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
.jugdes-list {
  display: flex;
  flex: 1;
  flex-wrap: wrap;

  .judge-card {
    width: 250px;
    min-height: 350px;
    background-color: #ffffff;
    margin: 10px 10px;
    padding: 15px;
    line-height: 1.3;
    box-shadow: $shadow-3D-effect;
    @include alignElement($flexDirection: column, $justifyContent: space-between, $alignItems: stretch);

    .name {
      text-overflow: ellipsis;
      overflow: hidden;
      & > a {
        white-space: nowrap;
        color: $primary;
      }
    }
    .photo {
      width: 100%;
      height: 200px;
      .avatar {
        width: 100%;
        height: 100%;
      }
    }
    .status {
        color: $text-muted;
        @include alignElement($justifyContent: start);
    }
    .bookmark {
        @include alignElement();
    } 
    .footer {
        font-size: .8rem;
    }
  }
}
</style>