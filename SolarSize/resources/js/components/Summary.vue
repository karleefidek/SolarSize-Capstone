<template>
  <div class="summary-wrapper">
    <div>
      <div class="estimation-info component-container" ><ROIText :propNumber="32" :propText="'Average Annual Generation: '" /></div>
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
import ROIText from "./ROIText";
export default {
  name: "Summary",
  components: { highcharts: Chart, ROIText },
  data: function () {
    return {
      chartOptions: {
        chart: {
          type: "line",
        },
        title: {
          text: "Expected Solar Generation Vs. Actual Generation",
          align: "center",
        },
        credits: {
          enabled: false,
        },
        xAxis: {
          type: "datetime",
          title: {
            text: "Power Generation",
          },
          labels: {
            format: "{value:%d/%Y}",
            rotation: -45,
            align: "right",
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
            name: "Generation",
            data: [],
          },
        ],
      },
    };
  },
  methods: {},
  created() {
    bus.$on("generationSuccess", (data) => {
      this.chartOptions.series[0].data = data; //Converts ['1','2','3','4'] into [1,2,3,4]
    });

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