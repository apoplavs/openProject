<template>
  <div>
    <header-component/>
    <keep-alive>
        <router-view></router-view>
    </keep-alive>
    <!--<footer-component></footer-component>-->
  </div>
</template>

<script>
    import HeaderComponent from './main/HeaderComponent.vue';
    // import FooterComponent from './FooterComponent.vue';

    export default {
        name: "app-component",
        
        components: {
            HeaderComponent,
            // FooterComponent,
        },
        // Мы перехватываем вызов axios, чтобы определить, получим ли мы 401 Unauthorizedответ
        //   created: function () {
        //     this.$http.interceptors.response.use(undefined, function (err) {
        //     return new Promise(function (resolve, reject) {
        //         if (err.status === 401 && err.config && !err.config.__isRetryRequest) {
        //         this.$store.dispatch(logout)
        //         }
        //         throw err;
        //     });
    // });
//   }
    }
</script>

<style lang="scss">
    @import "../../sass/app.scss";
    

</style>
