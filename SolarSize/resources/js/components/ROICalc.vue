<template>
  <div class="hello">
    <highcharts
      :options="chartOptions"
      :series="series"
      ref="cashFlow"
      class="component-container"
    ></highcharts>
  </div>
</template>

<script>
import "vue-input-ui/dist/vue-input-ui.css";
import { bus } from "../app";
import { Chart } from "highcharts-vue";
import Highcharts from "highcharts";
import ROIText from "./ROIText";
import AnimatedNumber from "./AnimatedNumber";

export default {
  props: {
    solarPanelData: Array,
    investmentData: Object,
  },
  components: { highcharts: Chart, ROIText, AnimatedNumber },
  name: "Optimization",
  data: function () {
    return {
      //These are objects which contain a key:value pair where the key is the UTC time, the value is the KWH
      // E.g 1609480800000:13.14
      // This helps to align the estimate and cosumption values together when doing calculations between them.
      balanceRemaining: [],
      bestAnnualCashflow: [],
      amountSaved: [],
      interestCost: [],
      maintenanceCost: [],
      priceOfPowerSaved: [],
      costInstallKw: 3000,
      interconnectionFee: 315,
      meterCost: 498.15,
      systemKw: 0,
      grants: 0,
      capitalCost: 0,
      powerPrice: 0.1475,
      fullCreditPower: 0,
      overGenPower: 0,
      breakEvenYear: 0,
      ROIPercent: 0,
      ROIYears: 0,
      series: [],
      chartOptions: {
        chart: {
          type: "column",
          zoomType: "x",
        },
        title: {
          text: "Cash Flow Diagram",
          align: "center",
        },
        credits: {
          enabled: false,
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
          labels: {
            style: {
              fontSize: "16px",
            },
          },
        },
        tooltip: {
          pointFormat:
            "<span style='color:{series.color}'>{series.name}</span>: <b>$ {point.y:,.0f}</b><br/>",
          shared: true,
        },
        yAxis: {
          title: {
            text: "$ CAD",
          },
          allowDecimals: false,
        },
        plotOptions: {
          column: {
            stacking: "normal",
            dataLabels: {
              enabled: true,
              format: "$ {point.y:,.0f}",
            },
            pointPadding: 0.2,
            borderWidth: 0,
          },
        },
        series: [],
      },
    };
  },
  computed: {},
  watch: {
    solarPanelData: {
      handler: function (newValue, oldValue) {
        this.powerPrice = Number(this.investmentData.powerCost);
        var solarPanel = this.findBestSolarPanel();

        this.fullCreditPower = solarPanel.totalGeneration;
        this.overGenPower = solarPanel.totalOverGeneration;

        this.chartOptions.series = [
          {
            name: "Capital Cost",
            data: [-this.bestPanelSetup.capitalCost],
            color: "#C25E5E",
            stack: "Costs",
          },
          {
            name: "Maintenance Cost",
            data: this.bestPanelSetup.maintenanceCostFlow.map((num) => -num),
            stack: "Costs",
            color: "#7A6ED9",
          },
          {
            name: "Value of Power Saved",
            data: this.bestPanelSetup.valueOfPowerSavedFlow,
            stack: "Costs",
            color: "#00C4FF",
          },
          {
            name: "Interest Cost",
            data: this.bestPanelSetup.interestCostFlow.map((num) => -num),
            stack: "Costs",
            color: "#728434",
          },
          {
            name: "Remaining Balance",
            data: this.bestPanelSetup.annualCashFlow.map((num) => -num),
            stack: "Principle",
            color: "#29B463",
            negativeColor: "#EE4036",
          },
        ];
      },
      deep: true,
    },
    "investmentData.interestRate": function (newValue, oldValue) {
      //this.interestRate = newValue;
    },
    "investmentData.grantTotal": function (newValue, oldValue) {
      this.grants = newValue;
    },
    "investmentData.powerCost": function (newValue, oldValue) {
      this.powerPrice = Number(newValue);
    },
  },
  methods: {
    findBestSolarPanel: function () {
      var mostAmountSaved = Infinity;

      for (const solarIndex in this.solarPanelData) {
        for (const dataIndex in this.solarPanelData[solarIndex].Data) {
          this.fullCreditPower =
            this.solarPanelData[solarIndex].Data[dataIndex].totalGeneration;
          this.overGenPower =
            this.solarPanelData[solarIndex].Data[dataIndex].totalOverGeneration;
          this.systemKw =
            (this.solarPanelData[solarIndex].Wattage *
              this.solarPanelData[solarIndex].Data[dataIndex].panelCount) /
            1000;
          // 0.3 * this.solarPanelData[solarIndex].Data[dataIndex].panelCount;

          var currentValueAtYear20 = this.calcAnnualCashFlow(
            this.solarPanelData[solarIndex].Data[dataIndex].panelCount,
            this.solarPanelData[solarIndex].Cost
          );

          if (currentValueAtYear20 < mostAmountSaved) {
            //A negative value indicated we get money back, so we minimize the return.
            mostAmountSaved = currentValueAtYear20;
            this.bestPanelSetup = {
              index: Number,
              panelCount: Number,
              mostAmountSaved: Number,
              capitalCost: Number,
              annualCashFlow: [],
              interestCostFlow: [],
              valueOfPowerSavedFlow: [],
              maintenanceCostFlow: [],
            };

            this.bestPanelSetup.index = solarIndex;
            this.bestPanelSetup.panelCount =
              this.solarPanelData[solarIndex].Data[dataIndex].panelCount;
            this.bestPanelSetup.mostAmountSaved = currentValueAtYear20;
            this.bestPanelSetup.annualCashFlow = this.balanceRemaining;
            this.bestPanelSetup.capitalCost = this.capitalCost;
            this.bestPanelSetup.interestCostFlow = this.interestCost;
            this.bestPanelSetup.valueOfPowerSavedFlow = this.priceOfPowerSaved;
            this.bestPanelSetup.maintenanceCostFlow = this.maintenanceCost;
          }
        }
      }

      bus.$emit(
        "bestSolarPanelFound",
        this.bestPanelSetup.index,
        this.bestPanelSetup.panelCount,
        this.bestPanelSetup.mostAmountSaved
      );
      bus.$emit(
        "balanceRemainingCalculated",
        this.bestPanelSetup.annualCashFlow
      );

      return this.solarPanelData[this.bestPanelSetup.index].Data[
        this.bestPanelSetup.panelCount - 1
      ];
    },
    calcAnnualCashFlow: function (panelCount, panelCost) {
      this.balanceRemaining = [];
      var cost = this.calcCapitalCost(panelCount, panelCost);
      var breakEvenYear = Infinity;
      for (let year = 1; year <= 20; year++) {
        if (cost > 0) {
          this.interestCost[year - 1] =
            cost * (Number(this.investmentData.interestRate) / 100);
        } else {
          this.interestCost[year - 1] = 0;
        }
        this.balanceRemaining[year - 1] =
          cost + this.interestCost[year - 1] - this.calcAmountSaved(year);
        cost = this.balanceRemaining[year - 1];
        if (this.balanceRemaining[year - 1] < 0 && breakEvenYear == Infinity) {
          this.breakEvenYear = year - 1;
        }
      }

      return this.balanceRemaining[19];
    },
    calcROIPercent: function () {
      this.ROIPercent = (this.priceOfPowerSaved[0] / (this.capitalCost + this.maintenanceCost[0] + this.interestCost[0])) * 100;
    },
    calcROIYears: function () {
      this.ROIYears = Math.ceil(1 / (this.ROIPercent / 100));
    },
    totalAmountSaved: function () {
      return this.amountSaved.reduce((a, b) => a + b, 0);
    },
    calcCapitalCost: function (panelCount, panelCost) {
      this.capitalCost =
        panelCount * panelCost +
        this.interconnectionFee +
        this.meterCost -
        this.investmentData.grantTotal;
      return this.capitalCost;
    },
    calcAmountSaved: function (year) {
      this.amountSaved[year - 1] =
        this.calcPriceOfPowerSaved(year) - this.calcMaintenanceCost(year);
      return this.amountSaved[year - 1];
    },
    calcPriceOfPowerSaved: function (year) {
      if (year - 1 > 0) {
        this.powerPrice = this.powerPrice + this.powerPrice * 0.055;
      } else {
        this.powerPrice = Number(this.investmentData.powerCost);
      }
      var fullCreditSaved = this.fullCreditPower * this.powerPrice;

      //SaskPower credits all over generation at 7.5 cents
      var overGenPowerPrice = 0.075;
      var halfCreditSaved = this.overGenPower * overGenPowerPrice;

      this.priceOfPowerSaved[year - 1] = fullCreditSaved + halfCreditSaved;
      return this.priceOfPowerSaved[year - 1];
    },
    calcMaintenanceCost: function (year) {
      var perPanelCost = this.systemKw * 10;
      var propInsurance = Math.ceil(this.systemKw / 25) * 250;
      var replacement = Math.ceil(this.systemKw / 25) * 400;

      if (year - 1 < 1) {
        this.maintenanceCost[year - 1] = perPanelCost + propInsurance;
      } else if (year - 1 < 5 || year - 1 > 5) {
        this.maintenanceCost[year - 1] =
          this.maintenanceCost[year - 2] +
          this.maintenanceCost[year - 2] * 0.025;
      } else {
        this.maintenanceCost[year - 1] =
          this.maintenanceCost[year - 2] +
          replacement +
          (this.maintenanceCost[year - 2] + replacement) * 0.025;
      }

      return this.maintenanceCost[year - 1];
    },
  },
  mounted() {},
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
