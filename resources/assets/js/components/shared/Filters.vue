<template>
  <div class="card">
    <div class="card-header">
      <div>
        <i class="fa fa-filter" aria-hidden="true"></i>
        <span>Фільтри</span>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-12">
          <h6>Інстанція</h6>
          <ul class="list-unstyled mb-0">
            <li>
              <label>
                <input type="checkbox" value="3" name="instances" v-model="filters.instances">
                <span class="checkmark"></span> Перша
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="2" name="instances" v-model="filters.instances">
                <span class="checkmark"></span> Апеляційна
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="1" name="instances" v-model="filters.instances">
                <span class="checkmark"></span> Касаційна
              </label>
            </li>
          </ul>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-lg-12">
          <h6>Юрисдикція</h6>
          <ul class="list-unstyled mb-0">
            <li>
              <label>
                <input
                  type="checkbox"
                  value="3"
                  name="jurisdictions"
                  v-model="filters.jurisdictions"
                >
                <span class="checkmark"></span> Господарська
              </label>
            </li>
            <li>
              <label>
                <input
                  type="checkbox"
                  value="2"
                  name="jurisdictions"
                  v-model="filters.jurisdictions"
                >
                <span class="checkmark"></span> Адміністративна
              </label>
            </li>
            <li>
              <label>
                <input
                  type="checkbox"
                  value="1"
                  name="jurisdictions"
                  v-model="filters.jurisdictions"
                >
                <span class="checkmark"></span> Загальна
              </label>
            </li>
          </ul>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-lg-12">
          <h6>Регіон суду</h6>
          <ul class="list-unstyled mb-0">
            <li>
              <label>
                <input type="checkbox" value="2" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Вінницька
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="3" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Волинська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="4" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Дніпропетровська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="5" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Донецька
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="6" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Житомирська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="7" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Закарпатська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="8" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Запорізька
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="9" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Івано-Франківська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="10" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Київська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="11" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Кіровоградська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="12" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Луганська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="13" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Львівська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="14" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Миколаївська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="15" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Одеська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="16" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Полтавська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="17" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Рівненська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="18" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Сумська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="19" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Тернопільська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="20" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Харківська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="21" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Херсонська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="22" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Хмельницька
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="23" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Черкаська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="24" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Чернівецька
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="25" name="region" v-model="filters.regions">
                <span class="checkmark"></span> Чернігівська
              </label>
            </li>
            <li>
              <label>
                <input type="checkbox" value="26" name="region" v-model="filters.regions">
                <span class="checkmark"></span> м. Київ
              </label>
            </li>
          </ul>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-lg-12">
          <ul class="list-unstyled mb-0">
            <li>
              <label>
                <input type="checkbox" value="1" v-model="filters.expired">
                    <span class="checkmark"></span>Закінчилися повноваження
              </label>
              {{filters.expired}}
            </li>
          </ul>
        </div>
      </div>

      <div class="apply-filters" id="apply-filters">
        <hr>
        <div class="row">
          <div class="col-6">
            <button type="reset" @click="resetFilters()" class="btn btn-secondary">Скинути</button>
          </div>
          <div class="col-6">
            <button type="button" @click="setFilters()" class="btn btn-info">Показати</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Filters",
  props: {
    filters: Object
  },
  methods: {
    resetFilters() {
      window.scrollTo(0, 0);
      this.filters.regions = [];
      this.filters.instances = [];
      this.filters.jurisdictions = [];
      this.filters.expired = 1;
      this.filters.search = null;
      this.filters.sort = 1;
      this.$emit("resetFilters");
    },
    setFilters() {
      this.$emit("setFilters");
    }
  }
};
</script>

<style lang="scss" scoped>
@import "../../../sass/judges_coutrs_list.scss";
</style>


