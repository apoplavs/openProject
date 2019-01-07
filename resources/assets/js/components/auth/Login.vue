<template>
<div class="content-wrapper">
    <div class="container d-flex justify-content-center ">
        <!--<vue-toasted ref="toasted"></vue-toasted>-->
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
                        <label for="email" class="form-control-label">E-Mail</label>
                        <p class="control has-icon has-icon-right">
                            <input id="email" type="email" class="form-control" name="email" v-model="user.email" v-validate="'required|email'" :class="{'input': true, 'is-danger': errors.has('email') }">
                            <small>
                                <i v-show="errors.has('email')" class="fa fa-warning"></i>
                                <span v-show="errors.has('email')" class="help is-danger">{{ errors.first('email') }}</span>
                            </small>
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-control-label">Пароль</label>
                        <p class="control has-icon has-icon-right">
                            <input id="password" type="password" class="form-control" name="password" v-model="user.password" v-validate="'required|min:6|max:32'">
                            <small><i v-show="errors.has('password')" class="fa fa-warning"></i>
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
                                    value=1
                                    v-model="user.remember_me"
                            >
                            Запамятати мене
                        </label>
                         <router-link to="/recover-password">
                            <a>Забув пароль?</a>
                        </router-link>
                    </div>
                    <div class="form-group mt-3">
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                Увійти
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group socials">
                        <login-google/>
                        <span class="px-3">або</span>
                        <login-facebook/>
                    </div>    
                </form>              
            </div>
        </div>
    </div>
    </div>
</template>

<script>
    import LoginFacebook from "../shared/FacebookSignInButton.vue";
    import LoginGoogle from "../shared/GoogleSignInButton.vue";

    export default {
        name: "Login",
		components: {
            LoginFacebook,
            LoginGoogle
		}, 
        data() {
            return {
                user: {
                    isAuth: false,
                    email: "",
                    password: "",
                    remember_me: 1
                }
            };
        },
        methods: {
             getUserData(callback) {
                axios
                    .get('/api/v1/user', {
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "Authorization": localStorage.getItem('token')
                        }
    
                    })
                    .then(response => {
                        let user = {
                            name: response.data.name,
                            email: response.data.email
                        }
                       
                        localStorage.setItem('user', JSON.stringify(user));     
                        this.$store.commit('auth_success', response.data.email);
                        callback();
                    })
                    .catch(error => {
                        console.log('ERR ', error);
                    });
            },
            validateBeforeSubmit() {
                this.$validator.validateAll().then(result => {
                    if (result) {
                        this.user.remember_me = this.user.remember_me === true || this.user.remember_me === 1 ? 1 : 0; //конвертую чекбокс в 1 або 0 по дефолку true/false
                        axios
                            .post("/api/v1/login", this.user, {
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-Requested-With": "XMLHttpRequest"
                                }
                            })
                            .then(response => {
                                if (response) {
                                    let token = response.data.token_type + " " + response.data.access_token;
                                    localStorage.setItem("token", token);  
                                    this.getUserData( () => {
                                        this.$router.push("/user-profile");                             
                                    });
                                }
                            })
                            .catch(error => {
                                if (error.response && error.response) {
                                    if (error.response.data && error.response.data.message) {
                                        this.$toasted.error(error.response.data.message, {
                                            theme: "primary",
                                            position: "top-right",
                                            duration: 5000
                                        });
                                    }
                                } else {
                                    this.$toasted.error("Щось пішло не так :( Cпробуй пізніше", {
                                        theme: "primary",
                                        position: "top-right",
                                        duration: 5000
                                    });
                                    alert(error);
                                }
                            });
                    } else {
                        this.$toasted.error("Заповніть коректно всі поля!", {
                            theme: "primary",
                            position: "top-right",
                            duration: 5000
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
        .form-check {
            @include alignElement($justifyContent: space-between);
        }
        .socials {
            @include alignElement();
            margin-top: 1.5rem;
        }
        
    }
</style>