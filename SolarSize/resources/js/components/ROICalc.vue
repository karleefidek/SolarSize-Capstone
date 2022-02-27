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
    <p>{{ capitalCost }}</p>
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
      costInstallKw: 2400,
      systemKw: 90,
      grants: 0,
      capitalCost: 0,
      powerPrice: 0.1475,
      series: [],
      chartOptions: {
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
  computed: {},
  watch: {
    "investmentData.interestRate": function (newValue, oldValue) {
      //this.investmentData = newValue;
    },
    "investmentData.grantTotal": function (newValue, oldValue) {
      this.grants = newValue;
    },
    "investmentData.powerCost": function (newValue, oldValue) {
      this.powerPrice = newValue;
    },
  },
  methods: {
    calcAnnualCashFlow: function (interestRate) {
      var cost = this.calcCapitalCost();
      for (let year = 1; year <= 20; year++) {
        this.interestCost[year - 1] = cost * (interestRate / 100);
        this.balanceRemaining[year - 1] =
          cost + this.interestCost[year - 1] - this.calcAmountSaved(year);
        cost = this.balanceRemaining[year - 1];
      }
      return 1;
    },
    calcCapitalCost: function () {
      this.capitalCost = this.systemKw * this.costInstallKw - this.grants;
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
      }
      var fullCreditSaved = this.fullCreditPower * this.powerPrice;
      var halfCreditSaved = this.overGenPower * (this.powerPrice / 2);

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
  mounted() {
    this.calcAnnualCashFlow(20);
  },
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
