<template>
  <div>
    <highcharts :options="yearlySavingsChartOptions"> </highcharts>
  </div>
</template>

<script>
import { Chart } from "highcharts-vue";
import Highcharts from "highcharts";
import { bus } from "../app";
export default {
  name: "BalanceRemainingChart",
  components: { highcharts: Chart },
  created() {
    bus.$on("balanceRemainingCalculated", (balanceRemaining) => {
      this.yearlySavingsChartOptions.series[0].data = balanceRemaining.map(
        (num) => -num
      );
    });
  },
  data: function () {
    return {
      yearlySavingsChartOptions: {
        chart: {
          type: "column",
        },
        title: {
          text: "Yearly Savings",
          align: "center",
        },
        credits: {
          enabled: false,
        },
        tooltip: {
          pointFormat:
            "<span style='color:{series.color}'>{series.name}</span>: <b>$ {point.y:,.0f}</b><br/>",
          shared: true,
        },
        xAxis: {
          categories: [
            "Year 1",
            "Year 2",
            "Year 3",
            "Year 4",
            "Year 5",
            "Year 6",
            "Year 7",
            "Year 8",
            "Year 9",
            "Year 10",
            "Year 11",
            "Year 12",
            "Year 13",
            "Year 14",
            "Year 15",
            "Year 16",
            "Year 17",
            "Year 18",
            "Year 19",
            "Year 20",
          ],
          crosshair: true,
        },
        yAxis: {
          title: {
            text: "$",
          },
        },
        plotOptions: {
          column: {
            stacking: "normal",
            dataLabels: {
              enabled: false,
              format: "$ {point.y:,.0f}",
            },
            pointPadding: 0.2,
            borderWidth: 0,
          },
        },
        series: [
          {
            name: "Annual Balance Remaining",
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            color: "#96C951",
          },
        ],
      },
    };
  },
};
</script>


<style>
</style>