
<template>

    <nav class="navbar navbar-expand-lg navbar-light bg-light z-depth-3">
        <a class="navbar-brand" href="#"><img src="../../../../public/img/logo.png" width="40" alt="logo"/>ТОЕсуд</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
            <ul class="navbar-nav">
                <router-link to="/home" tag="li" class="nav-item">
                    <a class="nav-link">На головну</a>
                </router-link>

                <!--<li class="nav-item">-->
                    <!--<a class="nav-link" href="#">На головну</a>-->
                <!--</li>-->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Рейтинг
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <router-link to="/passport-clients">
                            <a class="dropdown-item">Судді</a>
                        </router-link>
                        <router-link to="#">
                            <a class="dropdown-item disabled">Суди<small>( <i class="fa fa-code" aria-hidden="true"></i> в розробці)</small></a>
                        </router-link>
                        <!--<a class="dropdown-item" href="#">Судді</a>-->
                        <!--<a class="dropdown-item disabled" href="#">Суди<small>( <i class="fa fa-code" aria-hidden="true"></i> в розробці)</small></a>-->
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

                <li class="nav-item">
                    <a class="nav-link" href="#">Вхід</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Рєстрація</a>
                </li>
                <!----------------------->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Dropdown
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </li>
                <!--<li class="nav-item dropdown">-->
                    <!--<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" id="dropdown-menu" role="button" aria-expanded="false" aria-haspopup="true" v-pre>-->
                        <!--Таня <span class="caret"></span>-->
                    <!--</a>-->
                    <!--<ul class="dropdown-menu dropdown-menu-right">-->
                        <!--<li>-->
                            <!--<span class="text-muted">-->
                                 <!--email-->
                            <!--</span>-->
                        <!--</li>-->

                        <!--<hr class="my-2">-->

                        <!--<a href="#">-->
                            <!--<li>-->
                                <!--Особистий кабінет <i class="fa fa-home text-muted float-right" aria-hidden="true"></i>-->
                            <!--</li>-->
                        <!--</a>-->

                        <!--<hr class="my-2">-->

                        <!--<a href="#">-->
                            <!--<li>-->
                                <!--Налаштування <i class="fa fa-cog text-muted float-right" aria-hidden="true"></i>-->
                            <!--</li>-->
                        <!--</a>-->

                        <!--<hr class="my-2">-->

                        <!--<li>-->
                            <!--<a href="#">-->
                                <!--Вийти <i class="fa fa-sign-out text-muted float-right" aria-hidden="true"></i>-->
                            <!--</a>-->
                        <!--</li>-->
                    <!--</ul>-->
                <!--</li>-->
            </ul>


            
        </div>
    </nav>




    <!--<nav class="navbar navbar-expand-lg navbar-light bg-light z-depth-3">-->
        <!--<a href="../../../../public/img/logo.png" class="navbar-brand p-0 m-0">-->
            <!--<img src="" width="40" alt="logo">-->
            <!--ТОУсуд-->
        <!--</a>-->
        <!--<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">-->
            <!--<span class="navbar-toggler-icon"></span>-->
        <!--</button>-->
        <!--<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">-->
            <!--<ul class="navbar-nav" id="top-navbar">-->
                <!--<li class="nav-item active">-->
                    <!--<a class="nav-link" href="/">На головну<span class="sr-only">(поточна)</span></a>-->
                <!--</li>-->
                <!--<li class="nav-item dropdown active">-->
                    <!--<a class="nav-link dropdown-toggle" href="#" id="dropdown06" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
                        <!--Рейтинг-->
                    <!--</a>-->
                    <!--<div class="dropdown-menu" aria-labelledby="dropdown06">-->
                        <!--<a class="dropdown-item" href="#">Судді</a>-->
                        <!--<a class="dropdown-item disabled" href="#">Суди <small>( <i class="fa fa-code" aria-hidden="true"></i> в розробці)</small></a>-->
                    <!--</div>-->
                <!--</li>-->
                <!--<li class="nav-item">-->
                    <!--<a class="nav-link" href="#">Про нас</a>-->
                <!--</li>-->
                <!--<li class="nav-item">-->
                    <!--<a class="nav-link" href="#">Контакти</a>-->
                <!--</li>-->

                <!--&lt;!&ndash; Authentication Links &ndash;&gt;-->
                <!--<div class="mx-5"></div>-->
                <!--&lt;!&ndash;@guest&ndash;&gt;-->
                <!--<li class="nav-item active">-->
                    <!--<a class="nav-link" href="#">Вхід</a>-->
                <!--</li>-->
                <!--<li class="nav-item active">-->
                    <!--<a class="nav-link" href="#">Рєстрація</a>-->
                <!--</li>-->
                <!--&lt;!&ndash;@else&ndash;&gt;-->
                <!--<li class="nav-item dropdown pr-4 mr-5 active">-->
                    <!--<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" id="dropdown-menu" role="button" aria-expanded="false" aria-haspopup="true" v-pre>-->
                        <!--Таня <span class="caret"></span>-->
                    <!--</a>-->
                    <!--<ul class="dropdown-menu dropdown-menu-right">-->
                        <!--<li>-->
                            <!--<span class="text-muted">-->
                                <!--{{ email }}-->
                            <!--</span>-->
                        <!--</li>-->

                        <!--<hr class="my-2">-->

                        <!--<a href="#">-->
                            <!--<li>-->
                                <!--Особистий кабінет <i class="fa fa-home text-muted float-right" aria-hidden="true"></i>-->
                            <!--</li>-->
                        <!--</a>-->

                        <!--<hr class="my-2">-->

                        <!--<a href="#">-->
                            <!--<li>-->
                                <!--Налаштування <i class="fa fa-cog text-muted float-right" aria-hidden="true"></i>-->
                            <!--</li>-->
                        <!--</a>-->

                        <!--<hr class="my-2">-->

                        <!--<li>-->
                            <!--<a href="#"-->
                               <!--onclick="event.preventDefault();-->
                                                 <!--document.getElementById('logout-form').submit();">-->
                                <!--Вийти <i class="fa fa-sign-out text-muted float-right" aria-hidden="true"></i>-->
                            <!--</a>-->
                            <!--<form id="logout-form" >-->

                            <!--</form>-->
                        <!--</li>-->
                    <!--</ul>-->
                <!--</li>-->
                <!--&lt;!&ndash;@endguest&ndash;&gt;-->
            <!--</ul>-->
        <!--</div>-->
    <!--</nav>-->





</template>

<script>
    export default {
        name: 'HeaderComponent'
    }

</script>
<style>

</style>

