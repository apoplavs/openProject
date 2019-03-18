<template>
  <div class="recoverPassword">
    <div class="d-flex justify-content-center">
      <div class="card">
        <div class="card-header justify-content-center">Відновлення паролю</div>
        <div class="card-body">
          <div id="back-error" class="is-danger w-100 text-center">
            <small>Не вірний логін або пароль</small>
          </div>
          <form @submit.prevent="validateBeforeSubmit">
            <div class="form-group">
              <label for="password" class="form-control-label">Пароль</label>
              <p class="control has-icon has-icon-right">
                <input
                  id="password"
                  type="password"
                  class="form-control"
                  name="пароль"
                  v-model="user.password"
                  v-validate="'required|min:6|max:32'"
                  :class="{'input': true, 'is-danger': errors.has('пароль') }"
                >
                <small v-show="errors.has('пароль')">
                  <span class="help is-danger">{{ errors.first('пароль') }}</span>
                </small>
              </p>
            </div>

            <div class="form-group">
              <label for="repassword" class="form-control-label">Підтвердити пароль</label>
              <p class="control" :class="{error: !(user.repassword === user.password)}">
                <input
                  id="repassword"
                  type="password"
                  class="form-control"
                  name="repassword"
                  v-model="user.repassword"
                  v-validate="'required|min:6|max:32'"
                  :class="{'input': true, 'is-danger': user.repassword !== user.password}"
                >
                <small v-show="!(user.repassword === user.password)">
                  <span class="help is-danger">Паролі не співпадають</span>
                </small>
              </p>
            </div>

            <div class="form-group mt-3">
              <div class="d-flex justify-content-center">
                <button type="submit" class="btn b-primary" id="submit-btn">Надіслати</button>
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
  name: "RecoverPassword",
  data() {
    return {
      user: {
        password: "",
        repassword: ""
      }
    };
  },
  methods: {
    validateBeforeSubmit() {
      this.$validator.validateAll().then(result => {
        if (result) {
            let userData = {};
            userData.token = this.$route.query.token;
            userData.password = this.user.password;
          //console.log('this.$route.query.token', userData)
          axios
            .post("/api/v1/user/password/new", userData, {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
              }
            })
            .then(response => {
              this.$toasted.success("Пароль успішно оновлено!", {
                theme: "primary",
                position: "top-center",
                duration: 8000
              });
              this.$router.push("/user-profile");
            })
            .catch(error => {
              this.$toasted.error(
                "Щось пішло не так, перевірте Ваше інтернет з'єднання або спробуйте пізніше!",
                {
                  theme: "primary",
                  position: "top-right",
                  duration: 8000
                }
              );
            });
        } else {
          this.$toasted.error("Заповніть коректно всі поля!", {
            theme: "primary",
            position: "top-right",
            duration: 8000
          });
        }
      });
    }
  }
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