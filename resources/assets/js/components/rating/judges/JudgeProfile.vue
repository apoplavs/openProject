<template>
    <div class="judge-profile my-5">
        <spinner v-if="!loadData" />
        <div v-if="loadData" class="container">
            <div class="judge-info">
                <div class="card mt-2">
                    <div class="card-body d-flex">
                        <div class="photo w-25 p-3">
                            <!-- img -->
                        </div>
                        <div class="w-75 p-3">
                            <div class="main-info">
                                <h2>Бандура Анна Петрівна</h2>
                                <div class="d-flex">
                                    <i class="fa fa-university" aria-hidden="true"></i>
                                    <h3 class="court-name">Mлинівський районний суд Житомирської області</h3>
                                </div>
                                <div class="detail-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>вул Степана Бандери, 7 буд 302, 321222</span>
                                </div>
                                <div class="detail-info">
                                    <i class="fas fa-phone"></i>
                                    <span>(03132) 432 32 43</span>
                                </div>
                                <div class="detail-info">
                                    <i class="far fa-envelope"></i>
                                    <span>infobox@google.com</span>
                                </div>
                                <div class="detail-info">
                                <i class="fas fa-link"></i>
                                    <a target="_blank" href="google.com">www.google.com</a>
                                </div>      
                            </div>
                            <div class="status-info">
                                <div class="status">
                                    <span v-if="status === 1"> <!-- Cуддя на роботі  -->
                                        <i class="fa fa-briefcase" aria-hidden="true"></i>на роботі 
                                        <!-- {{ judge.due_date_status ? '('+judge.due_date_status+')' : null }} -->
                                    </span>
                                    <span v-if="status === 2"> <!-- На лікарняному  -->
                                        <i class="fa fa-medkit" aria-hidden="true"></i>на лікарняному 
                                        <!-- {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }} -->
                                    </span>
                                    <span v-if="status === 3"> <!-- У відпустці   -->
                                        <i class="fas fa-umbrella-beach"></i>у відпустці 
                                        <!-- {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }} -->
                                    </span>
                                    <span v-if="status === 4"> <!-- Відсуній на робочому місці з інших причин  --> 
                                            <i class="fa fa-calendar-minus-o" aria-hidden="true"></i>
                                        відсутній на робочому місці з інших причин 
                                        <!-- {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }} -->
                                    </span>
                                    <span v-if="status === 5"> <!-- Припинено повноваження  -->
                                            <i class="fa fa-calendar-times-o" aria-hidden="true"></i>припинено повноваження 
                                        <!-- {{ judge.due_date_status ? '(до '+judge.due_date_status+')' : null }} -->
                                    </span>
                                </div>
                                <div class="rating">
                                    <span>
                                        <i class="fas fa-thumbs-up"></i>25
                                    </span>
                                    <span>
                                        <i class="fa fa-line-chart" aria-hidden="true"> 12%</i>
                                    </span>
                                    <span>
                                        <i class="fas fa-thumbs-down"></i>2
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
                        <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="params.search" @keyup="liveSearch()">
                    </div>
                    <div class="card-body">
                        <div class="court-sessions">
                            <div class="container-component">
                                <div class="row header text-muted">
                                    <div class="col-2 pl-0">Дата розгляду</div>
                                    <div class="col-2">Номер справи</div>
                                    <div class="col-4">Сторони у справі</div>
                                    <div class="col-3">Суть справи</div>
                                    <div class="col-1 pr-0"></div>
                                </div>
                                <div class="row" v-for="(e, i_el) in [1,2,3,4,5]" :key="i_el">
                                    <div class="col-2 pl-0">
                                        <div>25.09.2018</div>
                                        <div>14:15</div>
                                    </div>
                                    <div class="col-2">923/623.10</div>
                                    <div class="col-4">Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод</div>
                                    <div class="col-3">Стягнення заборгованості </div>
                                    <div class="col-1 pr-0">
                                        <i v-if="i_el === 2" class="fas fa-star"></i>
                                        <i v-else class="far fa-star"></i>
                                    </div>
                                </div>
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
    
                    </div>
                </div>
                <div class="card w-50 mt-2 ml-1">
                    <div class="card-header">
                        Кримінальне судочинство
                    </div>
                    <div class="card-body">
    
                    </div>
                </div>
            </div>
            <div class="d-flex">
                <div class="card w-50 mt-2 mr-1">
                    <div class="card-header">
                        <span>Судочинство в порядку КУпАП</span>
                    </div>
                    <div class="card-body">
    
                    </div>
                </div>
                <div class="card w-50 mt-2 ml-1">
                    <div class="card-header">
                        Адміністративне судочинство
                    </div>
                    <div class="card-body">
    
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import Spinner from '../../shared/Spinner.vue';

    export default {
        name: "JudgeProfile",
        data() {
            return {
                status: 1,
                loadData: true,
                params: {
                    search: ''
                }
            }
        },
        components: {
            Spinner
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../../sass/_variables.scss";
    @import "../../../../sass/_mixins.scss";
    .judge-profile {
        .main-info {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            .fa-university {
                color: $primary;
                font-size: 1.7rem;
                margin-right: 5px;
            }
            .court-name {
                color: $text-muted;
                font-weight: 300;
            }
            .detail-info {
                @include alignElement($justifyContent: start);
                color: $text-muted;
                i[class^="fa"] {
                    width: 25px;
                }
            }
        }
        .status-info {
            .rating {
                @include alignElement($justifyContent: space-between);
            }
        }
        .court-sessions {
            width: 100%;
            height: auto;
            font-size: .9rem;
            .fa-star {
                color: $main-color;
            }
            .container-component {
                padding: 20px;
                background-color: #ffffff;
            }
            .row {
                margin: 0;
                padding: 15px 0;
                &:not(:last-child) {
                    border-bottom: 1px solid lightgrey;
                }
            }
        }
        input[type="search"] {
            width: 200px;
        }
    }
</style>