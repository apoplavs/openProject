<template>
<div class="">
    <nav class="navbar  sticky-top navbar-expand-lg navbar-light bg-light">
        <div class="container">
        <a class="navbar-brand" href="#"><img src="../../../images/logo.png" width="40" alt="logo" />ТОЕсуд</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#NavbarMenu" aria-controls="NavbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
        <div class="collapse navbar-collapse" id="NavbarMenu">
    
            <!-- NAVIGATION -->
            <ul class="navbar-nav">
                <!-- right -->
                <router-link exact to="/" tag="li" class="nav-item" active-class="active">
                    <a class="nav-link">На головну</a>
                </router-link>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="First" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Рейтинг</a>
                    <div class="dropdown-menu" aria-labelledby="First">
                        <router-link to="/judges"  active-class="active">
                            <a class="dropdown-item">Судді</a>
                        </router-link>
                        <router-link to="/courts" active-class="active">
                            <a class="dropdown-item">Суди</a>
                        </router-link>
                    </div>
                </li>
                <router-link to="/about" tag="li" class="nav-item" active-class="active" disabled>
                    <a class="nav-link">Про нас</a>
                </router-link>
                <router-link to="/contacts" tag="li" class="nav-item" active-class="active" disabled>
                    <a class="nav-link">Контакти</a>
                </router-link>
                <!-- left  -->
                <div class="d-lg-flex ml-lg-5">
                    <router-link to="/login" tag="li" class="nav-item" active-class="active" v-if="!isAuth">
                        <a class="nav-link">Вхід</a>
                    </router-link>
                    <router-link to="/registration" tag="li" class="nav-item" active-class="active" v-if="!isAuth">
                        <a class="nav-link">Реєстрація</a>
                    </router-link>
                    <li class="nav-item dropdown" v-if="isAuth">
                        <a class="nav-link dropdown-toggle" href="#" id="Second" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ name }}
                                </a>
                        <div class="dropdown-menu" aria-labelledby="Second">
                            <li>
                                <span class="dropdown-item">{{ email }}</span>
                            </li>
                            <router-link to="/user-profile" tag="li" class="nav-item" active-class="active">
                                <a class="dropdown-item">Особистий кабінет <i class="fa fa-home text-muted float-right" aria-hidden="true"></i></a>
                            </router-link>
                            <router-link to="#" tag="li" class="nav-item" active-class="active">
                                <a class="dropdown-item">Налаштування <i class="fa fa-cog text-muted float-right" aria-hidden="true"></i></a>
                            </router-link>
                            <li @click="logout()">
                                <a class="dropdown-item">Вийти <i class="fa fa-sign-out text-muted float-right" aria-hidden="true"></i></a>
                            </li>
                        </div>
                    </li>
                </div>
            </ul>
        </div>
        </div>
    </nav>
    </div>
</template>

<script>
    export default {
        name: "header-component",
        data() {
            return {
                name: localStorage.getItem('name'),
                email: localStorage.getItem('email'),
            }
        },
        computed: {
            isAuth() {
                return localStorage.getItem('token');
            }
        },
        watch: {
            userData: function() {
                this.name = localStorage.getItem('name');
                this.email = localStorage.getItem('email')
            }
        },
        methods: {
            logout() {
                axios
                    .get("/api/v1/logout", {
                        headers: {
                            "Content-Type": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "Authorization": localStorage.getItem('token')
                        },
                    })
                    .then(response => {
                        localStorage.clear();
                        this.$router.push('/login')
                    })
                    .catch(error => {
                        if (error.response.status === 401) {
                            localStorage.clear();
                            this.$router.push('/login');
                        }
                        console.log(error);
                    });
            }
        }
    };
</script>

<style scoped lang="scss">
  @import "../../../sass/_variables.scss";

.active {
    > a.nav-link{
   color:  #2b989b!important;
    }
}
    .navbar-light {
        box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.2), 0 4px 15px 0 rgba(0, 0, 0, 0.19);
    }
    
    ul li a:hover {
        color: #2b989b !important;
        cursor: pointer;
    }
    
    ul li a i {
        font-size: 1.1em;
    }
    
    @media (min-width: 992px) {
        .navbar-expand-lg .navbar-collapse {
            justify-content: flex-end;
        }
        .navbar-expand-lg .navbar-nav .dropdown-menu {
            left: -150px;
            width: 250px;
        }
    }
</style>
