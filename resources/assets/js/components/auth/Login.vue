<template>
    <div class="container mt-5 d-flex justify-content-center">
        <vue-toastr ref="toastr"></vue-toastr>
        <div class="card">
            <div class="card-header text-center">
                Вхід
            </div>
            <div class="card-body">
                <div id="back-error" class="is-danger w-100 text-center">
                    <small>Не вірний логін або пароль</small>
                </div>
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
                                    v-model="user.email"
                                    v-validate="'required|email'"
                                    :class="{'input': true, 'is-danger': errors.has('email') }"

                            >
                            <small>
                                <i v-show="errors.has('email')" class="fa fa-warning"></i>
                                <span v-show="errors.has('email')"
                                      class="help is-danger">{{ errors.first('email') }}</span>
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
                                    v-model="user.password"
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
                            <button type="submit" class="btn btn-my-primary" id="submit-btn">
                                Увійти
                            </button>
                            <div class="footer-link d-flex align-items-center">
                                <router-link to="/registration">
                                    <a>Забув пароль?</a>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "login",
        data: () => {
            return {
                user: {
                    email: '',
                    password: ''
                }
            }
        },
        methods: {
            validateBeforeSubmit() {
                this.$validator.validateAll()
                    .then(result => {
                        if (result) {
                            axios.post('/api/v1/login', this.user, {
                                headers: {
                                    'Content-Type': 'application/json',
                                }
                            }).then(response => {
                                    if (response) {
                                        // TODO something
                                    }
                                    // let token = res.data.token;
                                    // console.log(token);
                                    // localStorage.setItem('token', token);
                                    // this.$router.push('/home');
                                }).catch(error => {
                                if(error.response && error.response.status === 401) {
                                    if (error.response.data && error.response.data.message) {
                                        console.error(error.response.data.message);
                                        this.$toasted.error('Користувач не зареєстований!', {
                                            theme: "primary",
                                            position: "top-right",
                                            duration : 5000
                                        })
                                    }
                                }
                            });
                        }
                    });

            }
        }
    }
</script>

<style lang="scss" scoped>
    .card {
        width: 100%;
        max-width: 450px;

        input[aria-invalid="true"] {
            border-color: red;
        }
        i.fa-warning, span.is-danger,
        #back-error {
            color: red;
        }
        #back-error {
            display: none;
        }
        .card-header {
            font-size: 1.5em;
            color: #408080 !important;
            font-weight: 700;
        }
        .btn-my-primary {
            background-color: #408080;
            border-color: #408080;
            border-bottom: 3px solid #2d5656;
            color: #ffffff;
        }
        button:hover,
        button:active {
            opacity: .8;
        }
        .footer-link {
            font-weight: 300;
        }
    }
</style>