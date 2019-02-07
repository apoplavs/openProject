<template>
  <div class="settings">
    <div class="container content-wrapper">
      <div class="border content-wrapper_body">
        <!-- 1 -->
        <div class="profile">
          <h4>Профіль</h4>
          <form @submit.prevent="saveProfileData()">
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
                    <small v-show="errors.has('ім\'я')">
                      <span 
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
                      v-validate="'alpha|min:3||max:250'"
                      :class="{'input': true, 'is-danger': errors.has('прізвище')}"
                    >
                    <small  v-show="errors.has('прізвище')">
                      <span
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
                      v-mask="'(###) ### ## ##'"
                      v-validate="'min:15||max:15'"
                      :class="{'input': true, 'is-danger': errors.has('телефон') }"
                    >
                    <small v-show="errors.has('телефон')">
                      <span   
                        class="help is-danger"
                      >{{ errors.first('телефон') }}</span>
                    </small>
                  </p>
                </div>
              </div>
            </div>
            {{errors}}
            <div class="row">
              <div class="offset-6 col-6 text-center">
                {{errors.items === []}}
                <button type="submit" class="btn btn-primary" :disabled="errors.items === []">Зберегти</button>
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
                  <label for="currentPassword" class="form-control-label">Старий пароль:</label>
                  <p class="control has-icon has-icon-right">
                    <input
                      id="currentPassword"
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
                  <label for="newPassword" class="form-control-label">Новий пароль:</label>
                  <p class="control" :class="{error: !(user.repassword === user.password)}">
                    <input
                      id="newPassword"
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
                  <label for="rePassword" class="form-control-label">Підтвердити новий пароль:</label>
                  <p class="control has-icon has-icon-right">
                    <input
                      id="rePassword"
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
          <form @submit.prevent="saveNotifications()">
            <div class="table">
              <div class="row header">
                <div class="col-9">Подія</div>
                <div class="col-3">
                  <div>Спосіб зв'язку</div>
                  <input type="checkbox" v-model="allSelected" @change="selectAll()">
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
                <div class="col-9">По справі, яку користувач відстежує, в будь-якого судді змінився статус</div>
                <div class="col-3">
                  <input type="checkbox" v-model="notifications.email_notification_3" @click="select()"> Email
                </div>
              </div>
              <div class="row bg">
                <div class="col-9">За один день до судового засідання, яке користувач відстежує</div>
                <div class="col-3">
                  <input type="checkbox" v-model="notifications.email_notification_4" @click="select()"> Email
                </div>
              </div>
              <div class="row">
                <div class="col-9">Про пропозиції судової практики для користувача</div>
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
         <div class="row">
            <div class="col-6">
              <button type="button" class="btn btn-danger" @click="deleteProfile()">Видалити профайл</button>
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
        name: '',
        surname: '',
        phone: null,
        email:'',
        photo: ''
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
        currentPassword: '',
        newPassword: '',
        rePassword: ''
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
          this.user = response.data.profile;
          this.user.phone = '0660851225';
          this.notifications = response.data.notifications;
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
    saveProfileData() {
      let newProfileData = {};
      if (this.user.phone) {
        newProfileData.new_phone = this.user.phone.replace(/(\(|\)|\s)/g, '');
      }
      newProfileData.new_name = this.user.name;
      newProfileData.new_surname = this.user.surname;
      axios.post(`/api/v1/user/settings/user-data`, newProfileData, {
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          Authorization: localStorage.getItem("token")
        }
        }) .catch(error => {
        if (error && error.response && error.response.status === 401) {
          this.$router.push("/login");
        }
      })
    },
    saveNotifications() {
      // замінюємо в чекбоксі true/false на 0/1
      for (let key in this.notifications) {
        let elem = this.notifications[key];
        this.notifications[key] = (elem === true || elem === 1) ? 1 : 0;
      }
      axios.post(`/api/v1/user/settings/notification`, this.notifications, {
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          Authorization: localStorage.getItem("token")
        }
        }) .catch( error => {
          if (error && error.response && error.response.status === 401) {
            this.$router.push("/login");
        }
      })
    },
    deleteProfile() {
      console.log('Я хочу видалити свій профайл. Що скажете?')
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
  .header {
    font-weight: bold;
  }
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

</style>