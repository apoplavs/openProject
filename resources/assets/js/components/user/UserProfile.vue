<template>
    <div class="personal-cabinet">
        <div class="container">
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
    
            <!-- Tab panes -->
            <div class="tab-content">
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
</template>

<script>
import CourtSessions from './components/CourtSessions.vue';
import CourtPractice from './components/CourtPractice.vue';
import Establishments from './components/Establishments.vue';
import ViewsHistory from './components/ViewsHistory.vue';
import Templates from './components/Templates.vue';


    export default {
        name: "personal-account",
        components: {
            CourtSessions,
            CourtPractice,
            Establishments,
            ViewsHistory,
            Templates
        }
    }
</script>

<style scoped lang="scss">
    
</style>

