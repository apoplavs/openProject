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
                  data: [40, 55, 30, 50, 90, 50]
               },

            ]
         },
         //Chart.js options that controls the appearance of the chart
         options: {
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
            legend: {
               display: false
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