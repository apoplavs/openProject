
<template>
 <modal v-show="isModalVisible" @close="closeModal" @save="saveChanges">
            <h4 slot="header">Оновити статус судді</h4>
            <div slot="body">
                <form>
                    <div class="form-group row mx-0 my-4">
                        <label for="chooser-judge-status" class="col-4">Статус</label>
                        <div class="col-8">
                            <select class="form-control" id="chooser-judge-status" v-model="judgeStatus.set_status" value="judgeStatus.set_status">
                                <option value="1">на роботі</option>
                                <option value="2">на лікарняному</option>
                                <option value="3">у відпустці</option>
                                <option value="4">відсутній на робочому місці</option>
                                <option value="5">припинено повноваження</option>
                            </select>
                        </div>
                        <input type="hidden" id="judge-for-new-status" value="0">
                    </div>
                    <div class="form-group row mx-0 my-4">
                        <label for="status-end-date" class="col-7">Дата завершення дії статусу <br><sup class="text-muted">(якщо відома)</sup></label>
                        <div class="col-5">
                            <datepicker v-model="judgeStatus.due_date" :value="judgeStatus.due_date" language="uk" :min="calendar.startDate | formatDate" :max="calendar.endDate | formatDate">
                            </datepicker>
                        </div>
                    </div>
                </form>
            </div>
    
        </modal>

</template>

<script>
    export default {
        name: "Modal",
  
    }
</script>

<style scoped lang="scss">

</style>