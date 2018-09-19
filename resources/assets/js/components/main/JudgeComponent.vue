<template>
    <div>
        <div class="card-body px-4 py-2">
            <div v-if="!this.list">За заданими параметрами нічого не знайдено</div>
            <div v-if="this.list" class="container">
                <div class="judge-component row py-3 " v-for="(judge, index) of this.list.data.data" :key="index">
    
                    <div class="col-9 d-flex pl-0">
                        <div class="mr-3"><img class="avatar" :src="judge.photo" alt="фото" /></div>
                        <div>
                            <h4>
                                <router-link to="/"> {{ judge.surname }} {{ (judge.name.length > 1) ? judge.name : judge.name + '.' }} {{ judge.patronymic.length > 1 ? judge.patronymic : judge.patronymic + '.' }} </router-link>
                            </h4>
                            <h5>{{ judge.court_name }}</h5>
                        </div>
                    </div>
                    <div class="col-3">
                        <div> <i class="fa fa-line-chart" aria-hidden="true"> {{ judge.rating }} </i></div>
                        <div v-if="isAuth">
                            <span v-if="judge.is_bookmark" @click="changeBookmarkStatus(judge)">відстежується</span> <i class="fa fa-bookmark" aria-hidden="true"></i>
                            <span v-if="!judge.is_bookmark" @click="changeBookmarkStatus(judge)">відстежувати</span> <i class="fa fa-bookmark-o" aria-hidden="true"></i>
                        </div>
                        <div v-if="isAuth">
                            <span title="змінити статус судді">Змінити статус судді<i class="fa fa-pencil p-1" aria-hidden="true"  data-toggle="modal" data-target="#changeJudgeStatus"></i></span>
                        </div>
    
                    </div>
    
    
    
    
    
    
    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "judge-component",
        data: () => {
            return {
                isAuth: localStorage.getItem('token'),
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                }
            }
        },
        props: ['list'],
        // data() {
        //     return {
        //         // list: this.data,
    
        //     }
        // },
        methods: {
            changeBookmarkStatus(judge) {
                console.log(judge)
                // if (judge.is_bookmark === true) {
                //     axios
                //         .delete(`/api/v1//judges/${id}/bookmark`, this.headers)
                //         .then(response => {
    
                //             // console.log(response);
                //         })
                //         .catch(error => {
                //             console.log(error);
                //         });
                // } else {
    
                // }
            }
        },
        mounted() {
            //  console.log('props ----- ', this.data);
    
        }
    }
</script>

<style scoped lang="scss">
    .judge-component:not(:last-child) {
        border-bottom: 1px solid lightgray;
    }
    
    .avatar {
        width: 130px;
        height: 130px;
    }
</style>