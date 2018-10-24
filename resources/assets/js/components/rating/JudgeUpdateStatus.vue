<template>
     <!-- Modal -->
            <div class="modal fade" id="changeJudgeStatus" tabindex="-1" role="dialog" aria-labelledby="changeJudgeStatusLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changeJudgeStatusLabel">Оновити статус судді</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>
                                <div class="form-group row mt-1">
                                    <label for="chooser-judge-status" class="col-2 col-form-label">Статус</label>
                                    <select class="form-control col-8 ml-4" id="chooser-judge-status">
                                        <option value="1">&#xf0b1; &nbsp; на роботі</option>
                                        <option value="2">&#xf0fa; &nbsp; на лікарняному</option>
                                        <option value="3">&#xf274; &nbsp; у відпустці</option>
                                        <option value="4">&#xf272; &nbsp; відсутній на робочому місці</option>
                                        <option value="5">&#xf273; &nbsp; припиено повноваження</option>
                                    </select>
                                    <input type="hidden" id="judge-for-new-status" value="0">
                                </div>
                                <div class="form-group row mt-1">
                                    <label for="status-end-date" class="col-6 col-form-label">Дата завершення дії статусу <br><sup class="text-muted">(якщо відома)</sup></label>
                                    <div class="col-5">
                                        <input class="form-control" type="date"  id="status-end-date">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрити</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="updateJudgeStatus()">Змінити статус</button>
                        </div>
                    </div>
                </div>
            </div>
</template>
<script>

export default {
        name: 'modal-satus-judge',
        // data: () => {
            //return {}
        // }
}
</script>

<style scoped lang="scss">
  @import "../../../sass/_variables.scss";

</style>
