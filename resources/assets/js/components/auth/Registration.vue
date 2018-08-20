<template>
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card">
            <div class="card-header text-center">
                Реєстрація
            </div>
            <div class="card-body">
                <form @submit.prevent="validateBeforeSubmit">
                    <div class="form-group">
                        <label for="name" class="form-control-label">
                            Ім'я
                        </label>
                        <p class="control has-icon has-icon-right">
                            <input
                                id="name"
                                type="text"
                                class="form-control"
                                name="ім'я"
                                v-model="user.name"
                                v-validate="'required|alpha|min:1||max:30'"
                                :class="{'input': true, 'is-danger': errors.has('ім\'я') }"
                                >
                            <small>
                                <i v-show="errors.has('ім\'я')" class="fa fa-warning"></i>
                                <span v-show="errors.has('ім\'я')" class="help is-danger">{{ errors.first('ім\'я') }}</span>
                            </small>
                        </p>
                    </div>

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
                                    name="пароль"
                                    v-model="user.password"
                                    v-validate="'required|min:6|max:25'"
                                    :class="{'input': true, 'is-danger': errors.has('пароль') }"
                            >
                            <small>
                                <i v-show="errors.has('пароль')" class="fa fa-warning"></i>
                                <span v-show="errors.has('пароль')" class="help is-danger">{{ errors.first('пароль') }}</span>
                            </small>
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="repassword" class="form-control-label">
                            Підтвердити пароль
                        </label>
                        <p class="control has-icon has-icon-right">
                            <input
                                id="repassword"
                                type="password"
                                class="form-control"
                                name="підтвердити пароль"
                                v-model="user.repassword"
                                v-validate="'required|confirmed:password'"
                                <!--data-vv-as="password confirmation"-->
                            >
                            <small>
                                <!--<i v-show="errors.has('підтвердити пароль')" class="fa fa-warning"></i>-->
                                <span v-show="errors.has('підтвердити пароль')" class="help is-danger">{{ errors.message }}</span>
                            </small>
                        </p>
                    </div>

                    <div class="form-group">
                        <div class="text-center">
                            <button type="submit" class="btn btn-my-primary" id="register-btn">
                                Зареєструватись
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "registration",


        data: () => {
            return {
                user: {
                    name: '',
                    email: '',
                    password: '',
                    repassword: ''
                }

            }

        },
        methods: {
            validateBeforeSubmit() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        alert('Form Submitted!');
                        return;
                    }

                    alert('Correct them errors!');
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