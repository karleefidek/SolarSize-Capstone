<template>
  <div>
    <highcharts :options="dailyColumnChartOptions"> </highcharts>
  </div>
</template>

<script>
import { Chart } from "highcharts-vue";
import Highcharts from "highcharts";
export default {
  name: "AnnualGenerationChart",
  props: {
    estimateDataObject: {
      type: Object,
    },
    consumptionDataObject: {
      type: Object,
    },
  },
  components: { highcharts: Chart },

  data: function () {
    return {
      dailyColumnChartOptions: {
        chart: {
          type: "column",
        },
        title: {
          text: "Monthly KWH Generated",
          align: "center",
        },
        credits: {
          enabled: false,
        },
        tooltip: {
          headerFormat:
            '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat:
            '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} KWH</b></td></tr>',
          footerFormat: "</table>",
          shared: true,
          useHTML: true,
        },
        xAxis: {
          categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
          ],
          crosshair: true,
        },
        yAxis: {
          min: 0,
          title: {
            text: "KWH",
          },
        },
        plotOptions: {
          column: {
            pointPadding: 0.2,
            borderWidth: 0,
          },
        },
        series: [
          {
            name: "Generated",
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            color: "#96C951",
          },
          {
            name: "Consumed",
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            color: "#88E9FF",
          },
        ],
      },
    };
  },
  watch: {
    estimateDataObject: {
      deep: true,
      handler: function () {
        var generationArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for (const timeKey in this.estimateDataObject) {
          var date = new Date(Number(timeKey));
          generationArray[date.getMonth()] += this.estimateDataObject[timeKey];
        }

        var consumptionArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for (const timeKey in this.estimateDataObject) {
          var date = new Date(Number(timeKey));
          consumptionArray[date.getMonth()] +=
            this.consumptionDataObject[timeKey];
        }

        let categories = [
          "Jan",
          "Feb",
          "Mar",
          "Apr",
          "May",
          "Jun",
          "Jul",
          "Aug",
          "Sep",
          "Oct",
          "Nov",
          "Dec",
        ];
        let firstMonthIndex = -1;
        let lastMonthIndex = -1;
        for (const monthIndex in generationArray) {
          if (generationArray[monthIndex] != 0 && firstMonthIndex == -1) {
            firstMonthIndex = monthIndex;
          }
          if (generationArray[monthIndex] == 0 && firstMonthIndex != -1) {
            lastMonthIndex = monthIndex;
            break;
          }
        }
        this.dailyColumnChartOptions.xAxis.categories = categories.slice(
          firstMonthIndex,
          lastMonthIndex
        );
        this.dailyColumnChartOptions.series[0].data = generationArray.slice(
          firstMonthIndex,
          lastMonthIndex
        );

        this.dailyColumnChartOptions.series[1].data = consumptionArray.slice(
          firstMonthIndex,
          lastMonthIndex
        );
      },
    },
  },
};
</script>

<style>
</style>
