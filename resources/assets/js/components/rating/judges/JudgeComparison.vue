<template>
  <div>
    <div v-if="!this.judgesList || this.judgesList.length == 0">
      Не додано жодного суддю для порівняння</div>

    <div v-else>
      <table class="table">
        <thead>
        <tr>
          <th scope="col">#</th>
          <div
                  class="judge-component row py-3 mx-1"
                  v-for="(judge, index) of judgesList"
                  :key="index" >
            <th scope="col">Last</th>
          </div>

          <th scope="col">Handle</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>




    <div class="card-body">

      <div v-if="this.judgesList && this.judgesList.length > 0">
        <div
          class="judge-component row py-3 mx-1"
          v-for="(judge, index) of judgesList"
          :key="index"
        >
          <div class="col-9 d-flex pl-0 main-info">
            <div class="mr-3">
              <img class="avatar" :src="judge.photo" alt="фото" :class="{littleAvatar: littlePhoto}">
            </div>
            <div>
              <h5>
                <router-link
                  :to="`/judges/${judge.id}`"
                >{{ judge.surname }} {{ (judge.name.length != 1) ? judge.name : judge.name + '.' }} {{ judge.patronymic.length != 1 ? judge.patronymic : judge.patronymic + '.' }}</router-link>
              </h5>
              <div class="court_name">{{ judge.court_name }}</div>
              <div class="pt-3">
                 <i class="fas fa-balance-scale p-1" aria-hidden="true" title="Додати до порівняння" @click="addToCompare(judge.id)"><sup>+</sup></i>
              </div>
            </div>
          </div>
          <div class="col-3 pl-0 additional-info">
            <div class="align-center pb-3">
              <div class="w-75">
                <span class="float-left">
                  <i class="fa fa-line-chart float-right" aria-hidden="true"> {{ judge.rating + '%' }}</i>
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
    </div>
  </div>
</template>

<script>
    import _ from 'lodash';
    import StatusComponent from "../../shared/StatusComponent.vue";
    import ChangeStatus from "../../shared/ChangeStatus.vue";

    export default {
        name: "JudgeComparison",
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
                currentJudge: {},
            };
        },
        filters: {
            formatDate(date) {
                // getMobth() чомусь рахує місяці з 0 date.getMonth() + 1 //
                if (date === '' || date === null) {
                    return '';
                } else {
                    return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
                }
            }
        },
		created() {
			let judge_compare = [];
			if (sessionStorage.judge_compare) {
				judge_compare = JSON.parse(sessionStorage.getItem("judge_compare"));
			}

			// якщо масив пустий
			if (judge_compare.length < 1 || !this.$store.getters.isAuth) {
				this.$router.push("/judges");
			}
            // оримуємо список для поріняння
			$$.each(judge_compare, function(key, value) {
				axios
					.get(`/api/v1/judges/${value}`, {
						headers: {
							"Content-Type": "application/json",
							"X-Requested-With": "XMLHttpRequest",
							Authorization: localStorage.getItem("token")
						}
					})
					.then(response => {
						this.judgesList.push(response.data);
						//console.log("JUdge PROFILE", this.judge);
					})
					.catch(error => {
						if (error.response && error.response.status === 401) {
							this.$router.push("/login");
						}
						this.$router.push("/judges");
					});
				console.log(this.judgesList);
			});

        },
        methods: {
            formattingDate(date) {
                if (date === '' || date === null) {
                    return '';
                } else {
                    let arr = _.split(date, '.');
                    return `${arr[2]}-${arr[1]}-${arr[0]}`;
                }
            },
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