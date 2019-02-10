<template>
  <div class="content-wrapper">
    <div class="container d-flex justify-content-center">
      <div class="card">
        <div class="card-header justify-content-center">Реєстрація</div>
        <div class="card-body">
          <form @submit.prevent="validateBeforeSubmit">
            <div class="form-group">
              <label for="name" class="form-control-label">Ім'я</label>
              <p class="control has-icon has-icon-right">
                <input
                  id="name"
                  type="text"
                  class="form-control"
                  name="ім'я"
                  v-model="user.name"
                  v-validate="'required|alpha|min:3||max:250'"
                  :class="{'input': true, 'is-danger': errors.has('ім\'я') }"
                >
                <small v-show="errors.has('ім\'я')">
                  <span class="help is-danger">{{ errors.first('ім\'я') }}</span>
                </small>
              </p>
            </div>

            <div class="form-group">
              <label for="email" class="form-control-label">E-Mail</label>
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
                <small v-show="errors.has('email')">
                  <span class="help is-danger">{{ errors.first('email') }}</span>
                </small>
              </p>
            </div>
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
            <div class="form-group">
              <div class="text-center">
                <button type="submit" class="btn b-primary" id="register-btn">
                  <i class="fas fa-spinner" v-if="isLoading"></i>
                  Зареєструватись
                </button>
              </div>
            </div>
            <hr>
            <div class="form-group socials">
              <login-google/>
              <span class="px-3">-або-</span>
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
  name: "Registration",
  components: {
    LoginFacebook,
    LoginGoogle
  },
  data() {
    return {
      isLoading: false,
      user: {
        name: "",
        email: "",
        password: "",
        repassword: ""
      }
    };
  },
  methods: {
    validateBeforeSubmit() {
      this.$validator.validateAll().then(result => {
        if (result && this.user.password === this.user.repassword) {
          this.isLoading = true;
          // document.getElementById('register-btn').setAttribute('disabled');
          let newUser = {};
          newUser.name = this.user.name;
          newUser.email = this.user.email;
          newUser.password = this.user.password;
          axios
            .post("/api/v1/signup", newUser, {
              headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest"
              }
            })
            .then(response => {
              if (response) {
                this.$toasted.success(
                  "Вітаємо! Вам на пошту відпрвавлений лист з підтвердженням реєстрації!",
                  {
                    theme: "primary",
                    position: "top-center",
                    duration: 5000
                  }
                );
              }
            })
            .catch(error => {
              if (error.response && error.response.status) {
                if (error.response.data && error.response.data.message) {
                  console.error(error.response.data.message);
                  this.$toasted.error(error.response.data.message, {
                    theme: "primary",
                    position: "top-right",
                    duration: 5000
                  });
                }
              } else {
                if (error.response.data && error.response.data.message) {
                  console.error(error.response.data.message);
                  this.$toasted.error("Щось пішло не так:( Спробуйте ще раз!", {
                    theme: "primary",
                    position: "top-right",
                    duration: 10000
                  });
                }
              }
            });
        } else {
          this.$toasted.error("Заповніть коректно всі поля!", {
            theme: "primary",
            position: "top-right",
            duration: 10000
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
  .help.is-danger {
    color: red;
  }
  .input.is-danger {
    border: 1px solid red;
  }
  input:not([type="checkbox"]) {
    @include boxShadow($shadow-input);
    border: none;
  }
  .card-header {
    font-size: 1.3rem;
  }
  .socials {
    @include alignElement();
    margin-top: 1.5rem;
  }
  hr {
    height: 1px;
    margin: 2em 0;
    border: 0;
    background-image: -webkit-linear-gradient(
      left,
      rgba(228, 228, 231, 0),
      rgba(228, 228, 231, 0.9) 10%,
      rgba(228, 228, 231, 0.9) 90%,
      rgba(228, 228, 231, 0)
    );
    background-image: linear-gradient(
      to right,
      rgba(228, 228, 231, 0),
      rgba(228, 228, 231, 0.9) 10%,
      rgba(228, 228, 231, 0.9) 90%,
      rgba(228, 228, 231, 0)
    );
  }
}
</style>