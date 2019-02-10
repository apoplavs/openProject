<template>
    <div v-if="confirmed">
        <div class="row mt-5">
            <div class="col-8 offset-2 mt-5">
                <p class="bg-success h2 text-center">Email підтверджено, вітаємо в системі!</p>
                <p class="text-muted text-center">За декілька секунд Вас буде перенаправлено на сторінку входу</p>
            </div>
        </div>
    </div>

    <div v-else>
        <div class="row mt-5">
            <div class="col-8 offset-2 mt-5">
                <p class="text-danger h4 text-center">Даний email вже підтверджено, або його не існує в системі!</p>
                <p class="text-muted text-center">За декілька секунд Вас буде направлено на головну сторінку</p>
            </div>
        </div>
    </div>
</template>


<script>

    export default {
        name: "ConfirmEmail",
        data() {
            return {
				confirmed: true
            }
        },
        created() {

			var _this = this;

        	// якщо токена немає, або він неповний
        	if (this.$route.query.token === undefined || this.$route.query.token.length < 16) {
				_this.confirmed = false;
				this.$router.push("/");
				return;
            }
			axios
				.get(`/api/v1/confirm-email`, {
					headers: {
						"Content-Type": "application/json",
						"X-Requested-With": "XMLHttpRequest"
					},
					params: {
						token: this.$route.query.token
					}
				})
				.then(response => {
					_this.confirmed = true;
					setTimeout(function(){
						_this.$router.push("/login");
					}, 7000);
				})
				.catch(error => {
					if (error.response && error.response.status === 401) {
						_this.confirmed = false;
						setTimeout(function(){
							_this.$router.push("/");
						}, 5000);
					} else {
						_this.$router.push("/");
                    }
				});
        }
    }
</script>

<style scoped lang="scss">
    @import "../../../sass/_variables.scss";
</style>

