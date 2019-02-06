<template>
  <div class="settings">
    <div class="container content-wrapper">
      <div class="border content-wrapper_body">
        <!-- 1 -->
        <div class="profile">
          <h4>Профіль</h4>
          <form>
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="name" class="form-control-label">Ім'я:</label>
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
                    <small>
                      <i v-show="errors.has('ім\'я')" class="fa fa-warning"></i>
                      <span
                        v-show="errors.has('ім\'я')"
                        class="help is-danger"
                      >{{ errors.first('ім\'я') }}</span>
                    </small>
                  </p>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="name" class="form-control-label">Прізвище:</label>
                  <p class="control has-icon has-icon-right">
                    <input
                      id="surname"
                      type="text"
                      class="form-control"
                      name="прізвище"
                      v-model="user.surname"
                      v-validate="'required|alpha|min:3||max:250'"
                      :class="{'input': true, 'is-danger': errors.has('прізвище') }"
                    >
                    <small>
                      <i v-show="errors.has('прізвище')" class="fa fa-warning"></i>
                      <span
                        v-show="errors.has('прізвище')"
                        class="help is-danger"
                      >{{ errors.first('прізвище') }}</span>
                    </small>
                  </p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="name" class="form-control-label">Номер мобільного:</label>
                  <p class="control has-icon has-icon-right">
                    <input
                      id="telephone"
                      type="text"
                      class="form-control"
                      name="телефон"
                      v-model="user.phone"
                      :class="{'input': true, 'is-danger': errors.has('телефон') }"
                    >
                    <small>
                      <i v-show="errors.has('телефон')" class="fa fa-warning"></i>
                      <span
                        v-show="errors.has('телефон')"
                        class="help is-danger"
                      >{{ errors.first('телефон') }}</span>
                    </small>
                  </p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="offset-6 col-6 text-center">
                <button type="submit" class="btn btn-primary">Зберегти</button>
              </div>
            </div>
          </form>
        </div>

        <div class="hr py-4"></div>

        <!-- 2 -->
        <div class="change-password">
          <h4>Змінити пароль:</h4>
          <form>
            <div class="row">
              <div class="col-12 py-4">Пароль повинен бути мінімум 6 символів!</div>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="password" class="form-control-label">Старий пароль:</label>
                  <p class="control has-icon has-icon-right">
                    <input
                      id="password"
                      type="password"
                      class="form-control"
                      name="пароль"
                      v-model="password.currentPassword"
                      v-validate="'required|min:6|max:32'"
                      :class="{'input': true, 'is-danger': errors.has('пароль') }"
                    >
                    <small>
                      <i v-show="errors.has('пароль')" class="fa fa-warning"></i>
                      <span
                        v-show="errors.has('пароль')"
                        class="help is-danger"
                      >{{ errors.first('пароль') }}</span>
                    </small>
                  </p>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label for="repassword" class="form-control-label">Новий пароль:</label>
                  <p class="control" :class="{error: !(user.repassword === user.password)}">
                    <input
                      id="repassword"
                      type="password"
                      class="form-control"
                      name="repassword"
                      v-model="password.newPassword"
                      v-validate="'required|min:6|max:32'"
                      :class="{'is-danger': !(user.repassword === user.password)}"
                    >
                    <small v-show="!(user.repassword === user.password)">
                      <i class="fa fa-warning"></i>
                      <span class="help is-danger">Паролі не співпадають</span>
                    </small>
                  </p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label for="password" class="form-control-label">Підтвердити новий пароль:</label>
                  <p class="control has-icon has-icon-right">
                    <input
                      id="password"
                      type="password"
                      class="form-control"
                      name="пароль"
                      v-model="password.rePassword"
                      v-validate="'required|min:6|max:32'"
                      :class="{'input': true, 'is-danger': errors.has('пароль') }"
                    >
                    <small>
                      <i v-show="errors.has('пароль')" class="fa fa-warning"></i>
                      <span
                        v-show="errors.has('пароль')"
                        class="help is-danger"
                      >{{ errors.first('пароль') }}</span>
                    </small>
                  </p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="offset-6 col-6 text-center">
                <button type="submit" class="btn btn-primary">Зберегти</button>
              </div>
            </div>
          </form>
        </div>

        <div class="hr py-4"></div>

        <!-- 3 -->
        <div class="notifications">
          <h4>Налаштування сповіщень</h4>
          <div class="table">
            <div class="row">
              <div class="col-9">Подія</div>
              <div class="col-3">
                <div>Спосіб зв'язку</div>
                <input type="checkbox">
              </div>
            </div>
            <div class="row hr">
              <div class="col-9">В судді, якого користувач відстежує, змінився статус.</div>
              <div class="col-3">
                <input type="checkbox"> Email
              </div>
            </div>
            <div class="row bg">
              <div class="col-9">По справі, яку користувач відстежує, додалось нове судове засідання</div>
              <div class="col-3">
                <input type="checkbox"> Email
              </div>
            </div>
            <div class="row">
              <div class="col-9">По справі, яку користувач відстежує, в будь-якого судді змінився статус</div>
              <div class="col-3">
                <input type="checkbox"> Email
              </div>
            </div>
            <div class="row bg">
              <div class="col-9">За один день до судового засідання, яке користувач відстежує</div>
              <div class="col-3">
                <input type="checkbox"> Email
              </div>
            </div>
            <div class="row">
              <div class="col-9">Про пропозиції судової практики для користувача</div>
              <div class="col-3">
                <input type="checkbox"> Email
              </div>
            </div>
            <div class="row bg">
                <div class="col-9">Новини, пропозиції, оновлення</div>
                <div class="col-3">
                <input type="checkbox"> Email
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "UserSettings",
  data() {
    return {
      user: {
        name: "Tetiana",
        surname: "Prysiazhniuk",
        phone: "+380660851225"
      },
      password: {
        currentPassword: "",
        newPassword: "",
        rePassword: ""
      }
    };
  },
  created() {
    this.getUserInfo();
  },
  methods: {
    getUserInfo() {
      axios
        .get(`/api/v1/user/settings`, {
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
        .then(response => {
          this.user = response.data;
          console.log("User settings", this.user);
        })
        .catch(error => {
          if (error.response && error.response.status === 401) {
            this.$router.push("/login");
          }
        });
    }
  }
};
</script>

<style scoped lang="scss">
@import "../../../sass/_variables.scss";
@import "../../../sass/_mixins.scss";
.content-wrapper_body {
  //   height: 100%;
  padding: 3rem;
  border-radius: 4px;
  background: #ffffff;
}
input {
  @include boxShadow($shadow-input);
  border: none;
  background-color: $body-bg;
}
.table {
  & > .row {
    padding: 15px 0;
  }
  .bg {
    background-color: $body-bg;
  }
}
.btn-primary {
  min-width: 200px;
  text-transform: uppercase;
}
.hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0,0,0,.1);
}

// .content-wrapper  {
//     border: 1px solid gray;
// }
</style>