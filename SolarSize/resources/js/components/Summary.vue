<template>
  <div id="chart" margin-top="50px">
    <highcharts
      height="350"
      :options="chartOptions"
      :series="series"
    ></highcharts>
  </div>
</template>

<script>
import "vue-input-ui/dist/vue-input-ui.css";
import { bus } from "../app";
import { Chart } from "highcharts-vue";

export default {
  name: "Summary",
  components: { highcharts: Chart },
  data: function () {
    return {
      value1: "",
      value2: "",
      value3: "",
      value4: "",
      value5: "",
      value6: "",
      value7: null,
      value8: null,
      value9: null,
      value10: null,
      darkMode: false,
      loading: false,
      chartOptions: {
        chart: {
          type: "line",
        },
        title: {
          text: "Expected Solar Generation Vs. Actual Generation"
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
      console.log(data[0]);
      this.chartOptions.series[0].data = data; //Converts ['1','2','3','4'] into [1,2,3,4]
    });
  },
};
</script>