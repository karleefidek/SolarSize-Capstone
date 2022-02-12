<template>
  <div class="summary-wrapper">
    <div>
      <!-- <div class="estimation-info component-container" ><ROIText :propNumber="32" :propText="'Average Annual Generation: '" /></div> -->
      <highcharts
        :options="chartOptions"
        :series="series"
        ref="chartComponent"
        class="component-container"
      ></highcharts>
    </div>
  </div>
</template>

<script>
import "vue-input-ui/dist/vue-input-ui.css";
import { bus } from "../app";
import { Chart } from "highcharts-vue";
import Highcharts from "highcharts";
import ROIText from "./ROIText";
export default {
  name: "Summary",
  components: { highcharts: Chart, ROIText },
  data: function () {
    return {
      overGenerationTotal: 0,
      chartOptions: {
        chart: {
          type: "line",
          zoomType: "x",
        },
        title: {
          text: "Expected Solar Generation Vs. Actual Generation",
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
              (this.y / 1000).toFixed(2) + //Limit to 2 decmial points
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
            name: "Estimate",
            data: [1, 2, 3],
            fillColor: {
              linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
              stops: [
                [0, "rgba(58, 204, 188, 1)"],
                [1, "rgba(255,255,255,.25)"],
              ],
            },
          },
          {
            type: "area",
            name: "Consumption",
            data: [
              [Date.UTC(2021, 0, 1, 1), 1],
              [Date.UTC(2021, 0, 2, 1), 2],
              [Date.UTC(2021, 0, 3, 1), 3],
            ],
            fillColor: {
              linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
              stops: [
                [0, "#39dd73"],
                [1, "rgba(255,255,255,.25)"],
              ],
            },
          },
        ],
      },
    };
  },
  methods: {},
  created() {
    bus.$on(
      "generationSuccess",
      (estimateData, consumptionData, startTime, endTime, offSet) => {
        this.chartOptions.series[0].data = estimateData;
        this.chartOptions.series[1].data = consumptionData;
        this.chartOptions.xAxis.min = startTime;
        this.chartOptions.xAxis.max = endTime;
        this.chartOptions.time.timezoneOffset = -offSet * 60; //Offset UTC west = negative east = positive, in minutes

        var overGenerationArray = estimateData.map((element, index) => {
          return element - consumptionData[index];
        });

        this.overGenerationTotal = estimateData.reduce(
          array.reduce(
            (previousEntry, currentEntry) => previousEntry + currentEntry,
            0
          )
        );

        console.log(overGenerationArray, this.overGenerationTotal);
      }
    );

    bus.$on("summaryTabFocus", () => {
      console.log(this.$refs.chartComponent.chart);
      //this.$refs.chartComponent.chart.redraw();
    });
  },
};
</script>

<style scoped>
.summary-wrapper {
  width: 80%;
}

.estimation-info {
  text-align: center;
  font-size: 2em;
}

.component-container {
  margin: 0 10px 20px 10px;
  padding: 20px;
  background: #fff;
  border-radius: 4px;
  border: 1px solid #ebebeb;
  min-width: 300px;
  transition: all 0.25s cubic-bezier(0.645, 0.045, 0.355, 1);
  flex: 1 0 48%;
}
.component-container:hover {
  box-shadow: 0 0 8px 0 rgba(232, 237, 250, 0.6),
    0 2px 4px 0 rgba(232, 237, 250, 0.5);
}
</style>