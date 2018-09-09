<template>
    <div>  
        <div class="card-body p-2">
            <div v-if="!this.list">За заданими параметрами нічого не знайдено</div>
            <div v-if="this.list" class="container">
                <div class="row" v-for="(data, index) of list.data.data" :key="index">
                    <div class="col-12 col-lg-2 p-0 text-center">
                        <img :src="data.photo" alt="фото" style="width: 50px; height: 80px;"/>
                    </div>
                    <div class="col-12 col-lg-10 p-0">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-lg-6 text-center text-lg-left" title="ПІБ судді">
                                    <h5>
                                        <router-link to="/"> {{ data.surname }} {{ (data.name.length >  1) ?  data.name : data.name + '.' }} {{ data.patronymic.length > 1 ? data.patronymic : data.patronymic + '.' }} </router-link>
                                    </h5>
                                </div>
                                <div class="col-4 col-lg-2" title="рейтинг">
                                    <i class="fa fa-line-chart" aria-hidden="true"> NaN </i>
                                </div>
                                <div class="col-8 col-lg-4">
                                    <span class="text-muted">
    										<small class="text-muted float-right mr-3 bookmark">
    												<!-- <span>відстежується</span> <i class="fa fa-bookmark" aria-hidden="true"></i> -->
                                    <span>відстежувати</span> <i class="fa fa-bookmark-o" aria-hidden="true"></i>
                                    </small>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-7" title="найменування суду">
                                    <div class="float-left court-name">$judge->court_name</div>
                                </div>
                                <div class="col-12 col-lg-5">
                                    <small class="text-muted ml-1" id="judge->id" title="статус судді, дата оновлення статусу">
    										'judges.judge-statuses
    									</small> if юзер ввійшов - є можливість змінювати статус
                                    <span title="змінити статус судді"><i class="fa fa-pencil p-1" aria-hidden="true"  data-toggle="modal" data-target="#changeJudgeStatus"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-3 my-lg-2">
                <div class="row">
                    <div class="col-12">
                        // udges_list->links() //
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
    
</style>