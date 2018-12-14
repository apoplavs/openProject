<template>
    <div>
        <span v-if="Number(judgeData.status) === 1"> <!-- Cуддя на роботі  -->
            <i class="fa fa-briefcase" aria-hidden="true"></i> на роботі 
            {{ judgeData.due_date_status ? '('+judgeData.due_date_status+')' : null }}
        </span>
        <span v-if="Number(judgeData.status) === 2"> <!-- На лікарняному  -->
            <i class="fa fa-medkit" aria-hidden="true"></i> на лікарняному 
            {{ judgeData.due_date_status ? '(до '+judgeData.due_date_status+')' : null }}
        </span>
        <span v-if="Number(judgeData.status) === 3"> <!-- У відпустці   -->
            <i class="fas fa-umbrella-beach"></i> у відпустці 
            {{ judgeData.due_date_status ? '(до '+judgeData.due_date_status+')' : null }}
        </span>
        <span v-if="Number(judgeData.status) === 4"> <!-- Відсуній на робочому місці з інших причин  --> 
            <i class="fa fa-calendar-minus-o" aria-hidden="true"></i> відсутній на робочому місці з інших причин 
            {{ judgeData.due_date_status ? '(до '+judgeData.due_date_status+')' : null }}
        </span>
        <span v-if="Number(judgeData.status) === 5"> <!-- Припинено повноваження  -->
            <i class="fa fa-calendar-times-o" aria-hidden="true"></i> припинено повноваження 
            {{ judgeData.due_date_status ? '(до '+judgeData.due_date_status+')' : null }}
        </span>
    </div>
</template>
<script>
    export default {
        name: "StatusComponent",
        props: {
            judgeData: Object
        },
        computed: {
            checkStatus(number) {
                return Number(judgeData.status) === number;
            }
        }

    }
</script>

