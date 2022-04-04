<template>
  <div class="summary-wrapper">
    <div class="calculation-items">
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Return Statistics</h3>
          </template>
          <h3>Total Return on Investment:
          <h3 v-bind:class="-returnTotalValue < 0 ? 'numberRed' : 'numberGreen'">
              <h3>$ {{Number(-1 * Math.ceil(returnTotalValue)).toFixed(2)}}</h3>
          </h3>
          </h3>
          <template>
            <OvergenerationPieChart
              :overgenerationTotal="overGenerationTotal"
              :fullCreditConsumptionTotal="fullCreditConsumptionTotal"
              :costOfKWH="costOfKWH"
              :valueOfOverCredit="valueOfOverCredit"
              class="component-container"
            >
            </OvergenerationPieChart>
          </template>
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Generation Statistics</h3>
          </template>

          <h3>Annual KWH Generated:
          <h3 v-bind:class="numberGreen">
              <h3>{{estimateTotal}} KWH </h3>
          </h3>
          </h3>
          <template>
            <AnnualGenerationChart
              :estimateDataObject="estimateMap"
              :consumptionDataObject="consumptionMap"
              ref="chartComponent"
              class="component-container"
            ></AnnualGenerationChart>
          </template>

          <template v-slot:footer> </template>
        </ROIText>
      </div>
    </div>
    <div class="calculation-items">
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Cash Flow Estimation</h3>
          </template>

          <cashflow
            :solarPanelData="solarPanelData"
            :investmentData="investmentData"
            :formattedGenerationArr="formattedGenerationArr"
          ></cashflow>

          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Best Panel Setup</h3>
          </template>

          <h3>The optimal panel setup is:</h3>
          <div v-if="bestPanel.name != ''">
            <h3>
              <span style="color: #96c951">{{ bestPanel.count }}</span>
              <span style="color: #88e9ff">{{ bestPanel.name }}s</span>
              Angled at <span style="color: #ee4036">30°</span> degrees
            </h3>
            <div class="panel-repeat-group">
              <span style="margin: 3px" v-for="i in bestPanel.count" :key="i">
                <svg width="10" height="10">
                  <rect
                    width="10"
                    height="10"
                    v-bind:style="{
                      fill: i % 2 == 0 ? '#96c951' : '#88e9ff',
                    }"
                    style="stroke-width: 1; stroke: rgb(0, 0, 0)"
                  />
                </svg>
              </span>
            </div>
          </div>

          <template v-slot:footer> </template>
        </ROIText>
      </div>
    </div>
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>Yearly Balance Remaining</h3>
        </template>

        <BalanceRemainingChart
          ref="balanceRemainingChart"
          class="component-container"
        ></BalanceRemainingChart>

        <template v-slot:footer> </template>
      </ROIText>
    </div>
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>Consumption Graph</h3>
        </template>

        <ConsumptionVsEstimateChart
          :startTime="startTime"
          :endTime="endTime"
          :offset="offset"
          :generationSeries="generationSeries"
          ref="chartComponent"
          class="component-container"
        ></ConsumptionVsEstimateChart>

        <template v-slot:footer> </template>
      </ROIText>
    </div>
  </div>
</template>

<script>
import VueInputUi from "vue-input-ui";
import exporting from "highcharts/modules/exporting";
import offlineExporting from "highcharts/modules/offline-exporting";
import "vue-input-ui/dist/vue-input-ui.css";
import { bus } from "../app";
import { Chart } from "highcharts-vue";
import Highcharts from "highcharts";
import ROIText from "./ROIText";
import AnimatedNumber from "./AnimatedNumber";
import ROICalc from "./ROICalc";
import OvergenerationPieChart from "./OvergenerationPieChart";
import ConsumptionVsEstimateChart from "./ConsumptionVsEstimateChart";
import AnnualGenerationChart from "./AnnualGenerationChart";
import BalanceRemainingChart from "./BalanceRemainingChart.vue";

exporting(Highcharts);
offlineExporting(Highcharts);
Highcharts.setOptions({
  exporting: {
    filename: "test",
    enabled: true,
    sourceWidth: 1400.64,
    sourceHeight: 278.4,
    scale: 1,
    fallbackToExportServer: false,
  },
});

export default {
  name: "Summary",
  components: {
    highcharts: Chart,
    ROIText,
    AnimatedNumber,
    cashflow: ROICalc,
    VueInputUi,
    OvergenerationPieChart,
    ConsumptionVsEstimateChart,
    AnnualGenerationChart,
    BalanceRemainingChart,
  },
  data: function () {
    return {
      //These are objects which contain a key:value pair where the key is the UTC time, the value is the KWH
      // E.g 1609480800000:13.14
      // This helps to align the estimate and cosumption values together when doing calculations between them.
      consumptionMap: {},
      estimateMap: {},
      formattedGenerationArr: [],
      number: 0,
      generationSeries: [],
      investmentData: {
        interestRate: Number,
        powerCost: Number,
        grantTotal: Number,
      },
      bestPanel: {
        name: "",
        count: 0,
      },
      consumptionData: [],
      solarPanelData: [],
      costOfKWH: 0.13,
      valueOfOverCredit: 0.075,
      costOfInvestment: 100,
      startTime: 0,
      endTime: 0,
      offset: 0,
    };
  },
  computed: {
    returnTotalValue: function () {
      return this.bestPanel.name == ""
        ? this.fullCreditConsumptionTotal * this.costOfKWH +
            this.overGenerationTotal * this.valueOfOverCredit -
            this.costOfInvestment
        : this.valueAtEnd;
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

    estimateYearlyTotalGeneration: function (
      estimateDataObject,
      startTime,
      endTime
    ) {
      var total = this.sumTotalGenerationEstimate(estimateDataObject);
      var days = (endTime - startTime) / (1000 * 3600 * 24);

      return (total / days) * 365;
    },
    estimateYearlyOverGeneration: function (
      estimateDataObject,
      consumptionDataObject,
      startTime,
      endTime
    ) {
      var total = this.sumOverGenerationEstimate(
        estimateDataObject,
        consumptionDataObject
      );
      var days = (endTime - startTime) / (1000 * 3600 * 24);

      return (total / days) * 365;
    },
    sumTotalGenerationEstimate: function (estimateDataObject) {
      var totalGenerationEstimate = 0;
      for (const timeKey in estimateDataObject) {
        totalGenerationEstimate =
          totalGenerationEstimate + estimateDataObject[timeKey];
      }
      return totalGenerationEstimate;
    },
  },
  created() {
    bus.$on(
      "generationSuccess",
      (estimateData, consumptionData, startTime, endTime, offSet) => {
        Vue.set(this.generationSeries, 0, estimateData);
        Vue.set(this.generationSeries, 1, consumptionData);

        this.startTime = startTime;
        this.endTime = endTime;
        this.offSet = -offSet * 60; //Offset UTC, west = negative east = positive, in minutes

        this.consumptionMap = Object.assign(
          ...consumptionData.map(([key, value]) => ({ [key]: value })) //We map the UTC time to a Key:value object so we can align estimate and actual consumption by UTC time lookup
        );

        this.estimateMap = Object.assign(
          ...estimateData.map(([key, value]) => ({ [key]: value }))
        );
      }
    );

    bus.$on("summaryTabFocus", () => {
      console.log(this.$refs.chartComponent.chart);
      //this.$refs.chartComponent.chart.redraw();
    });

    bus.$on(
      "generationSuccessOptimized",
      (
        formattedGenerationArr,
        consumptionData,
        startTime,
        endTime,
        offSet,
        powerCost,
        grantTotal,
        interestInput,
        roofSize
      ) => {
        this.investmentData.interestRate = interestInput;
        this.investmentData.powerCost = powerCost;
        this.investmentData.grantTotal = grantTotal;
        this.formattedGenerationArr = formattedGenerationArr;

        this.consumptionData = consumptionData;
        this.startTime = startTime;
        this.endTime = endTime;
        this.offSet = -offSet * 60;
        this.consumptionMap = Object.assign(
          ...consumptionData.map(([key, value]) => ({ [key]: value })) //We map the UTC time to a Key:value object so we can align estimate and actual consumption by UTC time lookup
        );

        var solarPanelBreakdowns = [];
        for (const index in formattedGenerationArr) {
          var numberOfPanels = 1;

          var panelObject = {
            Name: formattedGenerationArr[index]["Name"],
            Cost: formattedGenerationArr[index]["Cost"],
            Wattage: formattedGenerationArr[index]["Wattage"],
          };
          var generationBreakdownsArr = [];
          while (
            numberOfPanels * formattedGenerationArr[index]["Area"] <
            roofSize
          ) {
            var estimateMap = Object.assign(
              ...formattedGenerationArr[index]["Data"].map(([key, value]) => ({
                [key]: value * numberOfPanels,
              }))
            );

            var totalGenEstimate = this.estimateYearlyTotalGeneration(
              estimateMap,
              startTime,
              endTime
            );

            var totalOverGenerationEstimate = this.estimateYearlyOverGeneration(
              estimateMap,
              this.consumptionMap,
              startTime,
              endTime
            );

            generationBreakdownsArr.push({
              panelCount: numberOfPanels,
              totalGeneration: totalGenEstimate,
              totalOverGeneration: totalOverGenerationEstimate,
            });
            numberOfPanels++;
          }
          panelObject.Data = generationBreakdownsArr;

          solarPanelBreakdowns.push(panelObject);
        }
        this.costOfKWH = Number(powerCost);
        this.valueOfOverCredit = Number(powerCost) / 2;
        this.solarPanelData = solarPanelBreakdowns;
      }
    );

    bus.$on(
      "bestSolarPanelFound",
      (bestPanelIndex, numOfPanels, valueAtEnd) => {
        this.valueAtEnd = valueAtEnd;
        var generationArray = [];
        this.bestPanel.name = this.formattedGenerationArr[bestPanelIndex].Name;
        this.bestPanel.count = numOfPanels;

        for (const index in this.formattedGenerationArr[bestPanelIndex].Data) {
          generationArray[index] = [];
          generationArray[index][0] =
            this.formattedGenerationArr[bestPanelIndex].Data[index][0];
          generationArray[index][1] =
            this.formattedGenerationArr[bestPanelIndex].Data[index][1] *
            numOfPanels;
        }
        Vue.set(this.generationSeries, 0, this.consumptionData);
        Vue.set(this.generationSeries, 1, generationArray);
        this.estimateMap = Object.assign(
          ...generationArray.map(([key, value]) => ({
            [key]: value,
          })) //We map the UTC time to a Key:value object so we can align estimate and actual consumption by UTC time lookup
        );
      }
    );
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
  white-space: break-spaces;
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
  color: #96c951;
}

.numberRed {
  text-decoration: bold;
  color: red;
  position: relative;
}

.sliderInput {
  display: grid;
  grid-template-columns: 1fr 8fr 2fr;
  grid-column-gap: 20px;
}

.sliderInput input {
  margin: auto;
}

.roiOutput {
  font-size: 1.8em;
  text-align: center;
}

* {
  /* all element selector */
  box-sizing: border-box; /* border, within the width of the element */
}
input {
  display: block;
  width: 100%;
}
</style>