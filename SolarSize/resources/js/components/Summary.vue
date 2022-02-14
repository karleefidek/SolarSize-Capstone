<template>
  <div class="summary-wrapper">
    <div class="calculation-items">
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>
              <span>
                Total Return on Investment:
                <span
                  v-bind:class="
                    returnTotalValue < 0 ? 'numberRed' : 'numberGreen'
                  "
                >
                  $<AnimatedNumber
                    :number="Math.ceil(returnTotalValue)"
                  ></AnimatedNumber>
                </span>
              </span>
            </h3>
          </template>
          <template>
            <label for="costOfKWH">Cost of KWH </label>
            <input
              type="range"
              max="2.00"
              min="0.01"
              step="0.01"
              v-model="costOfKWH"
              id="costOfKWH"
            />
            <VueInputUi
              id="costOfKWH"
              v-model="costOfKWH"
              label="Cost of KWH/KWH"
            />
            <label for="costOfPower">Value of Credit </label>
            <input
              type="range"
              max="2.00"
              min="0.01"
              step="0.01"
              v-model="valueOfOverCredit"
              id="valueOfCredit"
            />
            <VueInputUi
              id="valueOfOverCredit"
              v-model="valueOfOverCredit"
              label="Value of Credit /KWH"
            />
            <VueInputUi
              id="costOfInvestment"
              v-model="costOfInvestment"
              label="Cost Of Installation"
            />
            <highcharts :options="pieChartOptions"> </highcharts>
          </template>
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>GENERATION STATS</h3>
          </template>

          <p>Annual KWH Generated: {{sumTotalGenerationEstimate(estimateMap)}} KWH</p>

          <highcharts
            :options="dailyColumnChartOptions"
            :series="series"
            ref="chartComponent"
            class="component-container"
          ></highcharts>

          <template v-slot:footer> </template>
        </ROIText>
      </div>
    </div>
    <div>
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
import VueInputUi from "vue-input-ui";
import "vue-input-ui/dist/vue-input-ui.css";
import { bus } from "../app";
import { Chart } from "highcharts-vue";
import Highcharts from "highcharts";
import ROIText from "./ROIText";
import AnimatedNumber from "./AnimatedNumber";
export default {
  name: "Summary",
  components: { highcharts: Chart, ROIText, AnimatedNumber, VueInputUi },
  data: function () {
    return {
      //These are objects which contain a key:value pair where the key is the UTC time, the value is the KWH
      // E.g 1609480800000:13.14
      // This helps to align the estimate and cosumption values together when doing calculations between them.
      consumptionMap: Object,
      estimateMap: Object,
      number: 0,
      costOfKWH: 0.13,
      valueOfOverCredit: 0.075,
      costOfInvestment: 100,
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
              },
              {
                name: "Full Credit Value",
                y: 30,
              },
            ],
          },
        ],
        credits: {
          enabled: false,
        },
      },
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
      dailyColumnChartOptions: {
        chart: {
          type: "column",
        },
        title: {
          text: "Annual KWH Generated",
          align: "center",
        },
        credits: {
          enabled: false,
        },
        tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y:.1f} KWH</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
        },
        xAxis: {
        categories: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'KWH'
        }
    },
        plotOptions: {
          column: {
              pointPadding: 0.2,
              borderWidth: 0
          }
        },
        series: [{
          name: "Generated",
          data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        },{
          name: "Consumed",
          data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        }],
      },
    };
  },
  computed: {
    returnTotalValue: function () {
      return (
        this.fullCreditConsumptionTotal * this.costOfKWH +
        this.overGenerationTotal * this.valueOfOverCredit -
        this.costOfInvestment
      );
    },
    estimateTotal: function () {
      var sum = 0;
      for (const timeKey in this.estimateMap) {
        sum += this.estimateMap[timeKey];
      }
      return sum;
    },
    consumptionTotal: function () {
      var sum = 0;
      for (const timeKey in this.estimateMap) {
        sum += this.consumptionMap[timeKey];
      }
      return sum;
    },
    overGenerationTotal: function () {
      return this.sumOverGenerationEstimate(
        this.estimateMap,
        this.consumptionMap
      );
    },
    fullCreditConsumptionTotal: function () {
      return this.estimateTotal - this.overGenerationTotal;
    },
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
  },
  methods: {
    sumOverGenerationEstimate: function (
      estimateDataObject,
      consumptionDataObject
    ) {
      var overGenerationArray = [];
      for (const timeKey in estimateDataObject) {
        overGenerationArray.push(
          Math.max(
            estimateDataObject[timeKey] - consumptionDataObject[timeKey],
            0
          )
        );
      }
      return overGenerationArray.reduce(
        (previousEntry, currentEntry) => previousEntry + currentEntry,
        0
      );
    },
    sumTotalGenerationEstimate: function (
      estimateDataObject
    ) {
      var totalGenerationEstimate = 0;
      for (const timeKey in estimateDataObject) {
        totalGenerationEstimate = totalGenerationEstimate + estimateDataObject[timeKey];
      }
      return totalGenerationEstimate;
    },
    sumAnnualGenerationEstimate: function (
      estimateDataObject,
      consumptionDataObject
    ) {
      var generationArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
      for (const timeKey in estimateDataObject) {
        var date = new Date(timeKey);
        generationArray[date.getMonth()] += estimateDataObject[timeKey];
      }
      this.dailyColumnChartOptions.series[0].data = generationArray;

      var consumptionArray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
      for (const timeKey in consumptionDataObject) {
        var date = new Date(timeKey);
        consumptionArray[date.getMonth()] += consumptionDataObject[timeKey];
      }
      this.dailyColumnChartOptions.series[1].data = consumptionArray;
    },
  },
  created() {
    bus.$on(
      "generationSuccess",
      (estimateData, consumptionData, startTime, endTime, offSet) => {
        this.chartOptions.series[0].data = estimateData;
        this.chartOptions.series[1].data = consumptionData;
        this.chartOptions.xAxis.min = startTime;
        this.chartOptions.xAxis.max = endTime;
        this.chartOptions.time.timezoneOffset = -offSet * 60; //Offset UTC, west = negative east = positive, in minutes

        this.consumptionMap = Object.assign(
          ...consumptionData.map(([key, value]) => ({ [key]: value })) //We map the UTC time to a Key:value object so we can align estimate and actual consumption by UTC time lookup
        );

        this.estimateMap = Object.assign(
          ...estimateData.map(([key, value]) => ({ [key]: value }))
        );

        sumAnnualGenerationEstimate(this.estimateMap, this.consumptionMap);
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

.calculation-items {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-template-rows: 1fr;
  gap: 0px 3em;
  grid-template-areas: ". .";
}

.numberGreen {
  text-decoration: bold;
  color: green;
}

.numberRed {
  text-decoration: bold;
  color: red;
}
</style>