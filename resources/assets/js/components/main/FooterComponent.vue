<template>
  <div class="footer">
    <!-- Footer -->
    <footer class="page-footer font-small unique-color-dark">

       <hr>

        <!-- Footer Links -->
        <div class="container text-center text-md-left mt-5">

          <!-- Grid row -->
          <div class="row mt-3">

            <!-- Grid column -->
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">

              <!-- Content -->
              <h6 class="text-uppercase font-weight-bold">Open Project</h6>
              <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
              <p>Інформаційна платформа, де відображені показники діяльності судової системи за окремим суддею на основі відкритих даних ЄДРСРУ.</p>

            </div>
            <!-- Grid column -->

            <!-- Grid column -->
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">

              <!-- Links -->
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
            <!-- Grid column -->

            <!-- Grid column -->
            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">

              <!-- Links -->
              <h6 class="text-uppercase font-weight-bold">Корисні посилання</h6>
              <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
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
            <!-- Grid column -->

            <!-- Grid column -->
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">

              <!-- Links -->
              <h6 class="text-uppercase font-weight-bold">Контакти</h6>
              <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block mx-auto" style="width: 60px;">
              <p>
                <i class="fas fa-envelope mr-3"></i> toecyd@gmail.com</p>
              <p>
                <i class="fas fa-phone mr-3"></i> + 38 097 702 3034</p>

            </div>
            <!-- Grid column -->

          </div>
          <!-- Grid row -->

        </div>
        <!-- Footer Links -->

        <!-- Copyright -->
        <div class="footer-copyright text-center py-3">© 2018 - 2019 Copyright:
          <a href="https://toecyd.top"> Open Project</a>
          &nbsp; 
          <!-- Facebook -->
          <a class="fb-ic" href="https://www.facebook.com/TOEcyd/" target="_blank">
            <i class="fab fa-facebook-f white-text mr-4"> </i>
          </a>
        </div>
        <!-- Copyright -->

      </footer>
      <!-- Footer -->
  </div>
</template>

<script>
  export default {
    name: "footer-component",
    data() {
      return {};
    },
    computed: {
    },
    methods: {
    }
  };
</script>

<style scoped lang="scss">
  @import "../../../sass/_variables.scss";
  @import "../../../sass/_mixins.scss";
  .footer {
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
  }
</style>
