<template>
<div>
    <header-component></header-component>



</div>

</template>

<script>
    import HeaderComponent from './HeaderComponent.vue';
    import HomeComponent from './HomeComponent.vue';


    export default {
        name: "AppComponent",

        components: [
            HeaderComponent,
            HomeComponent
        ]
    }
</script>

<style lang="scss">
    @import "../../sass/app.scss";

</style>
