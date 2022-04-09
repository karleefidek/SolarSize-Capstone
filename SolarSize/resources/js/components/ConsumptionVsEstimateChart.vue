<template>
  <div>
    <highcharts :options="chartOptions"> </highcharts>
  </div>
</template>

<script>
import { Chart } from "highcharts-vue";
import Highcharts from "highcharts";
export default {
  name: "ConsumptionVsEstimateChart",
  props: {
    startTime: {
      type: Number,
      default: 0,
    },
    endTime: {
      type: Number,
      default: 0,
    },
    offset: {
      type: Number,
      default: 0,
    },
    generationSeries: {
      type: Array,
      default: [
        [0, 0],
        [0, 0],
      ],
    },
  },
  components: { highcharts: Chart },

  data: function () {
    return {
      chartOptions: {
        chart: {
          type: "line",
          zoomType: "x",
        },
        title: {
          text: "Consumption Vs. Estimated Solar Generation",
          align: "center",
        },
        time: {
          timezoneOffset: 6 * 60,
        },
        credits: {
          enabled: false,
        },
        tooltip: {
          formatter: function () {
            var timezoneOffset =
              this.point.series.chart.options.time.timezoneOffset;
            console.log(timezoneOffset);
            var dateMS = -timezoneOffset * 1000 * 60 + this.x;

            return (
              "Date: <b>" +
              Highcharts.dateFormat(
                "%a, %b, %d, %I:%M %p",
                new Date(dateMS) //Need this as this.x doesn't account for timezone offset automatically.
              ) + //Format ms to readable format
              " </b></br>" +
              "Power: <b>" +
              this.y.toFixed(2) + //Limit to 2 decmial points
              "</b> KWH"
            );
          },
        },
        xAxis: {
          type: "datetime",
          title: {
            text: "Dates",
          },
          min: Date.UTC(2021, 0, 1, 0),
          max: Date.UTC(2021, 11, 1, 0),
          labels: {
            format: "{value:%b %d %Y}",
            rotation: -45,
            align: "right",
          },
        },
        yAxis: {
          title: {
            text: "Power Generation WH",
          },
        },
        series: [
          {
            type: "area",
            name: "Consumption",
            data: [1, 2, 3],
            color: "#88E9FF",
            fillColor: {
              linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
              stops: [
                [0, "#88E9FF"],
                [1, "rgba(255,255,255,.25)"],
              ],
            },
          },
          {
            type: "area",
            name: "Solar Estimate",
            color: "#96C951",
            data: [
              [Date.UTC(2021, 0, 1, 1), 1],
              [Date.UTC(2021, 0, 2, 1), 2],
              [Date.UTC(2021, 0, 3, 1), 3],
            ],
            fillColor: {
              linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
              stops: [
                [0, "#96C951"],
                [1, "rgba(255,255,255,.25)"],
              ],
            },
          },
        ],
      },
    };
  },
  watch: {
    generationSeries: {
      deep: true,
      handler: function () {
        this.chartOptions.series[0].data = this.generationSeries[0];
        this.chartOptions.series[1].data = this.generationSeries[1];
      },
    },
    startTime: function () {
      this.chartOptions.xAxis.min = this.startTime;
    },
    endTime: function () {
      this.chartOptions.xAxis.max = this.endTime;
    },
    offset: function () {
      this.chartOptions.time.timezoneOffset = this.offset;
    },
  },
};
</script>

<style>
</style>
