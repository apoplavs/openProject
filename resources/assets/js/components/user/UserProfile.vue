<template>
    <div class="user-profile">  
        <div class="w-100">
            <div class="">
                <ul class="nav nav-tabs w-1140">              
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(1)">Судові засідання</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(2)">Судова практика</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(3)">Заклади</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(4)">Історія переглядів</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(5)">Шаблони</a>
                    </li>
                </ul>
            </div>
             <div class="tab-content container w-1140">
                <court-sessions v-if="tabs.sessions"/>
                <court-practice v-if="tabs.practice"/>
                <establishments v-if="tabs.establishments"/>
                <view-history v-if="tabs.history"/>
                <templates v-if="tabs.templates"/>
             </div>
            <!-- <div class="d-flex justify-content-center">
                <div class="tab-content container w-1140">
                    <div role="tabpanel" class="tab-pane" id="CourtSessions">
                        <court-sessions/>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="CourtPractice">
                        <court-practice/>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="Establishments">
                        <establishments />
                    </div>
                    <div role="tabpanel" class="tab-pane" id="ViewHistory">
                        <h1>BLYAA</h1>
                        <view-history />
                    </div>
                    <div role="tabpanel" class="tab-pane" id="Templates">
                        <templates />
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</template>

<script>
    import CourtSessions from './components/CourtSessions.vue';
    import CourtPractice from './components/CourtPractice.vue';
    import Establishments from './components/Establishments.vue';
    import ViewHistory from './components/ViewHistory.vue';
    import Templates from './components/Templates.vue';
    
    
    export default {
        name: "UserProfile",
        components: {
            CourtSessions,
            CourtPractice,
            Establishments,
            ViewHistory,
            Templates
        },
        data() {
            return {
                tabs: {
                    sessions: false,
                    practice: false,
                    establishments: false,
                    history: false,
                    templates: false
                },
                user: {}
            }
        },
        // beforeMount() {
            
        //     console.log('LOL');
        // },
        mounted() {
            this.setActiveTab(1);
        },
        methods: {
            setActiveTab(indexTab) {                                
                Object.keys(this.tabs).forEach((key, index) => {
                    this.tabs[key] = false;
                    if (index + 1 === indexTab) {
                        this.tabs[key] = true;
                    }
                });                      
                let list = document.getElementsByClassName('nav-link tab');
                for (let index = 0; index < list.length; index++) {   
                    list[index].classList.remove("active");                
                }
                list[indexTab - 1].classList.add('active')
            }
        }
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
        color: #568a8a; //$text-color;
        background-color: $body-bg; // $body-bg;
        border: 0;
        padding: 15px;
        border-radius: 0;
        border-top: 2px solid $main-color;
        text-transform: uppercase;
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

