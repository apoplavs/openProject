<template>
    <div class="privacy-policy">  
        <div class="card a4">
            ferfaervg
            frvgrvgrea
            vgf
        </div>
        <div class="card a4">
            ferfaervg
            frvgrvgrea
            vgf
        </div>
    </div>
</template>

<script>
    // import CourtSessions from './components/CourtSessions.vue';
    // import CourtPractice from './components/CourtPractice.vue';
    // import JudgesBookmarks from './components/JudgesBookmarks.vue';
    // import CourtsBookmarks from './components/CourtsBookmarks.vue';
    // import ViewHistory from './components/ViewHistory.vue';
      
    export default {
        name: "PrivacyPolicy",
        components: {
            // CourtSessions,
            // CourtPractice,
            // JudgesBookmarks,
            // CourtsBookmarks,
            // ViewHistory
        },
        data() {
            return {
                tabs: {
                    sessions: false,
                    practice: false,
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
    // @import "../../../sass/_mixins.scss";
    .a4 {
        width: 20cm !important;
        height: 30cm !important;
        margin: 50px auto 20px auto;
        padding: 1cm 0.5cm 1cm 2cm; 
    }


    .user-profile {
        display: flex;
        justify-content: center;
        margin-top: 30px;
    }
    
    .w-1140 {
        min-width: 1140px !important;
    }
    
    .nav.nav-tabs {
        height: 60px;
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

