<template>
    <div>
        <div class="card-body px-4 py-2">
            <div v-if="!this.list">За заданими параметрами нічого не знайдено</div>
            <div v-if="this.list" class="container">
                <div class="judge-component row py-3 " v-for="(data, index) of list.data.data" :key="index">

                    <div class="col-9 d-flex pl-0">
                        <div class="mr-3"><img class="avatar" :src="data.photo" alt="фото" /></div>
                        <div>
                            <h4>
                                <router-link to="/"> {{ data.surname }} {{ (data.name.length >  1) ?  data.name : data.name + '.' }} {{ data.patronymic.length > 1 ? data.patronymic : data.patronymic + '.' }} </router-link>
                            </h4>
                            <h5>{{ data.court_name }}</h5>
                        </div>
                    </div>
                    <div class="col-3">
                        <div> <i class="fa fa-line-chart" aria-hidden="true"> {{ data.rating }} </i></div>
                        <div>
                            <!-- <span>відстежується</span> <i class="fa fa-bookmark" aria-hidden="true"></i> -->
                            <span>відстежувати</span> <i class="fa fa-bookmark-o" aria-hidden="true"></i>
                        </div>
                        <div>

                            <span title="змінити статус судді"><i class="fa fa-pencil p-1" aria-hidden="true"  data-toggle="modal" data-target="#changeJudgeStatus"></i></span>
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
        props: ['list'],
        // data() {
        //     return {
        //         // list: this.data,
    
        //     }
        // },
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