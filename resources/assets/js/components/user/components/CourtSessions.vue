<template>
  <div class="courtSessions">
    <div class="card mt-2">
      <div class="card-header d-flex justify-content-between">
        <span>Найближчі судові засідання</span>
        <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="search">
      </div>
      <div class="card-body court-sessions-container">
        <div class="court-sessions">
          <div v-if="true" class="container-component">
            <div class="row header text-muted">
              <div class="col-1 pl-0">Дата розгляду</div>
              <div class="col-1">Номер справи</div>
              <div class="col-2">Судді</div>
              <div class="col-2">Форма</div>
              <div class="col-3">Сторони у справі</div>
              <div class="col-2">Суть справи</div>
              <div class="col-1 pr-0">Примітки</div>
            </div>
            <div class="row">
              <div class="col-1 pl-0">
                <div>25.09.2018</div>
              </div>
              <div class="col-1">923/623.10</div>
              <div class="col-2">Господарський суд Херсонської області Немченко Л.M.</div>
              <div class="col-1">Aміністративна</div>
              <div class="col-3">
                Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод,
                Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод
              </div>
              <div class="col-2">
                Позивач: Головня Максим Феодосієвич, відповідач: Публічне Акціонерне Товариство Херсонський Суднобудівний завод,
                Позивач: Головня Максим Феодосієвич
              </div>
              <div class="col-2 pr-0 text-center">
                <i class="fas fa-star" @click="delBookmarkCourtSession(session)"></i>
                <textarea class="note" maxlength="254"></textarea>
                <i class="icon-ok"></i>
              </div>
            </div>
          </div>
          <div v-else class="container-component">
            <p>Нічого не знайдено...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "CourtSessions",
  data() {
    return {
      courtSessions: {},
      filterSessions: null,
      search: ""
    };
  }
  // beforeMount() {
  //     if (this.isAuth) {
  //         axios
  //             .get(`/api/v1/judges/${this.$route.params.id}`, {
  //                 headers: {
  //                     "Content-Type": "application/json",
  //                     "X-Requested-With": "XMLHttpRequest",
  //                     "Authorization": localStorage.getItem('token')
  //                 },
  //             })
  //             .then(response => {
  //                 this.judge = response.data;
  //                 this.loadData = true;
  //                 console.log('JUdge PROFILE', this.judge);

  //             })
  //             .catch(error => {
  //                 if (error.response.status === 401) {
  //                     this.$router.push('/login');
  //                 }
  //                 console.log('error');
  //             });
  //     } else {
  //         axios
  //             .get(`/api/v1/guest/judges/${this.$route.params.id}`, {
  //                 headers: {
  //                     "Content-Type": "application/json",
  //                     "X-Requested-With": "XMLHttpRequest",
  //                 },
  //             })
  //             .then(response => {
  //                 this.judge = response.data;
  //                 this.loadData = true;
  //             })
  //             .catch(error => {
  //                 if (error.response.status === 401) {
  //                     this.$router.push('/login');
  //                 }
  //                 console.log('error');
  //             });

  //     }
  // }
};
</script>

<style scoped lang="scss">
@import "../../../../sass/_variables.scss";
.courtSessions {
  width: 100%;
  height: auto;
  margin-top: 50px;
  font-size: 0.8rem;
  .infoCard {
    padding: 20px;
    > p:first-child {
      font-size: 1.1rem;
    }
    
  }
  .fa-star {
        color: $main-color;
        cursor: pointer;
        font-size: 15px;
        float: right;
        padding: 0 10px 0 10px;
    }
  input[type="search"] {
    width: 200px;
  }
  .note {
    height: 100%;
    width: 100%;
    border: none;
    resize: none;
    background-color: #fafa599c;
    padding: 10px;
    &:before {
        content: '';
        position: absolute;
        top: 0; right: 0;
        border-top: 80px solid white;
        border-left: 80px solid #fafa599c;
        width: 0;
    }
  }

  .container-component {
    padding: 20px;
    background-color: #ffffff;
  }

  .row {
    margin: 0;
    padding: 15px 0;
    &:not(:last-child) {
      border-bottom: 1px solid $text-muted;
    }
  }
}
</style>