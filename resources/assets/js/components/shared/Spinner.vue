<template>
  <div class="spinner">
    <moon-loader :color="color" :size="size"/> 
  </div>
</template>

<script>
import MoonLoader from 'vue-spinner/src/MoonLoader.vue';

export default {
  name: 'Spinner',
  components: {
    MoonLoader
  },
  data() {
    return {
      color: '#2b989b',
      size: '70px'
    }
  },
};
</script>
<style scoped lang="scss">
    @import "../../../sass/_variables.scss";
    @import "../../../sass/_mixins.scss";

    .spinner {
        min-height: 400px;
        width: 100%;
        height: 100%;
        @include alignElement($justifyContent: center);
    }
    
</style>