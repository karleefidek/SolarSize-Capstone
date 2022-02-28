<template>
  <div class="hello">
    <p>
      balance remaining: {{ balanceRemaining }} Amount Saved: {{ amountSaved }}
    </p>
    <p>interest: {{ interestCost }} maint: {{ maintenanceCost }}</p>
    <p>{{ priceOfPowerSaved }}</p>
    <p>{{ costInstallKw }}</p>
    <p>{{ systemKw }}</p>
    <p>{{ grants }}</p>

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
      amountSaved: [],
      interestCost: [],
      maintenanceCost: [],
      priceOfPowerSaved: [],
      costInstallKw: 3000,
      interconnectionFee: 315,
      meterCost: 498.15,
      systemKw: 90,
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
          text: "Annual KWH Generated",
          align: "center",
        },
        credits: {
          enabled: false,
        },
        // tooltip: {
        //   headerFormat:
        //     '<span style="font-size:10px">{point.key}</span><table>',
        //   pointFormat:
        //     '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
        //     '<td style="padding:0"><b>${point.y:.1f} </b></td></tr>',
        //   footerFormat: "</table>",
        //   shared: true,
        //   useHTML: true,
        // },
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
        tooltip: {
          pointFormat:
            "<span style='color:{series.color}'>{series.name}</span>: <b>$ {point.y:,.0f}</b><br/>",
          shared: true,
        },
        yAxis: {
          title: {
            text: "KWH",
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
        series: [
          {
            name: "John",
            data: [5, 3, 4, 7, 2],
          },
          {
            name: "Jane",
            data: [2, 2, 3, 2, 1],
          },
          {
            name: "Joe",
            data: [-3, 4, 4, 2, 5],
            negativeColor: "red",
          },
        ],
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
        this.systemKw = 0.3 * solarPanel.panelCount;

        this.calcAnnualCashFlow();

        this.chartOptions.series = [
          {
            name: "Captial Cost",
            data: [-this.capitalCost],
            negativeColor: "#EE4036",
            stack: "Costs",
          },
          {
            name: "Maintanence Cost",
            data: this.maintenanceCost.map((num) => -num),
            stack: "Costs",
            negativeColor: "#FFBA40",
          },
          {
            name: "Value of Power Saved",
            data: this.priceOfPowerSaved,
            stack: "Costs",
            color: "#96C951",
          },
          {
            name: "Interest Cost",
            data: this.interestCost.map((num) => -num),
            stack: "Costs",
            color: "#523B89",
          },
          {
            name: "Remaining Balance",
            data: this.balanceRemaining,
            stack: "Principle",
            color: "#EE4036",
            negativeColor: "#C0D73E",
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
      var bestPanelIndex;
      var bestPanelNumberIndex;
      var mostAmountSaved = Infinity;

      for (const solarIndex in this.solarPanelData) {
        for (const dataIndex in this.solarPanelData[solarIndex].Data) {
          this.fullCreditPower =
            this.solarPanelData[solarIndex].Data[dataIndex].totalGeneration;
          this.overGenPower =
            this.solarPanelData[solarIndex].Data[dataIndex].totalOverGeneration;
          this.systemKw =
            0.3 * this.solarPanelData[solarIndex].Data[dataIndex].panelCount;

          var currentValueAtYear20 = this.calcAnnualCashFlow(20);
          if (currentValueAtYear20 < mostAmountSaved) {
            //A negative value indicated we get money back, so we minimize the return.
            bestPanelIndex = solarIndex;
            bestPanelNumberIndex =
              this.solarPanelData[solarIndex].Data[dataIndex].panelCount;
            mostAmountSaved = currentValueAtYear20;
          }
        }
      }

      bus.$emit("bestSolarPanelFound", bestPanelIndex, bestPanelNumberIndex);

      return this.solarPanelData[bestPanelIndex].Data[bestPanelNumberIndex - 1];
    },
    calcAnnualCashFlow: function () {
      var cost = this.calcCapitalCost();
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
      this.ROIPercent = (this.balanceRemaining[0] / this.capitalCost) * 100;
    },
    calcROIYears: function () {
      this.ROIYears = 1 / (this.ROIPercent / 100);
    },
    totalAmountSaved: function () {
      return this.amountSaved.reduce((a, b) => a + b, 0);
    },
    calcCapitalCost: function () {
      this.capitalCost =
        this.systemKw * this.costInstallKw +
        this.interconnectionFee +
        this.meterCost -
        this.grants;
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
