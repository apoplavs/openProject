<template>
  <div class="courtSessions">
    <spinner v-show="!loadData" />
    <div v-show="loadData" class="card mt-2">
      <div class="card-header d-flex justify-content-between">
        <span>Cудові засідання</span>
        <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="search">
      </div>
      <div class="card-body court-sessions-container">
        <div class="court-sessions">
          
            <div v-if="filterSessions.length > 0" class="container-component">
              <div class="row header">
                <div class="col-1 pl-0">Дата розгляду</div>
                <div class="col-1">Номер справи</div>
                <div class="col-2">Судді</div>
                <div class="col-1">Форма</div>
                <div class="col-3">Сторони у справі</div>
                <div class="col-2">Суть справи</div>
                <div class="col-2 pr-0">Примітки</div>
              </div>
              <!-- <transition name='fade'> -->
                <div class="row" v-for="(session, i_el) in filterSessions" :key="i_el + 'A'">
                  <!-- <transition name='fade'> -->
                  <div class="col-1 pl-0">
                    <div>{{ session.date }}</div>
                  </div>
                  <div class="col-1">{{ session.number }}</div>
                  <div class="col-2">{{ session.judges }}</div>
                  <div class="col-1">{{ session.forma }}</div>
                  <div class="col-3">{{ session.involved }}</div>
                  <div class="col-2">{{ session.description }}</div>
                  <div class="col-2 pr-0 text-center position-relative">
                    <i class="fas fa-star" @click="showModalDelete(session)"></i>
                    <textarea class="note" maxlength="254"></textarea>
                    <img class="checkmark" src="../../../../images/checkmark.png"/>
                  </div>
                  <!-- </transition> -->
                </div>
              <!-- </transition> -->
            </div>
          <div v-else class="container-component">
            <p>Нічого не знайдено...</p>
          </div>
         
        </div>
      </div>
    </div>
    <!-- modal confirm -->
    <modal v-show="showModalConfirm" @close="showModalConfirm = false" @confirm="deleteBookmarkCourtSession()" :modalConfirm="true" >
        <div slot="header"> </div>
        <div slot="body" style="text-align: center; font-size: 16px;">
           Ви впевнені, що хочете видалити закладку?
        </div>
    </modal>
  </div>
</template>

<script>
import Modal from "../../shared/Modal.vue";
import Spinner from "../../shared/Spinner.vue";
export default {
  name: "CourtSessions",
  components: {
    Modal,
    Spinner
  },
  data() {
    return {
      courtSessions: [],
      search: '',
      showModalConfirm: false,
      deleteSession: null,
      loadData: false
    };
  },
  computed: {
    filterSessions() {
      //  живий пошук = фільтер
      return _.filter(this.courtSessions, el => {
        let arr = _.filter(Object.keys(el), key => {
          let regEx = new RegExp(`(${this.search})`, "i");
          return regEx.test(el[key]) || this.search.length == 0;
        });
        return arr.length > 0 ? true : false;
      });
    },
    isAuth: () => {
      return localStorage.getItem("token");
    }
  },
  beforeMount() {
    if (this.isAuth) {
      axios
        .get(`/api/v1/court-sessions/bookmarks`, {
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
        .then(response => {
          this.courtSessions = response.data;
          this.loadData = true;
          console.log("User profile CourtSessions", this.courtSessions);
        })
        .catch(error => {
          if (error.response.status === 401) {
            this.$router.push("/login");
          }
          console.log("error");
        });
    } else {
      this.$router.push("/login");
    }
  },
  methods: {
    showModalDelete(session) {
      this.showModalConfirm = true;
      this.deleteSession = session;
    },

    deleteBookmarkCourtSession() { 
      this.showModalConfirm = false;
      if (!this.isAuth) {
        this.$router.push("/login");
      } else {
        this.loadData = false;
        axios({
          method: "delete",
          url: `/api/v1/court-sessions/${this.deleteSession.id}/bookmark`,
          headers: {
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Authorization: localStorage.getItem("token")
          }
        })
          .then(response => {
            let index;
            this.courtSessions.forEach( (el, i) => {
              if (this.deleteSession.id === el.id){
                index = i;
              }
            });            
            if (index >= 0) {   
              this.courtSessions.splice(index, 1);
            }
            this.deleteSession = null;
            this.loadData = true;
          })
          .catch(error => {
            if (error && error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
          });
      }
    }
  }
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
  .header {
    font-size: .9rem;
    font-weight: 700;
  }
  .fa-star {
    color: $main-color;
    cursor: pointer;
    font-size: 20px;
    float: right;
    padding: 0 0px 10px 10px;
  }
  input[type="search"] {
    width: 200px;
  }
  .note {
    height: calc(100% - 32.5px);
    width: 100%;
    border: none;
    resize: none;
    background-color: #fafa599c;
    padding: 15px 10px 50px 10px;
  }
  textarea.note:before {
    content: '';
    position: absolute;
    top: 0; right: 0;
    border-top: 80px solid white;
    border-left: 80px solid rgba(0,0,0,0);
    width: 0;
}
  .checkmark {
    width: 40px;
    position: absolute;
    bottom: 5px;
    right: 0;
    cursor: pointer;
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