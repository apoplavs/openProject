<template>
  <div>
    <header-component/>
    <div :class="{'content-wrapper':  $route.fullPath !== '/' &&  $route.fullPath !== '/about', 'min-width': $route.fullPath === '/'}">
        <router-view  />
    </div>
    <footer-component/>
  </div>
</template>

<script>
    import HeaderComponent from './main/HeaderComponent.vue';
    import FooterComponent from './main/FooterComponent.vue';

    export default {
        name: "AppComponent",
        
        components: {
            HeaderComponent,
            FooterComponent
        },
    }
</script>

<style lang="scss">
    @import "../../sass/app.scss";
    .content-wrapper {
        min-height: 70vh;
    }
</style>
