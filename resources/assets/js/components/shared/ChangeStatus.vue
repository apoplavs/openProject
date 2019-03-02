
<template>
    <transition name="modal-fade">
        <div class="modal-backdrop">
            <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription">
                <header class="modal-header" id="modalTitle">
                    <h4>Оновити статус судді</h4>
                    <button type="button" class="btn-close" @click="closeModal" aria-label="Close modal">x</button>
                </header>
                <section class="modal-body" id="modalDescription">
    
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
                </section>
                <footer class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="closeModal">
                        Закрити
                    </button>
                    <button type="button" class="btn btn-info" @click="saveChanges">
                        Змінити сатус
                    </button>
                </footer>
            </div>
        </div>
    </transition>
</template>

<script>
    import Datepicker from "vue-date";
    
    export default {
        name: "ChangeStatus",
        props: {
            judgeData: Object
        },
        components: {
            Datepicker
        },
        data() {
            return {
                judgeStatus: {
                    set_status: null,
                    due_date: null,
					old_set_status: null,
					old_due_date: null
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
            this.judgeStatus.set_status = this.judgeData.status;
            this.judgeStatus.due_date = this.formattingDate(this.judgeData.due_date_status);

			// запам'ятовуємо старий статус, щоб повернути його в випадку помилки
			this.judgeStatus.old_set_status = this.judgeStatus.set_status;
			this.judgeStatus.old_due_date = this.judgeStatus.due_date;
    
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
				this.$emit('closeModal');

				// встановлюємо новий статус
				this.judgeData.status = this.judgeStatus.set_status;
				this.judgeData.due_date_status = this.judgeStatus.due_date;
				
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

                    })
                    .catch(error => {
                        if (error.response && error.response.status === 401) {
                            this.$router.push('/login');
                        } else {
							this.judgeData.status = this.judgeStatus.old_set_status;
							this.judgeData.due_date_status = this.judgeStatus.old_due_date;
                        }
						this.$toasted.error("Неможливо змінити статус, перевірте Ваше інтернет з'єднання або спробуйте пізніше", {
							theme: "primary",
							position: "top-right",
							duration: 5000
						});
                        console.log(error);
                    });
            },
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../sass/modal.scss";
</style>