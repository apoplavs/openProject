<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 offset-md-3">
                <div class="card" id="input-form">
                    <div class="card-header text-center">
                        Вхід
                    </div>
                    <div class="card-body">
                        <div id="back-error" class="is-danger w-100 text-center"><small>Не вірний логін або пароль</small></div>
                        <form @submit.prevent="validateBeforeSubmit">
                            <div class="form-group">
                                <label for="email" class="form-control-label">
                                    E-Mail
                                </label>
                                <p class="control has-icon has-icon-right">
                                    <input
                                            id="email"
                                            type="email"
                                            class="form-control"
                                            name="email"
                                            v-model="email"
                                            v-validate="'required|email'"
                                            :class="{'input': true, 'is-danger': errors.has('email') }"

                                    >
                                    <small>
                                        <i v-show="errors.has('email')" class="fa fa-warning"></i>
                                        <span v-show="errors.has('email')" class="help is-danger">{{ errors.first('email') }}</span>
                                    </small>
                                </p>
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-control-label">
                                    Пароль
                                </label>
                                <p class="control has-icon has-icon-right">
                                    <input
                                            id="password"
                                            type="password"
                                            class="form-control"
                                            name="password"
                                            v-model="password"
                                            v-validate="'required|min:6|max:25'"
                                    >
                                    <small>
                                        <i v-show="errors.has('password')" class="fa fa-warning"></i>
                                        <span v-show="errors.has('password')" class="help is-danger">{{ errors.first('password') }}</span>
                                    </small>
                                </p>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="remember"
                                    >
                                    Запамятати мене
                                </label>
                            </div>
                            <div class="form-group mt-3">
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary" id="submit-btn">
                                        Увійти
                                    </button>
                                    <div>
                                        <router-link to="/registration">
                                            <a class="footer-link">Забув пароль?</a>
                                        </router-link>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "login",
        data() {
            return {
                email: '',
                password: ''
            }
        },
        methods: {
            validateBeforeSubmit() {

                this.$validator.validateAll()
                    .then((result) => {
                        if (result) {

                            // $('#submit-btn').removeAttr('di')
                            alert('OK');
                            // this.login();
                           // console.log($('#submit-btn').disabled)
                            // console.log(this)
                            console.log(this.email, this.password);
                            return;
                        }
                        alert('Correct them errors!');
                    });
            },

        }
    }
</script>

<style scoped>
    input[aria-invalid="true"] {
        border-color: red;
    }
    i.fa-warning, span.is-danger,
    #back-error{
        color: red;
    }
    #back-error{
        display: none;
    }
    .card-header {
        font-size: 1.5em;
        color: #408080 !important;
        font-weight: 700;
    }
    .btn-primary {
        background-color: #408080;
        border-color: #408080;
        border-bottom: 3px solid #2d5656;
    }
    button:hover(:not:disabled),
    button:active(:not:disabled){
        opacity: .8;
    }



</style>