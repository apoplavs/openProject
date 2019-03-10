<template>
  <div class="settings">
    <spinner v-if="!loadData" />
    <div v-if="loadData" class="border content-wrapper_body">
      <!-- 1 -->
      <div class="profile">
        <h4>Профіль</h4>
        <form @submit.prevent="changeProfileData()">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label for="name" class="form-control-label">Ім'я:</label>
                <input id="name" type="text" class="form-control" name="ім'я" v-model="user.name" v-validate="'required|alpha|min:3|max:250'" :class="{'input': true, 'is-danger': errors.has('ім\'я') }">
                <small v-show="errors.has('ім\'я')">
                    <span class="help is-danger">{{ errors.first('ім\'я') }}</span>
                  </small>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <label for="name" class="form-control-label">Прізвище:</label>
                <input id="surname" type="text" class="form-control" name="прізвище" v-model="user.surname" v-validate="'required|alpha|min:3|max:250'" :class="{'input': true, 'is-danger': errors.has('прізвище')}">
                <small v-show="errors.has('прізвище')">
                    <span class="help is-danger">{{ errors.first('прізвище') }}</span>
                  </small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label for="name" class="form-control-label">Номер мобільного:</label>
                <input id="telephone" type="text" class="form-control" name="телефон" v-model="user.phone" v-mask="'(###) ### ## ##'" v-validate="'required|min:15|max:15'" :class="{'input': true, 'is-danger': errors.has('телефон') }">
                <small v-show="errors.has('телефон')">
                    <span class="help is-danger">{{ errors.first('телефон') }}</span>
                  </small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="offset-6 col-6 text-center">
              <button type="submit" class="btn btn-primary" :disabled="errors.items.length > 0">Зберегти</button>
            </div>
          </div>
        </form>
      </div>
      <div class="hr py-4"></div>
  
      <!-- 2 -->
      <div class="change-password">
        <h4>Змінити пароль:</h4>
        <form @submit.prevent="changePassword()">
          <div class="row">
            <div class="col-12 py-4">Пароль повинен бути мінімум 6 символів!</div>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label for="currentPassword" class="form-control-label">Старий пароль:</label>
                <input id="currentPassword" type="password" class="form-control" name="пароль" v-model="password.currentPassword" v-validate="'min:6|max:32'" :class="{'input': true, 'is-danger': errors.has('пароль') }">
                <small v-show="errors.has('пароль')">
                    <span class="help is-danger">{{ errors.first('пароль') }}</span>
                  </small>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <label for="newPassword" class="form-control-label">Новий пароль:</label>
                <input id="newPassword" type="password" class="form-control" name="новий пароль" v-model="password.newPassword" v-validate="'min:6|max:32'" :class="{'input': true, 'is-danger': errors.has('новий пароль') }">
                <small>
                    <span
                      v-show="errors.has('новий пароль')"
                      class="help is-danger"
                    >{{ errors.first('новий пароль') }}</span>
                  </small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label for="rePassword" class="form-control-label">Підтвердити новий пароль:</label>
                <input id="rePassword" type="password" class="form-control" name="rePassword" v-model="password.rePassword" v-validate="'min:6|max:32'" :class="{'input': true, 'is-danger': password.rePassword !== password.newPassword}">
                <small v-show="password.rePassword !== password.newPassword">
                    <span class="help is-danger">Паролі не співпадають</span>
                  </small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="offset-6 col-6 text-center">
              <button type="submit" class="btn btn-primary" :disabled="isDisabled(errors, password)">Зберегти</button>
            </div>
          </div>
        </form>
      </div>
  
      <div class="hr py-4"></div>
  
      <!-- 3 -->
      <div class="notifications">
        <h4>Налаштування сповіщень</h4>
        <form @submit.prevent="changeNotifications()">
          <div class="table">
            <div class="row header">
              <div class="col-9">Подія</div>
              <div class="col-3">
                <div>Спосіб зв'язку</div>
                <input type="checkbox" v-model="allSelected" @change="selectAll()">
                <small>Виділити все/зняти виділення</small>
              </div>
            </div>
            <div class="row hr">
              <div class="col-9">В судді, якого користувач відстежує, змінився статус.</div>
              <div class="col-3">
                <input type="checkbox" v-model="notifications.email_notification_1" @click="select()"> Email
              </div>
            </div>
            <div class="row bg">
              <div class="col-9">По справі, яку користувач відстежує, додалось нове судове засідання</div>
              <div class="col-3">
                <input type="checkbox" v-model="notifications.email_notification_2" @click="select()"> Email
              </div>
            </div>
            <div class="row">
              <div class="col-9">По справі, яку користувач відстежує, в судді змінився статус</div>
              <div class="col-3">
                <input type="checkbox" v-model="notifications.email_notification_3" @click="select()"> Email
              </div>
            </div>
            <div class="row bg">
              <div class="col-9">Нагадування про судове засідання, яке користувач відстежує</div>
              <div class="col-3">
                <input type="checkbox" v-model="notifications.email_notification_4" @click="select()"> Email
              </div>
            </div>
            <div class="row">
              <div class="col-9">Пропозиції судової практики для користувача</div>
              <div class="col-3">
                <input type="checkbox" v-model="notifications.email_notification_5" @click="select()"> Email
              </div>
            </div>
            <div class="row bg">
              <div class="col-9">Новини, пропозиції, оновлення</div>
              <div class="col-3">
                <input type="checkbox" v-model="notifications.email_notification_6" @click="select()"> Email
              </div>
            </div>
            <div class="row">
              <div class="offset-6 col-6 text-center">
                <button type="submit" class="btn btn-primary">Зберегти</button>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="hr py-4"></div>
      <div class="row">
        <div class="col-6 offset-4">
          <button type="button" class="btn btn-danger btn-lg" @click="showModalDelete()">Видалити профайл</button>
        </div>
      </div>
    </div>
  
    <!-- modal confirm -->
    <modal v-show="showModalConfirm" @close="showModalConfirm = false" @confirm="deleteProfile" :modalConfirm="true">
      <div slot="header"></div>
      <div slot="body" class="modal-message">Ви впевнені, що хочете свій аккаунт?
        <br>
        <span>Дану дію можна виконати 1 раз і вона є незворотньою</span>
      </div>
    </modal>
  </div>
</template>

<script>
  import Spinner from "../shared/Spinner.vue";
  import Modal from "../shared/Modal.vue";
  
  export default {
    name: "UserSettings",
    components: {
      Modal,
      Spinner
    },
    data() {
      return {
        loadData: false,
        showModalConfirm: false,
        user: {
          name: "",
          surname: "",
          phone: null,
          email: "",
          photo: ""
        },
        allSelected: false,
        notifications: {
          email_notification_1: 0,
          email_notification_2: 0,
          email_notification_3: 0,
          email_notification_4: 0,
          email_notification_5: 0,
          email_notification_6: 0
        },
        password: {
          currentPassword: "",
          newPassword: "",
          rePassword: ""
        }
      };
    },
    computed: {
      isEqualPasswords: () => {
        return this.user.rePassword === this.user.newPassword;
      }
    },
    created() {
      this.getUserInfo();
    },
    methods: {
      isDisabled: (errors, password) => {
        if (
          errors.items.length > 0 ||
          !password.currentPassword.length ||
          !password.rePassword.length ||
          !password.newPassword.length
        ) {
          return true;
        }
        return false;
      },
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
            this.user = response.data.profile;
            // this.user.phone = '0660851225';
            this.notifications = response.data.notifications;
            this.loadData = true;
            console.log("User settings", response);
          })
          .catch(error => {
            if (error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
          });
      },
      selectAll() {
        if (this.allSelected) {
          for (let key in this.notifications) {
            this.notifications[key] = 1;
          }
        } else {
          for (let key in this.notifications) {
            this.notifications[key] = 0;
          }
        }
      },
      select() {
        this.allSelected = false;
      },
      changeProfileData() {
        let newProfileData = {};
        if (this.user.phone) {
          newProfileData.new_phone = this.user.phone.replace(/(\(|\)|\s)/g, "");
        }
        if (this.user.surname) {
          newProfileData.new_surname = this.user.surname;
        }
        newProfileData.new_name = this.user.name;
        axios
          .post(`/api/v1/user/settings/user-data`, newProfileData, {
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              Authorization: localStorage.getItem("token")
            }
          })
          .then(response => {
            this.$toasted.success("Збережено", {
              theme: "primary",
              position: "top-right",
              duration: 3000
            });
          })
          .catch(error => {
            if (error && error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
          });
      },
      changePassword() {
        let changePass = {
          old_password: this.password.currentPassword,
          new_password: this.password.newPassword
        };
        console.log(this.password);
        axios
          .post(`/api/v1/user/settings/password`, changePass, {
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              Authorization: localStorage.getItem("token")
            }
          })
          .catch(error => {
            if (error && error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
          });
      },
  
      changeNotifications() {
        // замінюємо в чекбоксі true/false на 0/1
        for (let key in this.notifications) {
          let elem = this.notifications[key];
          this.notifications[key] = elem === true || elem === 1 ? 1 : 0;
        }
        axios
          .post(`/api/v1/user/settings/notification`, this.notifications, {
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              Authorization: localStorage.getItem("token")
            }
          })
          .then(response => {
            this.$toasted.success("Збережено", {
              theme: "primary",
              position: "top-right",
              duration: 3000
            });
          })
          .catch(error => {
            if (error && error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
            this.$router.push("/");
          });
      },
  
      showModalDelete() {
        this.showModalConfirm = true;
      },
  
      deleteProfile() {
        axios
          .delete(`/api/v1/user/settings/delete-account`, {
            headers: {
              "Content-Type": "application/json",
              "X-Requested-With": "XMLHttpRequest",
              Authorization: localStorage.getItem("token")
            }
          })
          .then(response => {
            sessionStorage.clear();
            localStorage.clear();
            this.$router.go();
          })
          .catch(error => {
            if (error && error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
            this.$router.push("/");
          });
      }
    }
  };
</script>

<style scoped lang="scss">
  @import "../../../sass/_variables.scss";
  @import "../../../sass/_mixins.scss";
  .content-wrapper_body {
    padding: 3rem;
    border-radius: 4px;
    background: #ffffff;
  }
  
  .help.is-danger {
    color: red;
  }
  
  .input.is-danger {
    border: 1px solid red;
  }
  
  input {
    @include boxShadow($shadow-input);
    border: none;
    background-color: $body-bg;
  }
  
  .table {
    .header {
      font-weight: bold;
    }
    &>.row {
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
    border-top: 1px solid rgba(0, 0, 0, 0.1);
  }
  
  .modal-message {
    text-align: center;
    font-size: 18px;
  }
  
  .modal-message span {
    color: red;
    font-size: 14px;
  }
</style>