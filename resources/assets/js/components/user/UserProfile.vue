<template>
    <div class="user-profile">  
        <div class="w-100">
            <div class="">
                <ul class="nav nav-tabs">              
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(1)">Судові засідання</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(2)">Судді</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(3)">Судові установи</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link tab" href="javascript:" @click="setActiveTab(4)">Історія переглядів</a>
                    </li>
                </ul>
            </div>
             <div class="tab-content w-100">
                <court-sessions v-if="tabs.sessions"/>
                <judges-bookmarks v-if="tabs.judges"/>
                <courts-bookmarks v-if="tabs.courts"/>
                <view-history v-if="tabs.history"/>
             </div>
        </div>
    </div>
</template>

<script>
    import CourtSessions from './components/CourtSessions.vue';
    import JudgesBookmarks from './components/JudgesBookmarks.vue';
    import CourtsBookmarks from './components/CourtsBookmarks.vue';
    import ViewHistory from './components/ViewHistory.vue';
      
    export default {
        name: "UserProfile",
        components: {
            CourtSessions,
            JudgesBookmarks,
            CourtsBookmarks,
            ViewHistory
        },
        data() {
            return {
                tabs: {
                    sessions: false,
                    judges: false,
                    courts: false,
                    history: false
                },
                user: {}
            }
        },
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
    @import "../../../sass/_mixins.scss";
    .user-profile {
        display: flex;
        justify-content: center;
    }
    
    .w-1140 {
        min-width: 1140px !important;
    }
    
    .nav.nav-tabs {
        height: 60px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }
    
    .nav-item {
        margin: 0 10px 0 10px;
    }
    
    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        color: $main-color; 
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
        border: 0;
        border-radius: 0;
        padding: 15px;
        &:hover {
             background-color: $body-bg;
        }
    }
</style>

