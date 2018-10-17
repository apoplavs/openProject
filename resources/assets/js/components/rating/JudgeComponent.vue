<template>
    <div>
        <div class="card-body px-4 py-2">
            <div v-if="!this.judgesList || this.judgesList.length == 0">За заданими параметрами нічого не знайдено</div>
            <div v-if="this.judgesList && this.judgesList.length > 0">
                <div class="judge-component row py-3 " v-for="(judge, index) of this.judgesList" :key="index">
                    <div class="col-9 d-flex pl-0 main-info">
                        <div class="mr-3"><img class="avatar" :src="judge.photo" alt="фото" /></div>
                        <div>
                            <h5>
                                <router-link to="/"> {{ judge.surname }} {{ (judge.name.length != 1) ? judge.name : judge.name + '.' }} {{ judge.patronymic.length != 1 ? judge.patronymic : judge.patronymic + '.' }} </router-link>
                            </h5>
                            <div class="court_name">{{ judge.court_name }}</div>
                        </div>
                    </div>
                    <div class="col-3 pl-0 additional-info">
                        <div class="d-flex pb-2">
                            <div class="w-75">
                                <span v-if="isAuth">
                                            <span v-if="judge.is_bookmark" @click="changeBookmarkStatus(judge)">відстежується <i class="fa fa-bookmark" aria-hidden="true"></i></span>
                                <span v-if="!judge.is_bookmark" @click="changeBookmarkStatus(judge)">відстежувати <i class="fa fa-bookmark-o" aria-hidden="true"></i></span>
                                </span>
                            </div>
                            <div class="w-25"><i class="fa fa-line-chart float-right" aria-hidden="true"> {{ judge.rating }} </i></div>
                        </div>
                        <div class="d-flex">
                            <div class="w-75">
                                <span v-if="judge.status === 1"> <!-- Cуддя на роботі  -->
                                            <i class="fa fa-briefcase" aria-hidden="true"></i>
                                                на роботі {{ judge.due_date_status ? '('+judge.due_date_status+')' : null }}
                                        </span>
                                <span v-if="judge.status === 2"> <!-- На лікарняному  -->
                                            <i class="fa fa-medkit" aria-hidden="true"></i>
                                                на лікарняному {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }}
                                        </span>
                                <span v-if="judge.status === 3"> <!-- У відпустці   -->
                                            <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                у відпустці {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }}
                                        </span>
                                <span v-if="judge.status === 4"> <!-- Відсуній на робочому місці з інших причин  -->
                                            <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                відсуній на робочому місці з інших причин {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }}
                                        </span>
                                <span v-if="judge.status === 5"> <!-- Припинено повноваження  -->
                                            <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                                припинено повноваження {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }}
                                        </span>
                            </div>
                            <div class="w-25"><i v-if="isAuth" class="fa fa-pencil p-1 float-right" aria-hidden="true" @click="showModal(judge.id)"></i></div>
    
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
                        <lable for="chooser-judge-status" class="col-4">Статус</lable>
                        <div class="col-8">
                            <select class="form-control" id="chooser-judge-status" v-model="status.set_status">
                                    <option value="1">на роботі</option>
                                    <option value="2">на лікарняному</option>
                                    <option value="3">у відпустці</option>
                                    <option value="4">відсутній на робочому місці</option>
                                    <option value="5">припиено повноваження</option>
                                </select>
                        </div>
                        <input type="hidden" id="judge-for-new-status" value="0">
                    </div>
                    <div class="form-group row mx-0 my-4">
                        <label for="status-end-date" class="col-7">Дата завершення дії статусу <br><sup class="text-muted">(якщо відома)</sup></label>
                        <div class="col-5">
                            <input v-model="status.due_date" class=" form-control" type="date" id="status-end-date">
                        </div>
                    </div>
                </form>
            </div>
    
        </modal>
    </div>
</template>

<script>
    import Modal from '../shared/Modal.vue';
    
    export default {
        name: "judge-component",
        data() {
            return {
                isModalVisible: false,
                changeStatusId: null,
                status: {
                    set_status: 0,
                    due_date: 0
                },
                isAuth: localStorage.getItem('token'),
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "Authorization": localStorage.getItem('token')
                }
            }
        },
        props: ['judgesList'],
        methods: {
            changeBookmarkStatus(judge) {
                if (judge.is_bookmark === 0) {
                    axios({
                            method: 'put',
                            url: `/api/v1/judges/${judge.id}/bookmark`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                "Authorization": localStorage.getItem('token')
                            }
                        })
                        .then(response => {
                            judge.is_bookmark = 1;
                        })
                        .catch(error => {
                            console.log('Bookmark', error);
                        });
                } else {
                    axios({
                            method: 'delete',
                            url: `/api/v1/judges/${judge.id}/bookmark`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                "Authorization": localStorage.getItem('token')
                            }
                        })
                        .then(response => {
                            judge.is_bookmark = 0;
                            // console.log(response);
                        })
                        .catch(error => {
                            console.log(error);
                        });
                }
            },
            showModal(judgeId) {
                this.changeStatusId = judgeId;
                this.isModalVisible = true;
                console.log('showModal', this.isModalVisible);
            },
            closeModal() {
                this.isModalVisible = false;
                console.log('closeModal');
            },
            saveChanges() {
                axios({
                        method: 'put',
                        url: `/judges/${changeStatusId}/update-status`,
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "Authorization": localStorage.getItem('token')
                        },
                        params: {
                            status: this.params.search
                        }
                    })
                    .then(response => {
                        console.log('Save Status', response);
    
                        this.isModalVisible = false;
                    })
                    .catch(error => {
                        console.log('Bookmark', error);
                    });
    
            }
        },
        components: {
            Modal
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../sass/_variables.scss";
    .judge-component:not(:last-child) {
        border-bottom: 1px solid lightgray;
    }
    
    .additional-info {
        font-size: .8rem;
        color: grey;
        .fa {
            font-size: 1rem;
        }
        .bookmark,
        .fa-pencil {
            cursor: pointer;
        }
        /* styles for font awesome */
        .fa-bookmark-o,
        .fa-bookmark {
            color: #2b989b;
        }
        .fa-briefcase {
            color: green;
        }
        .fa-line-chart {
            color: #6291ba;
        }
        .fa-medkit {
            color: red;
        }
        .fa-calendar-check-o,
        .fa-calendar-minus-o,
        .fa-calendar-times-o {
            color: #2b989b;
        }
        .fa-pencil {
            color: #6c757d;
            -webkit-transition-duration: 0.3s;
            -o-transition-duration: 0.3s;
            -moz-transition-duration: 0.3s;
            transition-duration: 0.3s;
        }
        .fa-pencil:hover {
            color: #6291ba;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.5), inset 0 0 1px rgba(0, 0, 0, 0.7);
        }
    }
    
    .main-info {
        .avatar {
            width: 120px;
            height: 120px;
        }
        a {
            color: #6291ba;
        }
        .court_name {
            font-size: 1rem;
        }
    }
</style>