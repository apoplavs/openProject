<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 offset-md-3">
                <div class="card" id="input-form">
                    <div class="card-header text-center">
                        Вхід
                    </div>
                    <div class="card-body">
                        <form >
                            <div class="form-group">
                                <label for="email" class="form-control-label">
                                    E-Mail
                                </label>
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
                                <small  class="form-text text-muted">We'll never share your email with anyone else.</small>
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-control-label">
                                    Пароль
                                </label>
                                <input
                                        id="password"
                                        type="password"
                                        class="form-control"
                                        name="password"
                                        v-model="password"
                                        required
                                >
                                <small class="form-text text-muted">We'll never share your email with anyone else.</small>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="remember"
                                            v-model="remember"
                                            @click="remember = !remember"
                                            checked
                                    >
                                    Запамятати мене
                                </label>
                            </div>
                            <div class="form-group">
                                <div class="d-flex justify-between">
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