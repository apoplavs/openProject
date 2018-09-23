
<template>
    <nav class="navbar  sticky-top navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#NavbarMenu" aria-controls="NavbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>
        <div class="collapse navbar-collapse" id="NavbarMenu">
    
            <ul class="navbar-nav">
    
                <router-link exact to="/" tag="li" class="nav-item" active-class="active">
                    <a class="nav-link">На головну</a>
                </router-link>
    
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Рейтинг
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <router-link to="/judges-list">
                            <a class="dropdown-item">Судді</a>
                        </router-link>
                        <router-link to="#">
                            <a class="dropdown-item disabled">Суди<small>( <i class="fa fa-code" aria-hidden="true"></i> в розробці)</small></a>
                        </router-link>
    
                    </div>
                </li>
    
                <router-link to="/about" tag="li" class="nav-item" active-class="active">
                    <a class="nav-link  disabled">Про нас</a>
                </router-link>
    
                <router-link to="/contacts" tag="li" class="nav-item" active-class="active">
                    <a class="nav-link  disabled">Контакти</a>
                </router-link>
    
                <router-link to="/login" tag="li" class="nav-item" active-class="active">
                    <a class="nav-link">Вхід</a>
                </router-link>
    
                <router-link to="/registration" tag="li" class="nav-item" active-class="active">
                    <a class="nav-link">Реєстрація</a>
                </router-link>
    
    
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Тетяна
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li>
                            <span class="text-muted">tatka_28@gmail.com</span>
                        </li>
                        <router-link to="#" tag="li" class="nav-item" active-class="active">
                            <a class="dropdown-item">Особистий кабінет <i class="fa fa-home text-muted float-right" aria-hidden="true"></i></a>
                        </router-link>
                        <router-link to="#" tag="li" class="nav-item" active-class="active">
                            <a class="dropdown-item">Налаштування <i class="fa fa-cog text-muted float-right" aria-hidden="true"></i></a>
                        </router-link>
                        <router-link to="#" tag="li" class="nav-item" active-class="active">
                            <a class="dropdown-item">Вийти <i class="fa fa-sign-out text-muted float-right" aria-hidden="true"></i></a>
                        </router-link>
    
    
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
    export default {
        name: "header-component"
    };
</script>

<style scoped>
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
</style>

