import { Radar } from 'vue-chartjs';

export default {
   extends: Radar,
   props: {
      dataSet: '',
      labels: ''
   },
   data() {
      return {
         datacollection: {
            //Data to be represented on x-axis
            labels: ['Цивільне своєчасність', 'Цивільне вистояли', 'Кримінальне своєчасність', 'Кримінальне вистояли', 'КУпАП своєчасність', 'КУпАП вистояли'],
            datasets: [
               {
                  label: 'Data One',
                  backgroundColor: 'rgba(255,99,132,0.2)',
                  borderColor: 'rgba(255,99,132,1)',
                  pointBackgroundColor: 'rgba(255,99,132,1)',
                  pointBorderColor: '#fff',
                  pointHoverBackgroundColor: '#fff',

                  //Data to be represented on y-axis
                  data: [
                      this.dataSet.civil_statistic.cases_on_time,
                      this.dataSet.civil_statistic.approved_by_appeal,
                      this.dataSet.criminal_statistic.cases_on_time,
                      this.dataSet.criminal_statistic.approved_by_appeal,
                      this.dataSet.adminoffence_statistic.cases_on_time,
                      this.dataSet.adminoffence_statistic.approved_by_appeal,
                  ]
               },

            ]
         },
         //Chart.js options that controls the appearance of the chart
         options: {
            legend: {
               display: false,
               position: 'top',
               labels: {
                  boxWidth: 80,
                  fontColor: 'rgb(60, 180, 100)'
               }
            },
            tooltips: {
               callbacks: {
                  label: function(tooltipItem) {
                     return Number(tooltipItem.yLabel) + '%';
                  }
               }
            },
            scales: {
               yAxes: [{
                  display: false,
                  ticks: {
                     beginAtZero: true
                  },
                  gridLines: {
                     display: false
                  }
               }],
               xAxes: [{
                  display: false,
                  gridLines: {
                     display: false
                  }
               }]
            },
            responsive: true,
            maintainAspectRatio: false
         }
      }
   },
   mounted() {
      //renderChart function renders the chart with the datacollection and options object.
      this.renderChart(this.datacollection, this.options)
      console.log('>>>>', this.dataSet)
   }
}