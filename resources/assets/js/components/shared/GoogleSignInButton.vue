<template>
  <g-signin-button :params="googleSignInParams" @success="onSignInSuccess" @error="onSignInError">
    <img src="../../../images/google.png" width="30" alt="logo" /> &nbsp; Вхід через Google
  </g-signin-button>
</template>

<script>
  import Vue from 'vue';
  import GSignInButton from 'vue-google-signin-button';
  import LoginVue from '../auth/Login.vue';
  Vue.use(GSignInButton);
  
  
  
  export default {
    name: "login-google",
    data() {
      return {
        /**
         * The Auth2 parameters, as seen on
         * https://developers.google.com/identity/sign-in/web/reference#gapiauth2initparams.
         * As the very least, a valid client_id must present.
         */
        googleSignInParams: {
          client_id: '545266301595-t4ol1okp8967isgt8f6imoi4jnhlk141.apps.googleusercontent.com'
        }
      }
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
      onSignInSuccess(googleUser) {
        // `googleUser` is the GoogleUser object that represents the just-signed-in user.
        // See https://developers.google.com/identity/sign-in/web/reference#users
        const profile = googleUser.getBasicProfile()
        const user = {
          id: profile.getId(),
          name: profile.getGivenName(),
          surname: profile.getFamilyName(),
          email: profile.getEmail(),
          picture: profile.getImageUrl()
        }
  
        axios.post('/api/v1/login/google', user, {
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).then(response => {
          if (response) {
            let token =
              response.data.token_type + " " + response.data.access_token;
            localStorage.setItem("token", token);
            console.log('ya idu dodmkuuuu')
            this.getUserData(() => {
              this.$router.push("/user-profile");
            });
          }
        }).catch(error => {
          if (error.response && error.response) {
            if (error.response.data && error.response.data.message) {
              this.$toasted.error(error.response.data.message, {
                theme: "primary",
                position: "top-right",
                duration: 15000
              });
            }
          } else {
            this.$toasted.error("Щось пішло не так :(  Спробуйте ввійти через google", {
              theme: "primary",
              position: "top-right",
              duration: 15000
            });
            alert(error);
          }
        });
      },
      onSignInError(error) {
        this.$toasted.error('Щось пішло не так :(  Спробуйте ввійти іншим способом', {
          theme: "primary",
          position: "top-right",
          duration: 10000
        })
      }
    }
  }
</script>

<style>
  .g-signin-button {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    background-color: #3c82f7;
    color: #fff;
    cursor: pointer;
  }
</style>