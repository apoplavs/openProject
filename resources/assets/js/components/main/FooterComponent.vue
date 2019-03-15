<template>
  <div class="footer">
    <footer class="page-footer font-small unique-color-dark">
       <hr>
        <div class="foter-container text-left mt-5">
          <div class="row m-3">
            <div class="col-3  mb-4">
              <h6 class="text-uppercase font-weight-bold">Open Project</h6>
              <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
              <p>Інформаційна платформа, де відображені показники діяльності судової системи за окремим суддею на основі відкритих даних ЄДРСРУ.</p>
            </div>
            <div class="col-2  mb-4">
              <h6 class="text-uppercase font-weight-bold">Можливості</h6>
              <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
              <p>
                <router-link to="/about">Про нас</router-link>
              </p>
              <p>
                <router-link to="/judges">Статистика Cуддів</router-link>
              </p>
              <p>
                <router-link to="/courts">Статистика Судів</router-link>
              </p>
            </div>
            <div class="col-3 mb-4">
              <h6 class="text-uppercase font-weight-bold">Корисні посилання</h6>
              <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block" style="width: 60px;">
              <p>
                <router-link to="/privacy-policy" target="_blank">
                  <a>Політика конфіденційності</a>
                </router-link>
              </p>
              <p>
                <router-link to="/user-agreement" target="_blank">
                    <a>Угода користувача</a>
                  </router-link>
              </p>
            </div>
            <div class="col-4 mb-4">
              <h6 class="text-uppercase font-weight-bold">Контакти</h6>
              <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block " style="width: 60px;">
              <p>
                <i class="fas fa-envelope mr-2"></i> <a href="mailto:toecyd@gmail.com">toecyd@gmail.com</a></p>
              <p>
                <i class="fas fa-phone mr-2"></i> <a href="tel:+380977023034">+ 38 097 702 3034</a></p>
            </div>
          </div>
        </div>
        <div class="footer-copyright text-center py-3">© 2018 - 2019 Copyright:
          <a href="https://toecyd.top"> Open Project</a>
          &nbsp; 
          <a class="fb-ic" href="https://www.facebook.com/TOEcyd/" target="_blank">
            <i class="fab fa-facebook-f white-text mr-4"> </i>
          </a>
        </div>
      </footer>
  </div>
</template>

<script>
  export default {
    name: 'footer-component'
  };
</script>

<style scoped lang="scss">
  @import "../../../sass/_variables.scss";
  @import "../../../sass/_mixins.scss";
  .footer {
    background: rgba(0,0,0,.68);
    color: #afafaf;
    font-weight: 300;
    line-height: 1.5;
    font-size: 14px;
    h6 {
      font-weight: normal !important ;
      font-size: .9rem !important;
    }
    a {
      color: $main-color;
    }
    .foter-container,
    .footer-copyright {
      width: 1140px;
      margin: auto;
    }
  }
</style>
