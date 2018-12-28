<template>
    <div class="user-profile">  
        <div class="w-100">
            <div class="">
                <ul class="nav nav-tabs w-1140" role="tablist" >
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
                <div class="tab-content container w-1140">
                    <div role="tabpanel" class="tab-pane  active" id="CourtSessions">
                        <keep-alive>
                            <court-sessions/>
                        </keep-alive>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="CourtPractice">
                        <keep-alive>
                            <court-practice/>
                        </keep-alive>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="Establishments">
                        <keep-alive>
                            <establishments />
                        </keep-alive>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="ViewsHistory">
                        <keep-alive>
                            <views-history />
                        </keep-alive>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="Templates">
                        <keep-alive>
                            <templates />
                        </keep-alive>
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
    @import "../../../sass/_variables.scss";
    // @import "../../../sass/_mixins.scss";
    .user-profile {
        display: flex;
        justify-content: center;
    }
    
    .w-1140 {
        min-width: 1140px !important;
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
        color: $text-color; //$text-color;
        background-color: $body-bg; // $body-bg;
        border: 0;
        padding: 15px;
        border-radius: 0;
        border-top: 2px solid $main-color;
    }
    
    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link:not(.active) {
        color: $text-muted;
        background-color: #ffffff;
        border: 0;
        border-radius: 0;
        padding: 15px;
        &:hover {
             background-color: $body-bg;
        }
    }
</style>

