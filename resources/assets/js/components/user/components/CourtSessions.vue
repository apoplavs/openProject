<template>
  <div class="courtSessions">
    <div class="card mt-2">
      <div class="card-header d-flex justify-content-between">
        <span> <i class="fa fa-bookmark" aria-hidden="true"></i>Закладки судових засідань</span>
        <input type="search" class="form-control" placeholder="Пошук..." v-model.trim="search">
      </div>
      <div class="card-body court-sessions-container">
        <spinner v-if="!loadData"/>
        <div
          v-if="loadData && !filterSessions.length"
          >За даними параметрами нічого не знайдено...
        </div>
        <div v-if="loadData" class="court-sessions">
          <div v-if="filterSessions.length" class="container-component">
            <div class="row header">
              <div class="col-1 pl-0">Дата розгляду</div>
              <div class="col-1">Номер справи</div>
              <div class="col-2">Судді</div>
              <div class="col-1">Форма</div>
              <div class="col-3">Сторони у справі</div>
              <div class="col-2">Суть справи</div>
              <div class="col-2 pr-0">Примітки</div>
            </div>
            <div class="row body" v-for="(session, i_el) in filterSessions" :key="i_el">
              <div class="col-1 pl-0">
                <div>{{ session.date }}</div>
              </div>
              <div class="col-1">{{ session.number }}</div>
              <div class="col-2">{{ session.judges }}</div>
              <div class="col-1">{{ session.forma }}</div>
              <div class="col-3">{{ session.involved }}</div>
              <div class="col-2">{{ session.description }}</div>
              <div class="col-2 pr-0 text-center position-relative note-wrap">
                <i class="fas fa-star" @click="showModalDelete(session)"></i>
                <textarea class="note" maxlength="254" v-model.trim="session.note"></textarea>
                <img class="checkmark" src="../../../../images/checkmark.png" @click="saveNote(session)">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- modal confirm -->
    <modal
      v-show="showModalConfirm"
      @close="showModalConfirm = false"
      @confirm="deleteBookmark"
      :modalConfirm="true"
    >
      <div slot="header"></div>
      <div
        slot="body"
        style="text-align: center; font-size: 16px;"
      >Ви впевнені, що хочете видалити закладку?</div>
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
  },
  beforeMount() {
    if (this.$store.getters.isAuth) {
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
          if (error.response && error.response.status === 401) {
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

    deleteBookmark() {
      this.showModalConfirm = false;
      if (!this.$store.getters.isAuth) {
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
            this.courtSessions = _.filter( this.courtSessions, el => {
                return this.deleteSession.id !== el.id
            });
            console.log("courtSessions", this.courtSessions);
            this.deleteSession = null;
            this.loadData = true;
          })
          .catch(error => {
            if (error && error.response && error.response.status === 401) {
              this.$router.push("/login");
            }
          });
      }
    },
    saveNote(session) {
      // якщо пуста строка передаємо null
      session.note = !session.note.length ? null : session.note;
      axios.post(`/api/v1/court-sessions/${session.id}/bookmark/note`, { 'note': session.note }, {
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          Authorization: localStorage.getItem("token")
        }
        }) .catch(error => {
        if (error && error.response && error.response.status === 401) {
          this.$router.push("/login");
        }
      })
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
  .card-header {
    .fa-bookmark {
      color: #ffffff;
      font-size: 1.4rem;
      margin-right: 15px;
    }
  }
  .infoCard {
    padding: 20px;
    > p:first-child {
      font-size: 1.1rem;
    }
  }
  .header {
    font-size: 0.9rem;
    font-weight: 700;
    align-items: center;
    line-height: 1.4;
  }
  .fa-star {
    color: $main-color;
    cursor: pointer;
    font-size: 15px;
    float: right;
    padding: 0 0px 0px 10px;
    margin-right: -10px;
  }
  input[type="search"] {
    width: 200px;
  }
  .note {
    height: calc(100% - 13.5px);
    width: 100%;
    border: none;
    resize: none;
    background-color: #fafa599c;
    padding: 3px 5px 15px 5px;
    font-size: 0.7rem;
    margin-top: -5px;
    color: #002366;
    font-style: italic;
    background: linear-gradient(-135deg, transparent 10px, #fafa599c 0);
  }
  textarea.note:before {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    border-top: 80px solid white;
    border-left: 80px solid rgba(0, 0, 0, 0);
    width: 0;
  }
  .checkmark {
    width: 25px;
    position: absolute;
    bottom: 5px;
    right: 0;
    cursor: pointer;
  }

  .container-component {
    padding: 0;
    background-color: #ffffff;
    .body {
      font-size: .9rem;
    }
  }

  .row {
    margin: 0;
    padding: 15px 0;
    &:not(:last-child) {
      border-bottom: 1px solid $text-muted;
    }
  }
  .note-wrap {
    min-height: 160px;
    max-height: 200px;
  }
  .col-1,
  .col-2,
  .col-3 {
    padding-right: 5px;
    padding-left: 5px;
  }
}
</style>