<template>
  <div>
    <div v-if="loadData && (!judgesList || judgesList.length == 0)">
      Не додано жодного суддю для порівняння</div>

    <div v-else-if="loadData">
      <table class="table content-wrapper">
        <thead>
        <tr>
          <th scope="col"></th>
          <th scope="col" v-for="(judge, index) of judgesList" :key="index">{{ judge.data.surname }} {{ (judge.data.name.length != 1) ? judge.data.name : judge.data.name + '.' }} {{ judge.data.patronymic.length != 1 ? judge.data.patronymic : judge.data.patronymic + '.' }} 
           <sup class="delete-comparation" @click="deleteJudgeFromComparation(judge.data.id)" title="Видалити з порівняння"> x </sup></th>
        </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="col" class="text-center" :colspan="judgesList.length + 1">Статистика розглянутих справ</th>
          </tr>

          <tr>
            <td>Цивільні</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.civil_statistic.amount }}</td>
          </tr>
          <tr>
            <td>Кримінальні</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.criminal_statistic.amount }}</td>
          </tr>
          <tr>
            <td>Справи про адмін. правопорушення</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.adminoffence_statistic.amount }}</td>
          </tr>
          <tr>
            <td>Адміністративні справи</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.admin_statistic.amount }}</td>
          </tr>
          <tr>
            <td>Господарські справи</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.commercial_statistic.amount }}</td>
          </tr>

          <tr>
            <th scope="col" class="text-center" :colspan="judgesList.length + 1">Загальна ефективність</th>
          </tr>
          <tr>
            <td>Відсоток рішень, що вистояли у вищих інстанціях</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.common_statistic.competence }}%</td>
          </tr>
          <tr>
            <td>Відсоток справ, що розглянуті у визначений законом строк</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.common_statistic.timeliness }}%</td>
          </tr>

          <!-- ЦИВІЛЬНЕ СУДОЧИНСТВО -->

          <tr>
            <th scope="col" class="text-center" :colspan="judgesList.length + 1">Цивільне судочинство</th>
          </tr>
          <tr>
            <td>У позові відмовлено повністю</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.civil_statistic.negative_judgment }}%</td>
          </tr>
          <tr>
            <td>Позов задоволено повністю</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.civil_statistic.positive_judgment }}%</td>
          </tr>
          <tr>
            <td>Позов задоволено частково або уладено мирову угоду</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.civil_statistic.other_judgment }}%</td>
          </tr>
          <tr>
            <td>Відсоток справ, що розглянуті у визначений законом строк</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.civil_statistic.cases_on_time }}%</td>
          </tr>
          <tr>
            <td>Відсоток рішень, що вистояли у вищих інстанціях</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.civil_statistic.approved_by_appeal }}%</td>
          </tr>
          <tr>
            <td>Середня тривалість розгляду однієї справи (днів)</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.civil_statistic.average_duration }} дн.</td>
          </tr>

            <!-- КРИМІНАЛЬНЕ СУДОЧИНСТВО -->
          <tr>
            <th scope="col" class="text-center" :colspan="judgesList.length + 1">Кримінальне судочинство</th>
          </tr>
          <tr>
            <td>В результаті провадження, особу притягнено до кримінальної відповідальності</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.criminal_statistic.negative_judgment }}%</td>
          </tr>
          <tr>
            <td>В результаті провадження, особу звільнено від кримінальної відповідальності</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.criminal_statistic.positive_judgment }}%</td>
          </tr>
          <tr>
            <td>Відсоток вироків, що вистояли у вищих інстанціях</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.criminal_statistic.approved_by_appeal }}%</td>
          </tr>
          <tr>
            <td>Середня тривалість розгляду одного провадження (днів)</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.criminal_statistic.average_duration }} дн.</td>
          </tr>

          <!-- КУпАП СУДОЧИНСТВО -->
          <tr>
            <th scope="col" class="text-center" :colspan="judgesList.length + 1">Судочинство в порядку КУпАП</th>
          </tr>
          <tr>
            <td>В результаті провадження, особу притягнено до адміністративної відповідальності</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.adminoffence_statistic.negative_judgment }}%</td>
          </tr>
          <tr>
            <td>В результаті провадження, особу звільнено від адміністративної відповідальності</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.adminoffence_statistic.positive_judgment }}%</td>
          </tr>
          <tr>
            <td>Відсоток постанов, що вистояли у вищих інстанціях</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.adminoffence_statistic.approved_by_appeal ? judge.adminoffence_statistic.approved_by_appeal+'%' : '-' }}</td>
          </tr>
          <tr>
            <td>Відсоток справ, що розглянуті у визначений законом строк</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.adminoffence_statistic.cases_on_time ? judge.adminoffence_statistic.cases_on_time+'%' : '-' }}</td>
          </tr>
          <tr>
            <td>Середня тривалість розгляду однієї справи (днів)</td>
            <td v-for="(judge, index) of judgesList" :key="index">{{ judge.adminoffence_statistic.average_duration ? judge.adminoffence_statistic.average_duration+'дн.' : '-'}}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <spinner v-if="!loadData" />
  </div>
</template>

<script>
import Spinner from '../../shared/Spinner.vue';

    export default {
        name: "JudgeComparison",
        components: {
          Spinner
        },
        data() {
            return {
                judgesList: [],
                loadData: false,
            };
        },
		created() {
			let judge_compare = [];
      var $this = this;

			if (sessionStorage.judge_compare) {
				judge_compare = JSON.parse(sessionStorage.getItem("judge_compare"));
			}
      
      // отримуємо список суддів для поріняння
			let promises = judge_compare.map(function(value, key) {
				axios
					.get(`/api/v1/judges/${value}`, {
						headers: {
							"Content-Type": "application/json",
							"X-Requested-With": "XMLHttpRequest",
							Authorization: localStorage.getItem("token")
						}
					})
					.then(response => {
						$this.judgesList.push(response.data);
					})
					.catch(error => {
						if (error.response && error.response.status === 401) {
							this.$router.push("/login");
						}
						 $this.$router.push("/judges");
					});
      });


      // час затримки спінера пропорційно кількості суддів в порівнянні
      setTimeout(function() {
          $this.loadData = true;
        }, (judge_compare.length * 3000));
      },


      methods: {
            deleteJudgeFromComparation(id) {
              let judge_compare = JSON.parse(sessionStorage.getItem("judge_compare"));
              // якщо це останній суддя в порівнянні - переходимо на список суддів
              if (judge_compare.length < 2) {
                sessionStorage.clear();
                this.$router.push("/judges");

              } else {
              let new_judges_compare = judge_compare.splice(judge_compare.indexOf(id), 1);
              sessionStorage.setItem("judge_compare", JSON.stringify(judge_compare));
              this.$router.go(); 
              }
            },
        }
    };
</script>

<style scoped lang="scss">
@import "../../../../sass/_variables.scss";
@import "../../../../sass/_mixins.scss";

th {
  text-align: center;
}
td {
  text-align: center;
}
td:first-child {
  text-align: right;
  width: 30%;
  max-width: 300px !important;
  font-size: 0.9rem;
}
.delete-comparation {
  color: red;
  cursor: pointer;
}

</style>