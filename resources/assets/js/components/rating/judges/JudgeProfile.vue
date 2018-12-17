<template>
    <div class="judge-profile">
        <spinner v-if="!loadData" />
        <div v-if="loadData" class="container content-wrapper">
            <div class="judge-info">
                <div class="card">
                    <div class="card-body d-flex">
                        <div class="photo w-25">
                            <img :src="judge.data.photo" alt="avatar" class="w-100">
                        </div>
                        <div class="w-75 px-3">
                            <div class="main-info pb-2">
                                <h3>{{ judge.data.surname + ' ' + judge.data.name + ' ' + judge.data.patronymic }}</h3>
                                <div class="d-flex">
                                    <i class="fa fa-university" aria-hidden="true"></i>
                                    <h5 class="court-name"> {{ judge.data.court_name }}</h5>
                                </div>
                                <div class="detail-info mt-2" v-if="judge.data.court_address">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ judge.data.court_address }}</span>
                                </div>
                                <!-- previous works -->
                                <div class="detail-info" v-for="(prevWork, ind_1) in judge.previous_works" :key="ind_1 + 'H'" v-if="ind_1 < 3">
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
                                        <status-component :judgeData="judge.data" />
                                        <span><i class="fas fa-edit float-right pl-3" aria-hidden="true" @click="showModal = true"></i></span>
                                    </div>
                                    <div class="bookmark w-50">
                                        <span v-if="judge.data.is_bookmark" @click="changeBookmarkStatus()"><i class="fa fa-bookmark" aria-hidden="true"></i></span>
                                        <span v-if="!judge.data.is_bookmark" @click="changeBookmarkStatus()"><i class="fa fa-bookmark-o" aria-hidden="true"></i></span>
                                    </div>
                                </div>
                                <div class="rating">
                                    <span class="like" @click="changeLikes"><i class="fas fa-thumbs-up"></i> {{ judge.data.likes }}</span>
                                    <span class="line-chart"><i class="fa fa-line-chart" aria-hidden="true"> {{ judge.data.rating + '%' }}</i></span>
                                    <span class="dislike" @click="changeUnlikes"><i class="fas fa-thumbs-down"></i> {{ judge.data.unlikes }}</span>
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
                                    <div class="col-1 ">{{ session.number }}</div>
                                    <div class="col-2">{{ session.judges }}</div>
                                    <div class="col-2">{{ session.forma }}</div>
                                    <div class="col-3">{{ session.involved }}</div>
                                    <div class="col-2">{{ session.description }}</div>
                                    <div class="col-1 pr-0 text-center" v-if="isAuth">
                                        <i v-if="session.is_bookmark" class="fas fa-star" @click="delBookmarkCourtSession()"></i>
                                        <i v-else class="far fa-star" @click="addBookmarkCourtSession()"></i>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="container-component">
                                <p>Нічого не знайдено...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex">
                <div class="card w-50 mt-2 mr-1">
                    <div class="card-header">
                        <span>Статистика розгрянутих справ</span>
                    </div>
                    <div class="card-body">
                        <!-- <GChart type="PieChart" :data="pieChartData" :options="pieChartOptions" /> -->
                    </div>
                </div>
                <div class="card w-50 mt-2 ml-1">
                    <div class="card-header">
                        Загальна ефективність
                    </div>
                    <div class="card-body">
                        <div>
                            <label for="">Компетентність</label>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label for="">Своєчасність</label>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex">
                <div class="card w-50 mt-2 mr-1">
                    <div class="card-header">
                        Цивільне судочинство
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <!-- <GChart type="ColumnChart" :data="columnChartData" :options="columnChartOptions" /> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <doughnut-chart :percent="37" :visibleValue="true" foregroundColor="#8fdb42" :width="gchart.width" :height="gchart.width" />
                                <span>Справ розглянуто своєчасно</span>
                            </div>
                            <div class="col-6">
                                <doughnut-chart :percent="65" :visibleValue="true" foregroundColor="#cebd4b" :width="gchart.width" :height="gchart.width" />
                                <span>Рішень вистояли у вищих інстанціях</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card w-50 mt-2 ml-1">
                    <div class="card-header">
                        Кримінальне судочинство
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <!-- <GChart type="ColumnChart" :data="columnChartData" :options="columnChartOptions" /> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <doughnut-chart :percent="37" :visibleValue="true" foregroundColor="#8fdb42" :width="gchart.width" :height="gchart.width" />
                                <span>Справ розглянуто своєчасно</span>
                            </div>
                            <div class="col-6">
                                <doughnut-chart :percent="65" :visibleValue="true" foregroundColor="#cebd4b" :width="gchart.width" :height="gchart.width" />
                                <span>Рішень вистояли у вищих інстанціях</span>
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
                                <!-- <GChart type="ColumnChart" :data="columnChartData" :options="columnChartOptions" /> -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <doughnut-chart :percent="37" :visibleValue="true" foregroundColor="#8fdb42" :width="gchart.width" :height="120" />
                                <span>Справ розглянуто своєчасно</span>
                            </div>
                            <div class="col-6">
                                <doughnut-chart :percent="65" :visibleValue="true" foregroundColor="#cebd4b" :width="gchart.width" :height="120" />
                                <span>Рішень вистояли у вищих інстанціях</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card w-50 mt-2 ml-1">
                    <div class="card-header">
                        Адміністративне судочинство
                    </div>
                    <div class="card-body">
                        В розробці...
                    </div>
                </div>
             </div>
        </div>
        <GChart tupe="PieChart"/>
        <!-- modal change status -->
        <change-status v-if="showModal" :judgeData="judge.data" @closeModal="showModal = !showModal"  />
    </div>
</template>

<script>
    import { GChart } from "vue-google-charts";
    import DoughnutChart from 'vue-doughnut-chart'
    import _ from 'lodash';

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
            ChangeStatus
        },
        data() {
            return {
                showModal: false,
                loadData: false,
                judge: {},
                search: '',
                params: {
                    status: 1,
                },
                // Array will be automatically processed with visualization.arrayToDataTable function
                pieChartData: [
                    ['Task', 'Hours per Day'],
                    ['Work', 11],
                    ['Eat', 2],
                    ['Commute', 2],
                    ['Watch TV', 2],
                    ['Sleep', 7]
                ],
                pieChartOptions: {
                    is3D: true,
                    width: 400,
                    height: 300,
                    legend: {
                        position: 'left',
                        alignment: 'start',
                    },
                },
                columnChartData: [
                    ['Element', 'Density', {
                        role: 'style'
                    }, {
                        role: 'annotation'
                    }],
                    ['Copper', 8.94, '#b87333', '400'], // RGB value
                    ['Silver', 10.49, 'silver', '500'], // English color name
                    ['Gold', 19.30, 'gold', '200'],
                    ['Platinum', 21.45, 'color: #e5e4e2', '800'],
                ],
                columnChartOptions: {
                    legend: {
                        position: "none"
                    },
                },
                gchart: {
                    width: 120
                }
    
            };
        },
        computed: {
            filterSessions() {
                //  живий пошук = фільтер
                return _.filter(this.judge.court_sessions, (el) => {
                    let arr = _.filter(Object.keys(el), (key) => {
                        let regEx = new RegExp(`(${this.search})`, 'i');
                        return (regEx.test(el[key]) || this.search.length == 0)
                    })
                    return (arr.length > 0) ? true : false
                })
            },
            isAuth: () => {
                return localStorage.getItem("token");
            }
        },  
        beforeMount() {
            if (this.isAuth) { 
                axios
                    .get(`/api/v1/judges/${this.$route.params.id}`, {
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "Authorization": localStorage.getItem('token')
                        },
                    })
                    .then(response => {
                        this.judge = response.data;
                        this.loadData = true;                        
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push('/login');
                        }
                        console.log('error');
                    });
            } else {
                axios
                    .get(`/api/v1/guest/judges/${this.$route.params.id}`, {
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                    })
                    .then(response => {
                        this.judge = response.data;
                        this.loadData = true;
                        
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push('/login');
                        }
                        console.log('error');
                    });

            }
        },
        methods: {
            changeBookmarkStatus() {
                if (!this.isAuth) {
                    this.$router.push("/login");
                }
                if (this.judge.data.is_bookmark === 0) {
                    axios({
                        method: "put",
                        url: `/api/v1/judges/${this.$route.params.id}/bookmark`,
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            Authorization: localStorage.getItem("token")
                        }
                    })
                    .then(response => {
                        this.judge.data.is_bookmark = 1;
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push("/login");
                        }
                        console.log("Bookmark", error);
                    });
                } else {
                    axios({
                        method: "delete",
                        url: `/api/v1/judges/${this.$route.params.id}/bookmark`,
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            Authorization: localStorage.getItem("token")
                        }
                    })
                    .then(response => {
                        this.judge.data.is_bookmark = 0;
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push("/login");
                        }
                        console.log("Bookmark", error.response);
                    });
                }
            },
            changeLikes() {
                if (!this.isAuth) {
                    this.$router.push("/login");
                }
                if (this.judge.data.is_unliked) {
                    this.changeUnlikes();
                }
                if (this.judge.data.is_liked) {
                    // dell like
                    axios({
                            method: "delete",
                            url: `/api/v1/judges/${this.$route.params.id}/like`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                Authorization: localStorage.getItem("token")
                            }
                        })
                        .then(response => {
                            this.judge.data.likes -= 1;
                            this.judge.data.is_liked = 0;
                        })
                        .catch(error => {
                            if (error.response.status === 401) {
                                this.$router.push("/login");
                            }
                            console.log("set Likes", error);
                        });
                } else {
                    // set like
                    axios({
                            method: "put",
                            url: `/api/v1/judges/${this.$route.params.id}/like`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                Authorization: localStorage.getItem("token")
                            }
                        })
                        .then(response => {
                            this.judge.data.likes += 1;
                            this.judge.data.is_liked = 1;
                        })
                        .catch(error => {
                            if (error.response.status === 401) {
                                this.$router.push("/login");
                            }
                            console.log("set Likes", error);
                        });
    
                }
    
            },
            changeUnlikes() {
                if (!this.isAuth) {
                    this.$router.push("/login");
                }
                if (this.judge.data.is_liked) {
                    this.changeLikes();
                }
                if (this.judge.data.is_unliked) {
                    // dell unlike
                    axios({
                            method: "delete",
                            url: `/api/v1/judges/${this.$route.params.id}/unlike`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                Authorization: localStorage.getItem("token")
                            }
                        })
                        .then(response => {
                            this.judge.data.unlikes -= 1;
                            this.judge.data.is_unliked = 0;
                        })
                        .catch(error => {
                            if (error.response.status === 401) {
                                this.$router.push("/login");
                            }
                            console.log("set Likes", error);
                        });
                } else {
                    // set unlike
                    axios({
                            method: "put",
                            url: `/api/v1/judges/${this.$route.params.id}/unlike`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                Authorization: localStorage.getItem("token")
                            }
                        })
                        .then(response => {
                            this.judge.data.unlikes += 1;
                            this.judge.data.is_unliked = 1;
                        })
                        .catch(error => {
                            if (error.response.status === 401) {
                                this.$router.push("/login");
                            }
                            console.log("set Likes", error);
                        });
                }
            },
            delBookmarkCourtSession() {
                if (!this.isAuth) {
                    this.$router.push("/login");
                }
                if (this.judge.data.is_bookmark === 0) {
                    axios({
                        method: "put",
                        url: `/api/v1/judges/${this.$route.params.id}/bookmark`,
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            Authorization: localStorage.getItem("token")
                        }
                    })
                    .then(response => {
                        this.judge.data.is_bookmark = 1;
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push("/login");
                        }
                        console.log("Bookmark", error);
                    });
                } else {
                    axios({
                        method: "delete",
                        url: `/api/v1/judges/${this.$route.params.id}/bookmark`,
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            Authorization: localStorage.getItem("token")
                        }
                    })
                    .then(response => {
                        this.judge.data.is_bookmark = 0;
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push("/login");
                        }
                        console.log("Bookmark", error.response);
                    });
                }
            },
            addBookmarkCourtSession() {
                 
            }
        }
    };
</script>

<style scoped lang="scss">
    @import "../../../../sass/_variables.scss";
    @import "../../../../sass/_mixins.scss";
    .judge-profile {
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
                .like {
                    color: green;
                }
                .dislike {
                    color: red;
                }
            }
            .status {
                @include alignElement($alignItems: start);
            }
            .bookmark {
                >span {
                    width: 20px;
                    float: right;
                }
            }
        }
        .court-sessions-container {
            max-height: 600px;
            overflow-y: auto;
            .court-sessions {
                width: 100%;
                height: auto;
                font-size: 0.7rem;
                .fa-star {
                    color: $main-color;
                }
                .container-component {
                    background-color: #ffffff;
                }
                .header {
                    align-items: center;
                    font-weight: 700;
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
    }
</style>