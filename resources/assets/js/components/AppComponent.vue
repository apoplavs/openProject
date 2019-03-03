<template>
  <div>
    <header-component/>
    <router-view class="content" />
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
    .content {
        min-width: 1140px;
    }
</style>
