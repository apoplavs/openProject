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
              <!--<div class="pt-3">-->
              <!--<i class="fas fa-balance-scale p-1" aria-hidden="true" title="Додати до порівняння" @click="addToCompare(judge.id)"><sup>+</sup></i>-->
              <!--</div>-->
            </div>
          </div>
          <div class="col-3 pl-0 additional-info">
            <div class="align-center pb-3">
              <div class="w-75">
                <span class="float-left">
                  <i
                    class="fa fa-line-chart float-right"
                    aria-hidden="true"
                  >{{ judge.rating + '%' }}</i>
                </span>
              </div>
              <div class="w-25 bookmark">
                <span v-if="judge.is_bookmark" @click="deleteBookmark(judge)">
                  <i class="fa fa-bookmark" aria-hidden="true"></i>
                </span>
                <span v-if="!judge.is_bookmark" @click="setBookmark(judge)">
                  <i class="fa fa-bookmark-o" aria-hidden="true"></i>
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
    addToCompare(judge_id) {
      let judge_compare = [];
      if (sessionStorage.judge_compare) {
        judge_compare = JSON.parse(sessionStorage.getItem("judge_compare"));
      }

      // якщо суддя вже був доданий раніше
      if (judge_compare.indexOf(judge_id) != -1) {
        this.$toasted.error("Цей суддя вже доданий для порівняння", {
          theme: "outline",
          position: "top-right",
          duration: 3000
        });
        return;
      }

      // якщо занадто багато додається для порівняння
      if (judge_compare.length > 2) {
        this.$toasted.error("Можна порівнювати одночасно до 3 суддів", {
          theme: "outline",
          position: "top-right",
          duration: 3000
        });
        return;
      }
      this.$emit("show-comparation", judge_compare.length);
      judge_compare.push(judge_id);
      sessionStorage.setItem("judge_compare", JSON.stringify(judge_compare));
      this.$toasted.success("Додано до порівняння", {
        theme: "outline",
        position: "top-right",
        duration: 3000
      });
      console.log(judge_id);
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
      }).catch(error => {
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
      }).catch(error => {
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

.fa-balance-scale {
  color: #ffa726;
  cursor: pointer;
}

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
  /* styles for font awesome */
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