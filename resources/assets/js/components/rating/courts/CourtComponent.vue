<template>
    <div>
        <div class="card-body px-4 py-2">
            <div v-if="!this.judgesList || this.judgesList.length == 0">За заданими параметрами нічого не знайдено</div>
            <div v-if="this.judgesList && this.judgesList.length > 0">
                <div class="court-component row py-3 mx-1" v-for="(court, ind_1) of this.courtList" :key="ind_1">
                    
                </div>
            </div>
        </div>
    
    </div>
</template>

<script>    
    export default {
        name: "CourtComponent",
        data() {
            return {
                isAuth: localStorage.getItem("token"),
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    Authorization: localStorage.getItem("token")
                }
            };
        },
        props: ["courtList"],
        methods: {
            changeBookmarkStatus(judge) {
                if (!this.isAuth) {
                    this.$router.push("/login");
                }
                if (court.is_bookmark === 0) {
                    axios({
                            method: "put",
                            url: `/api/v1/courts/${court.id}/bookmark`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                Authorization: localStorage.getItem("token")
                            }
                        })
                        .then(response => {
                            court.is_bookmark = 1;
                        })
                        .catch(error => {
                            console.log("Bookmark", error);
                        });
                } else {
                    axios({
                            method: "delete",
                            url: `/api/v1/courts/${court.id}/bookmark`,
                            headers: {
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                Authorization: localStorage.getItem("token")
                            }
                        })
                        .then(response => {
                            court.is_bookmark = 0;
                        })
                        .catch(error => {
                            console.log('Bookmark', error);
                        });
                }
            },
           
        },
        components: {
        }
    };
</script>

<style scoped lang="scss">
    @import "../../../../sass/_variables.scss";
    @import "../../../../sass/_mixins.scss";
    
</style>