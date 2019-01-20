<template>
  <div class="header min-width">
    <nav class="navigation-menu">
      <div class="container min-width custom-navbar">
        <a class="navbar-brand" href="#">
          <img src="../../../images/logo.png" width="40" alt="logo">
          <span class="logo">ТОЕсуд</span>
        </a>
        <ul class="menu">
          <router-link to="/" tag="li" class="nav-item">
            <a class="nav-link" :class="{'active': $route.fullPath === '/'}">На головну</a>
          </router-link>
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle"
              href="#"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              :class="{'active': $route.fullPath === '/judges' || $route.fullPath === '/courts'}"
            >Рейтинг</a>
            <div class="dropdown-menu raiting">
              <router-link
                to="/judges"
                class="dropdown-item"
                :class="{'active': $route.fullPath === '/judges'}"
              >Cудді</router-link>
              <router-link
                to="/courts"
                class="dropdown-item"
                :class="{'active': $route.fullPath === '/courts'}"
              >Суди</router-link>
            </div>
          </li>
          <router-link to="/about" tag="li" class="nav-item" disabled>
            <a class="nav-link" :class="{'active': $route.fullPath === '/about'}">Про нас</a>
          </router-link>
          <router-link to="/contacts" tag="li" class="nav-item" disabled>
            <a class="nav-link" :class="{'active': $route.fullPath === '/contacts'}">Контакти</a>
          </router-link>
          <!-- left  -->
          <div class="d-flex ml-lg-5">
            <router-link to="/login" tag="li" class="nav-item" v-if="!isAuth">
              <a class="nav-link login" :class="{'active': $route.fullPath === '/login'}">Вхід</a>
            </router-link>
            <router-link to="/registration" tag="li" class="nav-item" v-if="!isAuth">
              <a
                class="nav-link registration"
                :class="{'active': $route.fullPath === '/registration'}"
              >Реєстрація</a>
            </router-link>
            <li class="nav-item dropdown user" v-if="isAuth"
            :class="{'active': $route.fullPath === '/settings' || $route.fullPath === '/user-profile' || $route.fullPath === '/user-profile'}"
            >
              <a
                class="nav-link dropdown-toggle"
                href="#"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                :class="{'active': $route.fullPath === '/settings' || $route.fullPath === '/user-profile' || $route.fullPath === '/user-profile'}"
              >{{ user.name }}</a>
              <div class="dropdown-menu user-cabinet">
                <li class="py-3">
                  <span class="email-user">{{ user.email }}</span>
                </li>
                <router-link to="/user-profile" tag="li" class="nav-item">
                  <a class="dropdown-item" :class="{'active': $route.fullPath === '/user-profile'}">
                    Особистий кабінет
                    <i class="fa fa-home float-right" aria-hidden="true"></i>
                  </a>
                </router-link>
                <router-link to="/settings" tag="li" class="nav-item">
                  <a class="dropdown-item" :class="{'active': $route.fullPath === '/settings'}">
                    Налаштування
                    <i class="fa fa-cog float-right" aria-hidden="true"></i>
                  </a>
                </router-link>
                <li @click="logout()">
                  <a class="dropdown-item">
                    Вийти
                    <i class="fa fa-sign-out float-right" aria-hidden="true"></i>
                  </a>
                </li>
              </div>
            </li>
          </div>
        </ul>
      </div>
    </nav>
  </div>
</template>

<script>
export default {
  name: "header-component",
  data() {
    return {};
  },
  computed: {
    isAuth() {
      return this.$store.getters.isAuth;
    },
    user() {
      return this.$store.getters.getUser;
    }
  },

  methods: {
    logout() {
      axios
        .get("/api/v1/logout", {
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
        .then(response => {
          this.$store.commit("logout");
          this.$router.push("/login");
        })
        .catch(error => {
          if (error.response.status === 401) {
            localStorage.clear();
            this.$router.push("/login");
          }
          console.log(error);
        });
    }
  }
};
</script>

<style scoped lang="scss">
@import "../../../sass/_variables.scss";
@import "../../../sass/_mixins.scss";
.header {
    height: 90px;
    border-top: 2px solid $main-color;
  nav.navigation-menu {
    box-shadow: $shadow-header;
  }
  .custom-navbar {
    @include alignElement($justifyContent: space-between);
    padding: 20px 0;

    ul.menu {
      @include alignElement($justifyContent: space-between);
      list-style-type: none;
      margin: 0;
      a {
        color: $text-color;
        &:hover {
          color: $main-color;
        }
      }
      .active {
        color: $main-color;
        font-weight: 500px;
      }
      .user {
        border: 1px solid $text-color;
        border-radius: $btn-border-radius;
        &.active {
          border-color: $main-color;
        }
        
        .email-user {
          padding: 0.25rem 1.5rem;
          color: $primary;
        }
        .user-cabinet {
          transform: translate3d(-132px, 42px, 0px) !important;
          width: 15rem;
        }
      }
      
      .raiting {
        transform: translate3d(-65px, 40px, 0px) !important;
      }
      .dropdown-item > i {
        color: $text-color;
        margin-top: 4px;
      }
      .dropdown-toggle::after {
        vertical-align: 0.1em;
      }
      .dropdown-item.active,
      .dropdown-item:active {
        background-color: transparent !important;
      }
      .login {
        border: 1px solid $main-color;
        border-radius: $btn-border-radius;
        color: $main-color;
        margin-right: 10px;
      }
      .registration {
        background: $main-color;
        color: #ffffff;
        border: 1px solid $main-color;
        border-radius: $btn-border-radius;
        &:hover {
          color: #ffffff;
        }
      }
    }
    .navbar-brand {
      @include alignElement();
      .logo {
        color: $text-color;
        font-weight: 500;
        font-size: 1.5rem;
        letter-spacing: 0.1em;
        // text-shadow: 3px 0 $main-color;
      }
    }
  }
}
</style>
