<template>
  <div>
    <header-component/>
    <div class="content-wrapper">
        <router-view  />
    </div> 
  </div>
</template>

<script>
    import HeaderComponent from './main/HeaderComponent.vue';
    // import FooterComponent from './FooterComponent.vue';

    export default {
        name: "AppComponent",
        
        components: {
            HeaderComponent,
            // FooterComponent,
        },
    }
</script>

<style lang="scss">
    @import "../../sass/app.scss";
</style>
