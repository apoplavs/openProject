<template>
  <div class="judge-profile">
    <spinner v-if="!loadData"/>
    <div v-if="loadData" class>
      <div class="judge-info">
        <div class="card">
          <div class="card-body d-flex">
            <div class="photo w-25 text-center">
              <img :src="judge.data.photo" alt="avatar" class="w-100">
              <button
                type="button"
                class="btn btn-add-photo mt-2"
                @click="show = true"
                v-if="judge.data.photo === '/img/judges/no_photo.jpg' && showBtnAddPhoto &&	isAuth"
              >Додати фото</button>
              <!-- upload photo  -->
              <my-upload
                field="img"
                langType="ua"
                @crop-success="cropSuccess"
                @crop-upload-success="cropUploadSuccess"
                @crop-upload-fail="cropUploadFail"
                @src-file-set="srcFileSet"
                v-model="show"
                :width="256"
                :height="256"
                url="/api/v1/judges/photo"
                :params="params"
                :headers="headers"
                img-format="png"
              ></my-upload>
              <!-- end -->
            </div>
            <div class="w-75 px-3">
              <div class="main-info pb-2">
                <h3>{{ judge.data.surname + ' ' + judge.data.name + ' ' + judge.data.patronymic }}</h3>
                <div class="d-flex">
                  <i class="fa fa-university" aria-hidden="true"></i>
                  <h5 class="court-name">{{ judge.data.court_name }}</h5>
                </div>
                <div class="detail-info mt-2" v-if="judge.data.court_address">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>{{ judge.data.court_address }}</span>
                </div>
                <!-- previous works -->
                <div class="detail-info" v-for="(prevWork, ind) in judge.previous_works" :key="ind">
                  <span>{{ prevWork }}</span>
                </div>
                <div class="detail-info mt-1" v-if="judge.data.court_phone">
                  <i class="fas fa-phone"></i>
                  <span>{{ judge.data.court_phone }}</span>
                </div>
                <div class="detail-info mt-1" v-if="judge.data.court_email">
                  <i class="far fa-envelope"></i>
                  <span>{{ judge.data.court_email }}</span>
                </div>
                <div class="detail-info mt-1" v-if="judge.data.court_site">
                  <i class="fas fa-link"></i>
                  <a target="_blank" :href="judge.data.court_site">{{ judge.data.court_site }}</a>
                </div>
              </div>
              <div class="status-info">
                <div class="status my-2">
                  <div class="w-50 d-flex align-items-center">
                    <!-- status component-->
                    <status-component :judgeData="judge.data"/>
                    <!-- end -->
                    <span>
                      <i
                        class="fa fa-edit float-right pl-3"
                        aria-hidden="true"
                        title="Змінити статус"
                        @click="showModal = true"
                      ></i>
                    </span>
                  </div>
                  <div class="bookmark w-50">
                    <span v-if="judge.data.is_bookmark" @click="changeBookmarkStatus()">
                      <i class="fa fa-bookmark" aria-hidden="true" title="Видалити з закладок"></i>
                    </span>
                    <span v-if="!judge.data.is_bookmark" @click="changeBookmarkStatus()">
                      <i class="fa fa-bookmark-o" aria-hidden="true" title="Додати в закладки"></i>
                    </span>
                  </div>
                </div>
                <div class="rating">
                  <span class="like" @click="changeLikes">
                    <i class="fas fa-thumbs-up"></i>
                    {{ judge.data.likes }}
                  </span>
                  <span class="line-chart">
                    <div class="rating">
                      <i class="fa fa-line-chart mr-1" aria-hidden="true"></i>
                      {{ judge.data.rating + '%' }}
                    </div>
                  </span>
                  <span class="dislike" @click="changeUnlikes">
                    <i class="fas fa-thumbs-down"></i>
                    {{ judge.data.unlikes }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="card mt-2">
          <div class="card-header d-flex justify-content-between">
            <span>Найближчі судові засідання</span>
            <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="search">
          </div>
          <div class="card-body court-sessions-container">
            <div class="court-sessions">
              <div v-if="filterSessions.length > 0" class="container-component">
                <div class="row header text-muted">
                  <div class="col-1 pl-0">Дата розгляду</div>
                  <div class="col-1">Номер справи</div>
                  <div class="col-2">Судді</div>
                  <div class="col-2">Форма</div>
                  <div class="col-3">Сторони у справі</div>
                  <div class="col-2">Суть справи</div>
                  <div class="col-1 pr-0"></div>
                </div>
                <div class="row" v-for="(session, i_el) in filterSessions" :key="i_el">
                  <div class="col-1 pl-0">
                    <div>{{ session.date }}</div>
                  </div>
                  <div class="col-1">{{ session.number }}</div>
                  <div class="col-2">{{ session.judges }}</div>
                  <div class="col-2">{{ session.forma }}</div>
                  <div class="col-3">{{ session.involved }}</div>
                  <div class="col-2">{{ session.description }}</div>
                  <div class="col-1 pr-0 center" v-if="isAuth">
                    <i
                      v-if="session.is_bookmark"
                      class="fas fa-star"
                      title="Видалити з закладок"
                      @click="deleteBookmarkCourtSession(session)"
                    ></i>
                    <i
                      v-else
                      class="far fa-star"
                      @click="addBookmarkCourtSession(session)"
                      title="Додати в закладки"
                    ></i>
                  </div>
                </div>
              </div>
              <div v-else class="container-component">
                <p>За даними параметрами нічого не знайдено...</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="judge.common_statistic.competence !== 0 && judge.common_statistic.timeliness !== 0" class="mt-3">
        <div class="d-flex">
          <div class="card w-50 mt-2 mr-1">
            <div class="card-header">
              <span>Статистика розглянутих справ</span>
            </div>
            <div class="card-body p-0">
              <GChart type="PieChart" :data="commonChartData" :options="commonChartOptions"/>
            </div>
          </div>
          <div class="card w-50 mt-2 ml-1">
            <div class="card-header">Загальна ефективність</div>
            <div class="card-body">
              <div>
                <label>Рішень вистоюють у вищих інстанціях</label>
                <div class="progress">
                  <div
                    class="progress-bar"
                    role="progressbar"
                    :style="{ width: judge.common_statistic.competence + '%', backgroundColor: calculateColor(judge.common_statistic.competence) }"
                    aria-valuemin="0"
                    aria-valuemax="100"
                  >{{ judge.common_statistic.competence }}%</div>
                </div>
              </div>
              <div class="mt-5">
                <label>Справ розглядаються у визначений законом строк</label>
                <div class="progress">
                  <div
                    class="progress-bar"
                    role="progressbar"
                    :style="{ width: judge.common_statistic.timeliness + '%', backgroundColor: calculateColor(judge.common_statistic.timeliness) }"
                    aria-valuemin="0"
                    aria-valuemax="100"
                  >{{ judge.common_statistic.timeliness }}%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex">
          <div class="card w-50 mt-2 mr-1">
            <div class="card-header">Цивільне судочинство</div>
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <GChart type="ColumnChart" :data="civilChartData" :options="civilChartOptions"/>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-6 align-center-chart">
                  <doughnut-chart
                    :percent="judge.civil_statistic.cases_on_time"
                    :visibleValue="true"
                    emptyText="N/A"
                    :foregroundColor="calculateColor(judge.civil_statistic.cases_on_time)"
                    :width="gchart.width"
                    :height="gchart.width"
                  />
                  <span>Справ розглянуто своєчасно</span>
                </div>
                <div class="col-6 align-center-chart">
                  <doughnut-chart
                    :percent="judge.civil_statistic.approved_by_appeal"
                    :visibleValue="true"
                    emptyText="N/A"
                    :foregroundColor="calculateColor(judge.civil_statistic.approved_by_appeal)"
                    :width="gchart.width"
                    :height="gchart.width"
                  />
                  <span>Рішень вистояли у вищих інстанціях</span>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col">
                  <span>
                    <b>{{ judge.civil_statistic.average_duration }}</b> днів - середня тривалість розгляду однієї справи
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="card w-50 mt-2 ml-1">
            <div class="card-header">Кримінальне судочинство</div>
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <GChart
                    type="ColumnChart"
                    :data="criminalChartData"
                    :options="criminalChartOptions"
                  />
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-6 align-center-chart">
                  <doughnut-chart
                    :percent="judge.criminal_statistic.cases_on_time"
                    :visibleValue="true"
                    emptyText="N/A"
                    :foregroundColor="calculateColor(judge.criminal_statistic.cases_on_time)"
                    :width="gchart.width"
                    :height="gchart.width"
                  />
                  <span>Справ розглядаються менше 6 міс.</span>
                </div>
                <div class="col-6 align-center-chart">
                  <doughnut-chart
                    :percent="judge.criminal_statistic.approved_by_appeal"
                    :visibleValue="true"
                    emptyText="N/A"
                    :foregroundColor="calculateColor(judge.criminal_statistic.approved_by_appeal)"
                    :width="gchart.width"
                    :height="gchart.width"
                  />
                  <span>Рішень вистояли у вищих інстанціях</span>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-12">
                  <span>
                    <b>{{ judge.criminal_statistic.average_duration }}</b> днів - середня тривалість розгляду однієї справи
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex">
          <div class="card w-50 mt-2 mr-1">
            <div class="card-header">
              <span>Судочинство в порядку КУпАП</span>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <GChart
                    type="ColumnChart"
                    :data="adminoffenceChartData"
                    :options="adminoffenceChartOptions"
                  />
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-6 align-center-chart">
                  <doughnut-chart
                    :percent="judge.adminoffence_statistic.cases_on_time"
                    :visibleValue="true"
                    emptyText="N/A"
                    :foregroundColor="calculateColor(judge.adminoffence_statistic.cases_on_time)"
                    :width="gchart.width"
                    :height="120"
                  />
                  <span>Справ розглянуто своєчасно</span>
                </div>
                <div class="col-6 align-center-chart">
                  <doughnut-chart
                    :percent="judge.adminoffence_statistic.approved_by_appeal"
                    :visibleValue="true"
                    emptyText="N/A"
                    :foregroundColor="calculateColor(judge.adminoffence_statistic.approved_by_appeal)"
                    :width="gchart.width"
                    :height="120"
                  />
                  <span>Рішень вистояли у вищих інстанціях</span>
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-12">
                  <span>
                    <b>{{ judge.adminoffence_statistic.average_duration }}</b> днів - середня тривалість розгляду однієї справи
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="card w-50 mt-2 ml-1">
            <div class="card-header">Адміністративне судочинство</div>
            <div class="card-body">В розробці...</div>
          </div>
        </div>
      </div>
      
    </div>
     <div v-if="loadData && judge.common_statistic.competence === 0 && judge.common_statistic.timeliness === 0" class="mt-2 ml-2">
      <b>Даних для статистики недостатньо!</b>
    </div>
    <!--<GChart tupe="PieChart"/>-->
    <!-- modal change status -->
    <change-status v-if="showModal" :judgeData="judge.data" @closeModal="showModal = !showModal"/>
  </div>
</template>

<script>
import { GChart } from "vue-google-charts";
import DoughnutChart from "vue-doughnut-chart";
import _ from "lodash";
import MyUpload from "vue-image-crop-upload";

import StatusComponent from "../../shared/StatusComponent.vue";
import ChangeStatus from "../../shared/ChangeStatus.vue";
import Spinner from "../../shared/Spinner.vue";

export default {
  name: "JudgeProfile",
  components: {
    Spinner,
    GChart,
    DoughnutChart,
    StatusComponent,
    ChangeStatus,
    MyUpload
  },
  data() {
    return {
      showBtnAddPhoto: true,
      show: false,
      params: {
        judge_id: null,
        photo: null
      },
      headers: {
        // "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        Authorization: localStorage.getItem("token")
      },
      imgDataUrl: '', // the datebase64 url of created image
      showModal: false,
      loadData: false,
      judge: {},
      search: "",
      params: {
        status: 1
      },
      // Array will be automatically processed with visualization.arrayToDataTable function
      // налаштування для Google графіка загальної статистики
      commonChartData: [],
      commonChartOptions: {
        is3D: true,
        width: "100%",
        height: "100%",
        legend: {
          position: "left",
          alignment: "start"
        }
      },
      // налаштування для Google графіка статистики по цивільних справах
      civilChartData: [],
      civilChartOptions: {
        title:
          "співвідношення % задоволених/частково/відмовлених у позові справ",
        chartArea: {
          left: 40,
          top: 30,
          bottom: 30,
          width: "100%",
          height: "100%"
        },
        bar: {
          groupWidth: "65%"
        },
        legend: {
          position: "none"
        }
      },
      // налаштування для Google графіка статистики по кримінальних справах
      criminalChartData: [],
      criminalChartOptions: {
        title: "співвідношення % результатів розглянутих справ",
        chartArea: {
          left: 40,
          top: 30,
          bottom: 30,
          width: "100%",
          height: "100%"
        },
        bar: {
          groupWidth: "65%"
        },
        legend: {
          position: "none"
        }
      },

      // налаштування для Google графіка статистики по КУпАП
      adminoffenceChartData: [],
      adminoffenceChartOptions: {
        title: "співвідношення % результатів розглянутих справ",
        chartArea: {
          left: 40,
          top: 30,
          bottom: 30,
          width: "100%",
          height: "100%"
        },
        bar: {
          groupWidth: "65%"
        },
        legend: {
          position: "none"
        }
      },
      // розмір кругів
      gchart: {
        width: 135
      }
    };
  },
  computed: {
    filterSessions() {
      //  живий пошук = фільтр
      return _.filter(this.judge.court_sessions, el => {
        let arr = _.filter(Object.keys(el), key => {
          let regEx = new RegExp(`(${this.search})`, "i");
          return regEx.test(el[key]) || this.search.length == 0;
        });
        return arr.length > 0;
      });
    },
    isAuth() {
      return this.$store.getters.isAuth;
    }
  },
  created() {
    if (this.$store.getters.isAuth) {
      axios
        .get(`/api/v1/judges/${this.$route.params.id}`, {
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
        .then(response => {
          this.judge = response.data;
          this.loadData = true;
          this.setStatistic();
        })
        .catch(error => {
          if (error.response && error.response.status === 401) {
            this.$router.push("/login");
          }
          console.log("error");
        });
    } else {
      axios
        .get(`/api/v1/guest/judges/${this.$route.params.id}`, {
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest"
          }
        })
        .then(response => {
          this.judge = response.data;
          this.loadData = true;
          this.setStatistic();
        })
        .catch(error => {
          if (error.response && error.response.status === 401) {
            this.$router.push("/login");
          }
          console.log(error);
        });
    }
  },
  methods: {
    changeBookmarkStatus() {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }
      if (this.judge.data.is_bookmark === 0) {
        this.judge.data.is_bookmark = 1;

        axios({
          method: "put",
          url: `/api/v1/judges/${this.$route.params.id}/bookmark`,
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
          .catch(error => {
            this.judge.data.is_bookmark = 0;
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
              this.$toasted.error(
                "Для виконання цієї дії необхідно увійти в систему",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } else {
              this.$toasted.error(
                "Неможливо додати в закладки, перевірте Ваше інтернет з'єднання або спробуйте пізніше",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            }
          });

        // якщо вже є закладка - видаляємо
      } else {
        this.judge.data.is_bookmark = 0;
        axios({
          method: "delete",
          url: `/api/v1/judges/${this.$route.params.id}/bookmark`,
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
          .catch(error => {
            this.judge.data.is_bookmark = 1;
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
              this.$toasted.error(
                "Для виконання цієї дії необхідно увійти в систему",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } else {
              this.$toasted.error(
                "Перевірте Ваше інтернет з'єднання або спробуйте пізніше",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            }
          });
      }
    },
    changeLikes() {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }

      if (this.judge.data.is_unliked) {
        this.changeUnlikes();
      }
      if (this.judge.data.is_liked) {
        // delete like
        // спочатку міняємо view
        this.judge.data.likes -= 1;
        this.judge.data.is_liked = 0;

        axios({
          method: "delete",
          url: `/api/v1/judges/${this.$route.params.id}/like`,
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
          .catch(error => {
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
              this.$toasted.error(
                "Для виконання цієї дії необхідно увійти в систему",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } else {
              this.$toasted.error(
              "Перевірте Ваше інтернет з'єднання або спробуйте пізніше",
              {
                theme: "primary",
                position: "top-right",
                duration: 5000
              }
            );
            }
            // якщо поставити лайк на сервері не вдалось, повертаємо кількість назад
            this.judge.data.likes += 1;
            this.judge.data.is_liked = 1;
            
          });
      } else {
        // set like
        this.judge.data.likes += 1;
        this.judge.data.is_liked = 1;
        axios({
          method: "put",
          url: `/api/v1/judges/${this.$route.params.id}/like`,
          headers: {
            //"Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
          .catch(error => {
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
              this.$toasted.error(
                "Для виконання цієї дії необхідно увійти в систему",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } else {
              this.$toasted.error(
              "Перевірте Ваше інтернет з'єднання або спробуйте пізніше",
              {
                theme: "primary",
                position: "top-right",
                duration: 5000
              }
            );
            }
            // якщо поставити лайк на сервері не вдалось, повертаємо кількість назад
            this.judge.data.likes -= 1;
            this.judge.data.is_liked = 0;
          });
      }
    },
    changeUnlikes() {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      }
      if (this.judge.data.is_liked) {
        this.changeLikes();
      }
      if (this.judge.data.is_unliked) {
        // dell unlike
        this.judge.data.unlikes -= 1;
        this.judge.data.is_unliked = 0;
        axios({
          method: "delete",
          url: `/api/v1/judges/${this.$route.params.id}/unlike`,
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
          .catch(error => {
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
              this.$toasted.error(
                "Для виконання цієї дії необхідно увійти в систему",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } else {
              this.$toasted.error(
                "Перевірте Ваше інтернет з'єднання або спробуйте пізніше",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            }
            // якщо поставити лайк на сервері не вдалось, повертаємо кількість назад
            this.judge.data.unlikes += 1;
            this.judge.data.is_unliked = 1;
          });
      } else {
        // set unlike
        this.judge.data.unlikes += 1;
        this.judge.data.is_unliked = 1;
        axios({
          method: "put",
          url: `/api/v1/judges/${this.$route.params.id}/unlike`,
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
          .catch(error => {
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
              this.$toasted.error(
                "Для виконання цієї дії необхідно увійти в систему",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } else {
              this.$toasted.error(
                "Перевірте Ваше інтернет з'єднання або спробуйте пізніше",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            }
            // якщо поставити лайк на сервері не вдалось, повертаємо кількість назад
            this.judge.data.unlikes -= 1;
            this.judge.data.is_unliked = 0;
            
          });
      }
    },
    deleteBookmarkCourtSession(session) {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      } else {
        session.is_bookmark = 0;
        axios({
          method: "delete",
          url: `/api/v1/court-sessions/${session.id}/bookmark`,
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        }).catch(error => {
            session.is_bookmark = 1;
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
              this.$toasted.error(
                "Для виконання цієї дії необхідно увійти в систему",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } else {
              this.$toasted.error(
                "Перевірте Ваше інтернет з'єднання або спробуйте пізніше",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } 
          });
      }
    },
    addBookmarkCourtSession(session) {
      if (!this.$store.getters.isAuth) {
        this.$router.push("/login");
      } else {
        session.is_bookmark = 1;
        axios({
          method: "put",
          url: `/api/v1/court-sessions/${session.id}/bookmark`,
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
          .then(response => {
            //
          })
          .catch(error => {
            session.is_bookmark = 0;
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
              this.$toasted.error(
                "Для виконання цієї дії необхідно увійти в систему",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            } else {
              this.$toasted.error(
                "Неможливо додати в закладки, перевірте Ваше інтернет з'єднання або спробуйте пізніше",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 5000
                }
              );
            }
          });
      }
    },
    calculateColor(val) {
      let red = 205 - val * 2;
      let green = 5 + val * 2;

      if (val >= 50 && val < 70) {
        red += 90;
        green += 60;
      } else if (val >= 50 && val < 80) {
        green += 60;
      } else if (val < 50) {
        red += val;
        green -= val;
      }

      return "rgb(" + red + " " + green + " 0)";
    },
    setStatistic() {
      this.commonChartData = [
        ["Категорія", "Кількість справ"],
        ["Цивільні", parseInt(this.judge.civil_statistic.amount)],
        ["Кримінальні", parseInt(this.judge.criminal_statistic.amount)],
        [
          "Справи про адмін. правопорушення",
          parseInt(this.judge.adminoffence_statistic.amount)
        ],
        ["Адміністративні справи", parseInt(this.judge.admin_statistic.amount)],
        [
          "Господарські справи",
          parseInt(this.judge.commercial_statistic.amount)
        ]
      ];
      this.civilChartData = [
        ["Element", "відсотків", { role: "style" }],
        [
          "у позові відмовлено повністю",
          this.judge.civil_statistic.negative_judgment,
          "red"
        ],
        [
          "позов задоволено повністю",
          this.judge.civil_statistic.positive_judgment,
          "green"
        ],
        [
          "задоволено частково, укладено мирову угоду",
          this.judge.civil_statistic.other_judgment,
          "gold"
        ]
      ];
      this.criminalChartData = [
        ["Element", "відсотків", { role: "style" }],
        [
          "особу притягнено до кримінальної відповідальності",
          this.judge.criminal_statistic.negative_judgment,
          "red"
        ],
        [
          "особа звільнена від кримінальної відповідальності",
          this.judge.criminal_statistic.positive_judgment,
          "green"
        ]
      ];
      this.adminoffenceChartData = [
        ["Element", "відсотків", { role: "style" }],
        [
          "особу притягнено до адміністративної відповідальності",
          this.judge.adminoffence_statistic.negative_judgment,
          "red"
        ],
        [
          "особа звільнена від адміністративної відповідальності",
          this.judge.adminoffence_statistic.positive_judgment,
          "green"
        ]
      ];
    },
    /* ---------  upload photo ----------- */
    toggleShow() {
      this.show = !this.show;
    },
    srcFileSet(fileName, fileType, fileSize) {
      // if (fileSize > (1024 * 1024)) {
      //     this.$toasted.error('Розмір файла має бути не більше 1Мб!', {
      //       theme: "primary",
      //       position: "top-right",
      //       duration: 8000
      //     });
      //   this.show = !this.show;
      // }     
    },
    cropSuccess(imgDataUrl, field){
      this.params.judge_id = this.judge.data.id;
      this.params.photo = imgDataUrl;
      setTimeout(() => {
         this.judge.data.photo = imgDataUrl;
      }, 5000);
     
    },
    cropUploadSuccess(jsonData, field){
      this.$toasted.success('Фото успішно додано!', {
        theme: "primary",
        position: "top-center",
        duration: 3000
      });
      setTimeout(() => {
        this.show = !this.show;
      }, 3000);
    },
    cropUploadFail(status, field){
      console.log('upload fail', status);
    }
},
};
</script>

<style lang="scss">
.vicp-wrap {
  width: 430px !important;
}
.vicp-preview {
  display: none;
}
.vicp-crop {
  display: flex;
  justify-content: center;
}
.doughnut_chart > div {
    bottom: 52.5px !important;
    left: 108.5px !important;
}
</style>

<style scoped lang="scss">
@import "../../../../sass/_variables.scss";
@import "../../../../sass/_mixins.scss";

.judge-profile {
  .card-body {
    font-size: 0.9rem;
  }
  .align-center-chart {
    display: flex;
    justify-content: center;
    flex-direction: column;
    text-align: center;
  }
  .progress {
    height: 20px;
    font-size: 1.1rem;
  }
  .main-info {
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    .fa-university {
      color: $primary;
      font-size: 1.3rem;
      margin-right: 5px;
    }
    .court-name {
      color: $text-muted;
      font-weight: 300;
    }
    .detail-info {
      @include alignElement($justifyContent: start, $alignItems: start);
      color: $text-muted;
      i[class^="fa"] {
        margin-right: 10px;
        padding-top: 5px;
      }
    }
  }
  .status-info {
    .rating {
      @include alignElement($justifyContent: space-between);
      padding-left: 20%;
      padding-right: 20%;
      .like {
        color: green;
        font-size: 1.1rem;
        cursor: pointer;
      }
      .dislike {
        color: red;
        font-size: 1.1rem;
        cursor: pointer;
      }
    }
    .status {
      @include alignElement($alignItems: start);
    }
    .bookmark {
      > span {
        width: 20px;
        float: right;
      }
    }
  }
  .court-sessions-container {
    max-height: 600px;
    overflow-y: scroll;
    margin: 20px;
    .court-sessions {
      width: 100%;
      height: auto;
      font-size: 0.9rem;
      .fa-star {
        color: $main-color;
        cursor: pointer;
        font-size: 20px;
      }
      .container-component {
        background-color: #ffffff;
      }
      .header {
        align-items: center;
        font-weight: 700;
        line-height: 1.3;
      }
      .row {
        margin: 0;
        padding: 15px 0;
        &:not(:last-child) {
          border-bottom: 1px solid lightgrey;
        }
        div[class^="col"] {
          padding-right: 0;
        }
      }
    }
  }
  input[type="search"] {
    width: 200px;
  }
  .btn-add-photo {
    border: 1px solid gray;
    border-radius: 4px;
    font-size: 12px;
    padding: 3px 10px;
  }
  .center {
    @include alignElement();
  }
}
</style>