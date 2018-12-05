<template>
    <div class="user-profile content-wrapper">  
        <div class="w-100">
            <div class="">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#CourtSessions" role="tab" data-toggle="tab">Судові засідання</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#CourtPractice" role="tab" data-toggle="tab">Судова практика</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#Establishments" role="tab" data-toggle="tab">Заклади</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ViewsHistory" role="tab" data-toggle="tab">Історія переглядів</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#Templates" role="tab" data-toggle="tab">Шаблони</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex justify-content-center">
                <!-- Tab panes -->
                <div class="tab-content w-1140">
                    <div role="tabpanel" class="tab-pane  active" id="CourtSessions">
                        <court-sessions/>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="CourtPractice">
                        <court-practice/>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="Establishments">
                        <establishments />
                    </div>
                    <div role="tabpanel" class="tab-pane" id="ViewsHistory">
                        <views-history />
                    </div>
                    <div role="tabpanel" class="tab-pane" id="Templates">
                        <templates />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import CourtSessions from './components/CourtSessions.vue';
    import CourtPractice from './components/CourtPractice.vue';
    import Establishments from './components/Establishments.vue';
    import ViewsHistory from './components/ViewsHistory.vue';
    import Templates from './components/Templates.vue';
    
    
    export default {
        name: "UserProfile",
        components: {
            CourtSessions,
            CourtPractice,
            Establishments,
            ViewsHistory,
            Templates
        },
        data() {
            return {
                user: {}
            }
        },
        beforeMount() {
           
        },
    }
</script>

<style scoped lang="scss">
    // @import "_variables";
    .user-profile {
        display: flex;
        justify-content: center;
    }
    
    .w-1140 {
        width: 1140px !important;
    }
    
    .nav.nav-tabs {
        height: 100px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        background-color: #ffffff;
        border-bottom: 0;
    }
    
    .nav-item {
        margin: 0 10px 0 10px;
    }
    
    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: #636b6f; //$text-color;
        background-color: #eceaea; // $body-bg;
        border: 2px solid #408080; // $logo-color;
        border-bottom: none;
    }
    
    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link:not(.active) {
        color: #636b6f; //$text-color;
        background-color: inherit;
        border: 2px solid #eceaea; // $body-bg;
        border-bottom: none;
    }
</style>

