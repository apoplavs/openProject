
<template>
    <div>
        <div id="welcome-page" class="pt-lg-5">
            <div class="col-12 text-center" id="title-label">
                <h1>ТОЕсуд</h1>
                <div>робимо суд прозорішим</div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        name: 'home-component',
    
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
  @import "../../../sass/_variables.scss";

    /* styles for welcome page */
    
    #welcome-page {
       height: 100vh;
    width: 100%;
    background-color: #4686a08f;
    background-attachment: fixed, fixed, fixed;
    background-image: linear-gradient(to bottom, rgb(48, 129, 207) 0%, rgb(130, 219, 133) 100%);
    /* background-position: top left, center center, center center;
    background-size: auto, cover, cover; */
    overflow: hidden;
    }
    
    #title-label {
        font-family: BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        color: white;
    }
    
    #title-label h1 {
        font-size: 7em;
        animation-name: title1;
        animation-duration: 1s;
    }
    
    #title-label div {
        font-size: 2.8em;
        text-decoration: overline;
        margin-top: -22px;
        animation-name: title2;
        animation-duration: 1s;
    }
    
    
    /* Анімація для welcome.blade.php титульного надпису */
    
    @keyframes title1 {
        0% {
            color: rgba(255, 255, 255, 0.1);
            font-size: 4.5em;
        }
        10% {
            color: rgba(255, 255, 255, 0.2);
            font-size: 5em;
        }
        20% {
            color: rgba(255, 255, 255, 0.3);
            font-size: 5.4em;
        }
        30% {
            color: rgba(255, 255, 255, 0.4);
            font-size: 5.7em;
        }
        40% {
            color: rgba(255, 255, 255, 0.5);
            font-size: 6em;
        }
        50% {
            color: rgba(255, 255, 255, 0.6);
            font-size: 6.3em;
        }
        60% {
            color: rgba(255, 255, 255, 0.7);
            font-size: 6.5em;
        }
        70% {
            color: rgba(255, 255, 255, 0.7);
            font-size: 6.7em;
        }
        80% {
            color: rgba(255, 255, 255, 0.8);
            font-size: 6.8em;
        }
        90% {
            color: rgba(255, 255, 255, 0.9);
            font-size: 6.9em;
        }
    }
    
    @keyframes title2 {
        0% {
            color: rgba(255, 255, 255, 0.1);
            font-size: 1em;
            text-decoration: none;
        }
        10% {
            color: rgba(255, 255, 255, 0.2);
            font-size: 1.3em;
        }
        20% {
            color: rgba(255, 255, 255, 0.3);
            font-size: 1.5em;
        }
        30% {
            color: rgba(255, 255, 255, 0.4);
            font-size: 1.7em;
        }
        40% {
            color: rgba(255, 255, 255, 0.5);
            font-size: 1.9em;
        }
        50% {
            color: rgba(255, 255, 255, 0.6);
            font-size: 2.1em;
        }
        60% {
            color: rgba(255, 255, 255, 0.7);
            font-size: 2.3em;
        }
        70% {
            color: rgba(255, 255, 255, 0.7);
            font-size: 2.5em;
        }
        80% {
            color: rgba(255, 255, 255, 0.8);
            font-size: 2.6em;
        }
        90% {
            color: rgba(255, 255, 255, 0.9);
            font-size: 2.7em;
            text-decoration: none;
        }
    }
</style>
