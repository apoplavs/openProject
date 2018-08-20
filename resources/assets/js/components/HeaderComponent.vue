
<template>

    <nav class="navbar navbar-expand-lg navbar-light bg-light z-depth-3">
        <a class="navbar-brand" href="#"><img src="../../../../public/img/logo.png" width="40" alt="logo"/>ТОЕсуд</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
            <ul class="navbar-nav">
                <router-link to="/" tag="li" class="nav-item">
                    <a class="nav-link">На головну</a>
                </router-link>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                <li class="nav-item ">
                    <a class="nav-link disabled" href="#">Про нас</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link disabled" href="#">Контакти</a>
                </li>

                <!-- Authentication Links -->
                <div class="mx-5"></div>

                <router-link to="/login" tag="li" class="nav-item">
                    <a class="nav-link">Вхід</a>
                </router-link>
                <router-link to="/registration" tag="li" class="nav-item">
                    <a class="nav-link">Реєстрація</a>
                </router-link>
                <!----------------------->

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" id="dropdown-menu" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                        Таня <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right mx-3 px-2">
                        <li>
                            <span class="text-muted">
                                 email
                            </span>
                        </li>

                        <hr class="my-2">

                        <a href="#">
                            <li>
                                Особистий кабінет <i class="fa fa-home text-muted float-right" aria-hidden="true"></i>
                            </li>
                        </a>

                        <hr class="my-2">

                        <a href="#">
                            <li>
                                Налаштування <i class="fa fa-cog text-muted float-right" aria-hidden="true"></i>
                            </li>
                        </a>

                        <hr class="my-2">

                        <li>
                            <a href="#">
                                Вийти <i class="fa fa-sign-out text-muted float-right" aria-hidden="true"></i>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>



        </div>
    </nav>



</template>

<script>
    export default {
        name: 'header-component'
    }

</script>
<style scoped>
    .navbar-light {
        box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.2), 0 4px 15px 0 rgba(0, 0, 0, 0.19);
    }
    ul li a:hover{
        color: #2b989b!important;
        cursor: pointer;
    }
    ul li a i{
        font-size: 1.1em;
    }


</style>

