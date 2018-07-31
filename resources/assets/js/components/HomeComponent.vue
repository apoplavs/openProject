
<template>
    <div>
        <header-component></header-component>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">Main Component</div>

                        <div class="panel-body">
                            I'm an example component!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>

    // var headerComponent = require('./HeaderComponent.vue');
    export default {
        name: 'home-component',
        components: {
            // HeaderComponent
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
