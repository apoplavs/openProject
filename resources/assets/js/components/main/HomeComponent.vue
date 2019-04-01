
<template>
  <div class="welcome-wrap w-100">
    <div id="welcome-page" class="pt-lg-5 nice-background">
      <div class="">
        <div class="text-center" id="title-label">
          <h1>ТОЕсуд</h1>
          <div>прозорий відкритий ефективний суд</div>
        </div>
      </div>
    </div>
    <div class="wrap-container services text-center">
      <div class="h2">Можливості</div>
      <div class="row mt-5">
        <div class="court-practice col-4 text-center">
          <i class="fas fa-pie-chart"></i>
          <i class="fas fa-chart-bar"></i>
          <i class="fas fa-chart-area"></i>
          <div class="service-description">Статистика судової практики судді</div>
        </div>
        <div class="court-session col-4 text-center">
          <i class="fas fa-poll"></i>
          <i class="fas fa-balance-scale"></i>
          <i class="fas fa-poll"></i>
          <div class="service-description">Порівняння показників статистики суддів</div>
        </div>
        <div class="judges-rating col-4 text-center">
          <i class="fas fa-thumbs-up"></i>
          <i class="fas fa-user-check"></i>
          <i class="fas fa-thumbs-down"></i>
          <div class="service-description">Соціальний рейтинг суддів</div>
        </div>
      </div>

      <div class="row mt-5">
        <div class="current-status col-6 text-center">
          <i class="fas fa-briefcase"></i>
          <i class="fas fa-umbrella-beach"></i>
          <i class="fas fa-user-tie"></i>
          <i class="fas fa-calendar-minus-o"></i>
          <i class="fas fa-medkit"></i>
          <div class="service-description">Поточний статус судді</div>
        </div>
        <div class="court-session col-6 text-center">
          <i class="fas fa-gavel"></i>
          <div class="service-description">Роботa з судовими засіданнями</div>
        </div>
      </div>
    </div>

    <div class="nice-background"></div>
  </div>
</template>

<script>
export default {
  name: "HomeComponent"
};
</script>

<style lang='scss' scoped>
@import "../../../sass/_variables.scss";

.home {
  height: calc(100vh - 60px);
}
/* styles for welcome page */

.service-description {
  font-size: 1.2rem;
  margin-top: 20px;
}
.court-practice i,
.fa-gavel {
  font-size: 3rem;
  position: relative;
}
.court-practice i.fa-chart-bar {
  bottom: 50px;
}
.court-practice i.fa-pie-chart {
  left: 20px;
}
.court-practice i.fa-chart-area {
  right: 20px;
}
.fa-thumbs-up {
  color: green;
}
.fa-thumbs-down {
  color: red;
}

.judges-rating .fa-user-check,
.fa-balance-scale {
  font-size: 3rem;
  position: relative;
}
.fa-thumbs-up,
.fa-thumbs-down,
.fa-briefcase,
.fa-umbrella-beach,
.fa-calendar-minus-o,
.fa-medkit {
  position: relative;
  bottom: 40px;
}
i.fa, i.fas {
  color: $text-color;
}


.fa-user-tie {
  font-size: 3rem;
}
.services {
  padding-top: 20px;
  padding-bottom: 20px;
}
.services .h2 {
  font-weight: 300;
  font-size: 3rem;
}

.nice-background {
  background-color: #4686a08f;
  background-image: linear-gradient(
    to bottom,
    rgb(48, 129, 207) 0%,
    rgb(130, 219, 133) 100%
  );
}

#welcome-page {
  height: 100%;
  width: 100%;
  background-attachment: fixed, fixed, fixed;
  /* background-position: top left, center center, center center;
        background-size: auto, cover, cover; */
  overflow: hidden;
}

#title-label {
  font-family: BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial,
    sans-serif;
  color: white;
  -moz-user-select: none;
  -khtml-user-select: none;
  user-select: none;
  margin-bottom: 50px;
}

#title-label h1 {
  font-size: 7em;
  animation-name: title1;
  animation-duration: 1s;
}

#title-label div {
  font-size: 2.2em;
  text-decoration: overline;
  margin-top: -17px;
  animation-name: title2;
  animation-duration: 1s;
}

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
    font-size: 1.6em;
  }
  40% {
    color: rgba(255, 255, 255, 0.5);
    font-size: 1.7em;
  }
  50% {
    color: rgba(255, 255, 255, 0.6);
    font-size: 1.8em;
  }
  60% {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.9em;
  }
  70% {
    color: rgba(255, 255, 255, 0.7);
    font-size: 2em;
  }
  80% {
    color: rgba(255, 255, 255, 0.8);
    font-size: 2.1em;
  }
  90% {
    color: rgba(255, 255, 255, 0.9);
    font-size: 2.2em;
    text-decoration: none;
  }
}
.nice-background {
  height: 350px;
}
.wrap-container {
  width: 1149px;
  margin: auto;
}
</style>
