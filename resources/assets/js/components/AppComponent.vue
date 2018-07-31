<template>
<div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light z-depth-3">
        <a href="{{ url('/') }}" class="navbar-brand p-0 m-0">
            <img src="{{ asset('img/logo.png') }}" width="40" alt="logo">
            {{ config('app.name', 'ТОЕсуд') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav" id="top-navbar">
                <li class="nav-item active">
                    <a class="nav-link" href="{{ url('/') }}">На головну<span class="sr-only">(поточна)</span></a>
                </li>
                <li class="nav-item dropdown active">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown06" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Рейтинг
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdown06">
                        <a class="dropdown-item" href="{{ url('/judges') }}">Судді</a>
                        <a class="dropdown-item disabled" href="#">Суди <small>( <i class="fa fa-code" aria-hidden="true"></i> в розробці)</small></a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('about') }}">Про нас</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Контакти</a>
                </li>

                <!-- Authentication Links -->
                <div class="mx-5"></div>
                @guest
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('login') }}">Вхід</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="{{ route('register') }}">Рєстрація</a>
                </li>
                @else
                <li class="nav-item dropdown pr-4 mr-5 active">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" id="dropdown-menu" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right px-1">
                        <li>
                                    <span class="text-muted">
                                        {{ Auth::user()->email }}
                                    </span>
                        </li>

                        <hr class="my-2">

                        <a href="{{ url('/home') }}">
                            <li>
                                Особистий кабінет <i class="fa fa-home text-muted float-right" aria-hidden="true"></i>
                            </li>
                        </a>

                        <hr class="my-2">

                        <a href="{{ route('settings') }}">
                            <li>
                                Налаштування <i class="fa fa-cog text-muted float-right" aria-hidden="true"></i>
                            </li>
                        </a>

                        <hr class="my-2">

                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                Вийти <i class="fa fa-sign-out text-muted float-right" aria-hidden="true"></i>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </li>
                @endguest
            </ul>
        </div>
    </nav>

</div>

</template>

<script>
    export default {
        name: "AppComponent"
    }
</script>

<style scoped>

</style>
