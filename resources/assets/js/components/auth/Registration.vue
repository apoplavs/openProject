<template>
    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-6 offset-md-3">
                <div class="card" id="input-form">
                    <div class="card-header">
                        Реєстрація
                    </div>
                    <div class="card-body">
                        <form class="form-group">

                            <div class="form-group}">
                                <label for="name" class="col-lg-4 form-control-label">
                                    Ім'я
                                </label>
                                <div class="col-lg-12">
                                    <input id="name" type="text" class="form-control" name="name" value="" required autofocus>

                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-lg-4 form-control-label">
                                    E-Mail
                                </label>
                                <div class="col-lg-12">
                                    <input id="email" type="email" class="form-control" name="email" value="" required>

                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-lg-4 form-control-label">
                                    Пароль
                                </label>
                                <div class="col-lg-12">
                                    <input id="password" type="password" class="form-control" name="password" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="col-lg-6 form-control-label">
                                    Підтвердити пароль
                                </label>
                                <div class="col-lg-12">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        Зареєструватись
                                    </button>
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
        name: "registration"
    }
</script>

<style scoped>

</style>