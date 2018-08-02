<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 offset-md-3">
                <div class="card" id="input-form">
                    <div class="card-header text-center">
                        Вхід
                    </div>
                    <div class="card-body">
                        <form class="form-group">
                            <div class="row">
                                <label for="email" class="col-12 form-control-label">
                                    E-Mail
                                </label>
                                <div class="col-12">
                                    <input
                                            id="email"
                                            type="email"
                                            class="form-control"
                                            name="email"
                                            value=""
                                            v-model="email"
                                            required
                                            autofocus

                                    >
                                </div>
                            </div>
                            <div class="row">
                                <label for="password" class="col-12 form-control-label">
                                    Пароль
                                </label>
                                <div class="col-12">
                                    <input
                                            id="password"
                                            type="password"
                                            class="form-control"
                                            name="password"
                                            v-model="password"
                                            required
                                    >
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <input
                                            type="checkbox"
                                            name="remember"
                                            v-model="remember"
                                            @click="remember = !remember"
                                            checked
                                    >
                                    Запамятати мене
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex justify-between">
                                    <button type="submit" class="btn btn-primary" @click.prevent="login()">
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
                password: '',
                remember: false
            }
        },
        methods: {
            login() {
                console.log(this.email, this.password, this.remember);

            }
        }
    }
</script>

<style scoped>

</style>