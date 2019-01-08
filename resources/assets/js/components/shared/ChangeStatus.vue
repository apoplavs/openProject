
<template>
    <modal @close="closeModal" @save="saveChanges">
        <h4 slot="header">Оновити статус судді</h4>
        <div slot="body">
            <form>
                <div class="form-group row mx-0 my-4">
                    <label for="chooser-judge-status" class="col-4">Статус</label>
                    <div class="col-8">
                        <select class="form-control" id="chooser-judge-status" v-model="judgeStatus.set_status" :value="judgeStatus.set_status">
                            <option value="1">на роботі</option>
                            <option value="2">на лікарняному</option>
                            <option value="3">у відпустці</option>
                            <option value="4">відсутній на робочому місці</option>
                            <option value="5">припинено повноваження</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row mx-0 my-4">
                    <label for="status-end-date" class="col-7">Дата завершення дії статусу <br><sup class="text-muted">(якщо відома)</sup></label>
                    <div class="col-5">
                        <datepicker v-model="judgeStatus.due_date" :value="judgeStatus.due_date" language="uk" :min="calendar.startDate | formatDate" :max="calendar.endDate | formatDate"></datepicker>
                    </div>
                </div>
            </form>
        </div>
    </modal>
</template>

<script>
    import Datepicker from "vue-date";
    import Modal from "./Modal.vue";

    export default {
        name: "ChangeStatus",
        props: {
            judgeData: Object
        },
        components: {
            Modal,
            Datepicker
        },
        data() {
            return {
                judgeStatus: {
                    set_status: null,
                    due_date: null
                },
                calendar: {
                    startDate: new Date(),
                    endDate: new Date()
                },
            }
        },
        filters: {
            formatDate(date) {
                // getMobth() чомусь рахує місяці з 0 date.getMonth() + 1 
                if (date === '' || date === null) {
                    return '';
                } else {
                    return `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`;
                }
            }
        },
        created() {
            if (!this.isAuth) {
                this.$router.push('/login');
            }       
            this.judgeStatus.set_status = this.judgeData.status;
            this.judgeStatus.due_date = this.formattingDate(this.judgeData.due_date_status);

            this.calendar.startDate = new Date();
            this.calendar.endDate = new Date(
                this.calendar.startDate.getFullYear(),
                this.calendar.startDate.getMonth() + 1, //end date limit 1 month
                this.calendar.startDate.getDate()
            );
        },
         methods: {
            formattingDate(date) {
                if (date === '' || date === null) {
                    return '';
                } else {
                    let arr = _.split(date, '.');
                    if (arr.length > 1) {
                        return `${arr[2]}-${arr[1]}-${arr[0]}`;
                    } else {
                        return arr[0];
                    } 
                }
            },
            closeModal() {
                this.$emit('closeModal');
            },
            saveChanges() {
                if (this.judgeStatus.set_status === "1" || this.judgeStatus.set_status === "5") {
                    this.judgeStatus.due_date = null;
                }
                axios({
                        method: "put",
                        url: `/api/v1/judges/${this.judgeData.id}/update-status`,
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            Authorization: localStorage.getItem("token")
                        },
                        data: this.judgeStatus
                    })
                    .then(response => {
                        this.judgeData.status = this.judgeStatus.set_status;
                        this.judgeData.due_date_status = this.judgeStatus.due_date;
                        this.$emit('closeModal');
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            this.$router.push('/login');
                        }
                        console.log(error);
                    });
            },
        }
    }
</script>

<style scoped lang="scss">

</style>