<template>
    <div>
        <div class="card-body">
            <div v-if="!this.judgesList || this.judgesList.length == 0">За заданими параметрами нічого не знайдено</div>
            <div v-if="this.judgesList && this.judgesList.length > 0">
                <div class="judge-component row py-3 mx-1" v-for="(judge, index) of judgesList" :key="index">
                    <div class="col-9 d-flex pl-0 main-info">
                        <div class="mr-3"><img class="avatar" :src="judge.photo" alt="фото" /></div>
                        <div>
                            <h5>
                                <router-link :to="`/judges/${judge.id}`"> {{ judge.surname }} {{ (judge.name.length != 1) ? judge.name : judge.name + '.' }} {{ judge.patronymic.length != 1 ? judge.patronymic : judge.patronymic + '.' }} </router-link>
                            </h5>
                            <div class="court_name">{{ judge.court_name }}</div>
                        </div>
                    </div>
                    <div class="col-3 pl-0 additional-info">
                        <div class="align-center pb-3">
                            <div class="w-75">
                                <span class="float-left">
                                    <i class="fa fa-line-chart float-right" aria-hidden="true"> {{ judge.rating }} </i>
                                </span>
                            </div>
                            <div class="w-25 bookmark">
                                <span v-if="judge.is_bookmark" @click="changeBookmarkStatus(judge)"><i class="fa fa-bookmark" aria-hidden="true"></i></span>
                                <span v-if="!judge.is_bookmark" @click="changeBookmarkStatus(judge)"><i class="fa fa-bookmark-o" aria-hidden="true"></i></span>
                            </div>
                        </div>
                        <div class="align-center">
                            <div class="w-75">
                                <!-- status-component -->
                                <status-component :judgeData="judge" />         
                            </div>
                            <div class="w-25"><i class="fa fa-edit p-1 float-right" aria-hidden="true" @click="showModal(judge)"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal change status -->
        <change-status v-if="isModalVisible" :judgeData="currentJudge" @closeModal="isModalVisible = !isModalVisible"  />
    </div>
</template>

<script>
    import _ from 'lodash';
    import StatusComponent from "../../shared/StatusComponent.vue";
    import ChangeStatus from "../../shared/ChangeStatus.vue";

    
    export default {
        name: "JudgeComponent",
        props: {
            judgesList: Array
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
        methods: {
            formattingDate(date) {
                if (date === '' || date === null) {
                    return '';
                } else {
                    let arr = _.split(date, '.');
                    return `${arr[2]}-${arr[1]}-${arr[0]}`;
                }
            },
            changeBookmarkStatus(judge) {
                if (!this.$store.getters.isAuth) {
                    this.$router.push("/login");
                }
                if (judge.is_bookmark === 0) {
                    axios({
                            method: "put",
                            url: `/api/v1/judges/${judge.id}/bookmark`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                Authorization: localStorage.getItem("token")
                            }
                        })
                        .then(response => {
                            judge.is_bookmark = 1;
                        })
                        .catch(error => {
                            if (error.response.status === 401) {
                                this.$router.push('/login');
                            }
                            console.log("Bookmark", error);
                        });
                } else {
                    axios({
                        method: "delete",
                        url: `/api/v1/judges/${judge.id}/bookmark`,
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            Authorization: localStorage.getItem("token")
                        }
                    })
                    .then(response => {
                        judge.is_bookmark = 0;
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push('/login');
                        }
                        console.log('Bookmark', error);
                    });
                }
            },
            showModal(judge) {
                if (!this.$store.getters.isAuth) {
                    this.$router.push('/login');
                }
                this.currentJudge = judge;
                this.isModalVisible = true;
            },
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
        .bookmark>span {
            float: right;
            padding-right: 7px;
        }
        .bookmark>span,
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
        a {
            color: $primary;
        }
        .court_name {
            font-size: 1rem;
        }
    }
</style>