<template>
<div class="resetPassword">
    <div class="container d-flex justify-content-center ">
        <div class="card">
            <div class="card-header justify-content-center">
                Відновлення паролю
            </div>
            <div class="card-body">
                <div id="back-error" class="is-danger w-100 text-center">
                    <small>Не вірний логін або пароль</small>
                </div>
                <form @submit.prevent="validateBeforeSubmit">
                    <div class="form-group">
                        <label for="email" class="form-control-label">Введіть email, який Ви вказували при реєстрації</label>
                        <p class="control has-icon has-icon-right">
                            <input id="email" type="email" class="form-control" name="email" v-model="user.email" v-validate="'required|email'" :class="{'input': true, 'is-danger': errors.has('email') }">
                            <small>
                                <span v-show="errors.has('email')" class="help is-danger">{{ errors.first('email') }}</span>
                            </small>
                        </p>
                    </div>
                    <div class="form-group mt-3">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn b-primary" id="submit-btn">
                                Скинути пароль
                            </button>
                        </div>
                    </div>    
                </form>              
            </div>
        </div>
    </div>
    </div>
</template>

<script>

    export default {
        name: "ResetPassword", 
        data() {
            return {
                user: {
                    email: "",
                }
            };
        },
        methods: {
            validateBeforeSubmit() {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        axios
                            .post("/api/v1/user/password/reset", this.user, {
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-Requested-With": "XMLHttpRequest"
                                }
                            })
                            .then(response => {
                                this.$toasted.success(
                                  "Вам на пошту відправлений лист з посиланням для відновлення паролю!",
                                  {
                                    theme: "primary",
                                    position: "top-center",
                                    duration: 10000
                                  }
                                );
                            })
                            .catch(error => {
                                if (error.response && error.response) {
                                    if (error.response.data && error.response.data.message) {
                                        this.$toasted.error('Даний email не зареєстрований!', {
                                            theme: "primary",
                                            position: "top-right",
                                            duration: 8000
                                        });
                                    }
                                } else {
                                    this.$toasted.error("Щось пішло не так, перевірте Ваше інтернет з'єднання, або спробуйте пізніше", {
                                        theme: "primary",
                                        position: "top-right",
                                        duration: 8000
                                    });
                                }
                            });
                    } else {
                        this.$toasted.error("Заповніть коректно всі поля!", {
                            theme: "primary",
                            position: "top-right",
                            duration: 3000
                        });
                    }
                });
            },
           
        },
    };
</script>

<style lang="scss" scoped>
  @import "../../../sass/_variables.scss";
  @import "../../../sass/_mixins.scss";

    .card {
        width: 100%;
        max-width: 450px;
        input[aria-invalid="true"] {
            border: 1px solid red;
        }
        i.fa-warning,
        span.is-danger,
        #back-error {
            color: red;
        }
        #back-error {
            display: none;
        }
        input:not([type="checkbox"]) {
            @include boxShadow($shadow-input);
            border: none;
        }
        .card-header {
            font-size: 1.3rem;
        }
        
    }
</style>