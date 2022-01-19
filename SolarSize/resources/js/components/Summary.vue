<template>
    <div id="chart" margin-top=50px>
            <apexchart type="area" height="350" :options="chartOptions" :series="series"></apexchart>
    </div>
</template>

<script>
import 'vue-input-ui/dist/vue-input-ui.css';
import VueApexCharts from 'vue-apexcharts';
import { bus } from '../app';


export default {
  name: "Summary",
  components: {  apexchart: VueApexCharts },
  data: function() {
      return {
        value1: '',
        value2: '',
        value3: '',
        value4: '',
        value5: '',
        value6: '',
        value7: null,
        value8: null,
        value9: null,
        value10: null,
        darkMode: false,
        loading: false,
        series: [{
            name: 'Estimate',
            data: []
          }, {
            name: 'series2',
            data: []
        }],
        chartOptions:{
            chart: {
                id: 'generation-overlay'
            },
            xaxis: {
                type: 'datetime',
                categories: []
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                }
            }
        }
      }
  },
  methods:{
  },
    created(){
      bus.$on('generationSuccess', (chartOptions,series) =>{
        console.log("got here");
        this.series = series
        this.chartOptions = chartOptions


      })
      }
};
</script>