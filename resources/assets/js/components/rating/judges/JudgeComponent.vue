<template>
  <div>
    <div class="card-body">
      <div v-if="this.judgesList && this.judgesList.length > 0">
        <div
          class="judge-component row py-3 mx-1"
          v-for="(judge, index) of judgesList"
          :key="index"
        >
          <div class="col-9 d-flex pl-0 main-info">
            <div class="mr-3">
              <img
                class="avatar"
                :src="judge.photo"
                alt="фото"
                :class="{littleAvatar: littlePhoto}"
              >
            </div>
            <div>
              <h5>
                <router-link
                  :to="`/judges/${judge.id}`"
                >{{ judge.surname }} {{ (judge.name.length != 1) ? judge.name : judge.name + '.' }} {{ judge.patronymic.length != 1 ? judge.patronymic : judge.patronymic + '.' }}</router-link>
              </h5>
              <div class="court_name">{{ judge.court_name }}</div>
              <i
                v-if="$route.fullPath === '/judges'"
                class="fas fa-balance-scale p-1"
                aria-hidden="true"
                title="Додати до порівняння"
                @click="addToCompare(judge.id)"
              >
                <sup>+</sup>
              </i>
            </div>
          </div>
          <div class="col-3 pl-0 additional-info">
            <div class="align-center pb-3">
              <div class="w-75">
                <div class="rating">
                <i class="fa fa-line-chart mr-1" aria-hidden="true"></i>
                {{ judge.rating + '%' }}
              </div>
              </div>
              <div class="w-25 bookmark">
                <span v-if="judge.is_bookmark" @click="deleteBookmark(judge)">
                  <i class="fa fa-bookmark" aria-hidden="true" title="Видалити з закладок"></i>
                </span>
                <span v-if="!judge.is_bookmark" @click="setBookmark(judge)">
                  <i class="fa fa-bookmark-o" aria-hidden="true" title="Додати в закладки"></i>
                </span>
              </div>
            </div>
            <div class="align-center">
              <div class="w-75">
                <!-- status-component -->
                <status-component :judgeData="judge"/>
              </div>
              <div class="w-25">
                <i class="fa fa-edit p-1 float-right" aria-hidden="true" @click="showModal(judge)"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-else>За даними параметрами нічого не знайдено...</div>
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
import StatusComponent from "../../shared/StatusComponent.vue";
import ChangeStatus from "../../shared/ChangeStatus.vue";

export default {
  name: "JudgeComponent",
  props: {
    judgesList: Array,
    littlePhoto: {
      type: Boolean,
      default: false
    }
  },
  components: {
    StatusComponent,
    ChangeStatus
  },
  data() {
    return {
      isModalVisible: false,
      currentJudge: {}
    };
  },
  filters: {
    formatDate(date) {
      // getMobth() чомусь рахує місяці з 0 date.getMonth() + 1 //
      if (date === "" || date === null) {
        return "";
      } else {
        return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
      }
    }
  },
  methods: {
    // порівняння суддів
    addToCompare(judge_id) {      
      this.$emit('addToCompare', judge_id);
    },

    formattingDate(date) {
      if (date === "" || date === null) {
        return "";
      } else {
        let arr = _.split(date, ".");
        return `${arr[2]}-${arr[1]}-${arr[0]}`;
      }
    },
    setBookmark(judge) {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }
      judge.is_bookmark = 1;
      axios({
        method: "put",
        url: `/api/v1/judges/${judge.id}/bookmark`,
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          Authorization: localStorage.getItem("token")
        }
      })
      .catch(error => {
          if (error.response && error.response.status === 401) {
            this.$router.push("/login");
          }
          judge.is_bookmark = 0;
          this.$toasted.error(
            "Неможливо додати в закладки, перевірте Ваше інтернет з'єднання або спробуйте пізніше",
            {
              theme: "primary",
              position: "top-right",
              duration: 5000
            }
          );
          console.log("Bookmark", error);
        });
    },
    deleteBookmark(judge) {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }
      judge.is_bookmark = 0;
      axios({
        method: "delete",
        url: `/api/v1/judges/${judge.id}/bookmark`,
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
          Authorization: localStorage.getItem("token")
        }
      })
        .catch(error => {
          if (error.response.status === 401) {
            this.$router.push("/login");
          }
          judge.is_bookmark = 1;
          this.$toasted.error(
            "Перевірте Ваше інтернет з'єднання або спробуйте пізніше",
            {
              theme: "primary",
              position: "top-right",
              duration: 5000
            }
          );
          console.log("Bookmark", error);
        });
    },
    showModal(judge) {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }
      this.currentJudge = judge;
      this.isModalVisible = true;
    }
  }
};
</script>

<style scoped lang="scss">
@import "../../../../sass/_variables.scss";
@import "../../../../sass/_mixins.scss";

.judge-component:not(:last-child) {
  border-bottom: 1px solid lightgray;
}

.additional-info {
  font-size: 0.8rem;
  color: grey;
  .fa,
  .fas {
    font-size: 1.1rem;
    margin-right: 5px;
  }
  .bookmark > span {
    float: right;
    padding-right: 7px;
  }
  .bookmark > span,
  .fa-pencil {
    cursor: pointer;
  }
  .align-center {
    @include alignElement();
  }
}

.main-info {
  .avatar {
    width: 120px;
    height: 120px;
  }
  .littleAvatar {
    width: 60px !important;
    height: 60px !important;
  }
  a {
    color: $primary;
  }
  .court_name {
    font-size: 1rem;
  }
}
</style>