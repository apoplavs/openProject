<template>
    <div>
        <div class="card-body px-4 py-2">
            <div v-if="!this.judgesList || this.judgesList.length == 0">За заданими параметрами нічого не знайдено</div>
            <div v-if="this.judgesList && this.judgesList.length > 0">
                <div class="judge-component row py-3 mx-1" v-for="(judge, index) of this.judgesList" :key="index">
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
                                <span v-if="judge.status === 1"> <!-- Cуддя на роботі  -->
                                    <i class="fa fa-briefcase" aria-hidden="true"></i> на роботі 
                                    {{ judge.due_date_status ? '('+judge.due_date_status+')' : null }}
                                </span>
                                <span v-if="judge.status === 2"> <!-- На лікарняному  -->
                                    <i class="fa fa-medkit" aria-hidden="true"></i> на лікарняному 
                                    {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }}
                                </span>
                                <span v-if="judge.status === 3"> <!-- У відпустці   -->
                                    <i class="fas fa-umbrella-beach"></i> у відпустці 
                                    {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }}
                                </span>
                                <span v-if="judge.status === 4"> <!-- Відсуній на робочому місці з інших причин  --> 
                                    <i class="fa fa-calendar-minus-o" aria-hidden="true"></i>
                                    відсутній на робочому місці з інших причин 
                                    {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }}
                                </span>
                                <span v-if="judge.status === 5"> <!-- Припинено повноваження  -->
                                    <i class="fa fa-calendar-times-o" aria-hidden="true"></i> припинено повноваження 
                                    {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }}
                                </span>
                            </div>
                            <div class="w-25"><i class="fas fa-edit p-1 float-right" aria-hidden="true" @click="showModal(judge)"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL change status темплейт передаємо через слоти -->
        <modal v-show="isModalVisible" @close="closeModal" @save="saveChanges">
            <h4 slot="header">Оновити статус судді</h4>
            <div slot="body">
                <form>
                    <div class="form-group row mx-0 my-4">
                        <label for="chooser-judge-status" class="col-4">Статус</label>
                        <div class="col-8">
                            <select class="form-control" id="chooser-judge-status" v-model="judgeStatus.set_status" value="judgeStatus.set_status">
                                <option value="1">на роботі</option>
                                <option value="2">на лікарняному</option>
                                <option value="3">у відпустці</option>
                                <option value="4">відсутній на робочому місці</option>
                                <option value="5">припинено повноваження</option>
                            </select>
                        </div>
                        <input type="hidden" id="judge-for-new-status" value="0">
                    </div>
                    <div class="form-group row mx-0 my-4">
                        <label for="status-end-date" class="col-7">Дата завершення дії статусу <br><sup class="text-muted">(якщо відома)</sup></label>
                        <div class="col-5">
                            <datepicker v-model="judgeStatus.due_date" :value="judgeStatus.due_date" language="uk" :min="calendar.startDate | formatDate" :max="calendar.endDate | formatDate">
                            </datepicker>
                        </div>
                    </div>
                </form>
            </div>
    
        </modal>
    </div>
</template>

<script>
    import Modal from "../../shared/Modal.vue";
    import Datepicker from "vue-date";
    import _ from 'lodash';
    
    export default {
        name: "judge-component",
        props: ["judgesList"],
        data() {
            return {
                isModalVisible: false,
                changeStatusId: null,
                judgeStatus: {
                    set_status: null,
                    due_date: null
                },
                calendar: {
                    startDate: new Date(),
                    endDate: new Date()
                },
                format: 'YYYY',
                isAuth: localStorage.getItem("token"),
            };
        },
        // computed: {
        //     dueDate: function() {
        //         return judge.due_date_status ? `до (${judge.due_date_status})` : null;
        //     }
        // }, 
        filters: {
            formatDate(date) {
                // getMobth() чомусь рахує місяці з 0 date.getMonth() + 1 //костиль
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
                console.log(this.isAuth); 
                if (!this.isAuth) {
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
                            console.log('Bookmark', error.response);
                        });
                }
            },
            showModal(judge) {
                if (!this.isAuth) {
                    this.$router.push("/login");
                }
                this.changeStatusId = judge.id;
                this.judgeStatus.set_status = judge.status;
                this.judgeStatus.due_date = this.formattingDate(judge.due_date_status);

                this.calendar.startDate = new Date();
                this.calendar.endDate = new Date(
                    this.calendar.startDate.getFullYear(),
                    this.calendar.startDate.getMonth() + 1,
                    this.calendar.startDate.getDate()
                );
                this.isModalVisible = true;
            },
            closeModal() {
                this.isModalVisible = false;
            },
            saveChanges() {
                if (this.judgeStatus.set_status === "1" || this.judgeStatus.set_status === "5") {
                    this.judgeStatus.due_date = null;
                }
                console.log("STATUS", this.judgeStatus);
                axios({
                        method: "put",
                        url: `/api/v1/judges/${this.changeStatusId}/update-status`,
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            Authorization: localStorage.getItem("token")
                        },
                        data: this.judgeStatus
                    })
                    .then(response => {
                        this.judgesList.forEach(e => { 
                            e.id === this.changeStatusId ? e.status = this.judgeStatus.set_status : null;
                        })
                        this.isModalVisible = false;
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push('/login');
                        }
                        console.log("Status", error);
                    });
            }
        },
        components: {
            Modal,
            Datepicker,
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
        .fa, .fas {
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
            @include alignElement($alignItems: start);
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