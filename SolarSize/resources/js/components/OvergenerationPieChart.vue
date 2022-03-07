<template>
  <div>
    <highcharts :options="pieChartOptions"> </highcharts>
  </div>
</template>

<script>
import { Chart } from "highcharts-vue";
import Highcharts from "highcharts";
export default {
  name: "OvergenerationChart",
  props: {
    overgenerationTotal: {
      type: Number,
      default: 0,
    },
    fullCreditConsumptionTotal: {
      type: Number,
      default: 0,
    },
    costOfKWH: {
      type: Number,
      default: 0,
    },
    valueOfOverCredit: {
      type: Number,
      default: 0,
    },
  },

  components: { highcharts: Chart },
  data: function () {
    return {
      pieChartOptions: {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: "pie",
        },
        title: {
          text: "Full Credit Generation Value Vs. Over Generation Value",
        },
        tooltip: {
          pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>",
        },
        accessibility: {
          point: {
            valueSuffix: "%",
          },
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: "pointer",
            dataLabels: {
              enabled: true,
              format: "<b>{point.name}</b>: {point.percentage:.1f} %",
            },
          },
        },
        series: [
          {
            name: "Generated",
            colorByPoint: true,
            data: [
              {
                name: "Over Generation Value",
                y: 70,
                color: "#96C951",
              },
              {
                name: "Full Credit Value",
                y: 30,
                color: "#88E9FF",
              },
            ],
          },
        ],
        credits: {
          enabled: false,
        },
      },
    };
  },
  watch: {
    costOfKWH: function () {
      this.pieChartOptions.series[0].data[1].y =
        this.fullCreditConsumptionTotal * this.costOfKWH;
    },
    valueOfOverCredit: function () {
      this.pieChartOptions.series[0].data[0].y =
        this.overGenerationTotal * this.valueOfOverCredit;
    },
    fullCreditConsumptionTotal: function () {
      this.pieChartOptions.series[0].data[1].y =
        this.fullCreditConsumptionTotal * this.costOfKWH;
    },
    overGenerationTotal: function () {
      this.pieChartOptions.series[0].data[0].y =
        this.overGenerationTotal * this.valueOfOverCredit;
    },
  },
};
</script>

<style>
</style>
