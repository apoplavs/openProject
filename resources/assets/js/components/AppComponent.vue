<template>
  <div>
    <header-component/>
    <router-view></router-view>
    <!--<footer-component></footer-component>-->
  </div>

</template>

<script>
    import HeaderComponent from './HeaderComponent.vue';
    // import FooterComponent from './FooterComponent.vue';



    export default {
        name: "app-component",

        components: [
            HeaderComponent,
            // FooterComponent,
        ]
    }
</script>

<style lang="scss">
    @import "../../sass/app.scss";
    @import "../../sass/_variables.scss";

</style>
