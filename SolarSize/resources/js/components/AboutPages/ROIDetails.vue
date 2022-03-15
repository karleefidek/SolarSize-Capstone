<template>
  <div class="summary-wrapper">
    <div class="calculation-items">
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Balance Remaining</h3>
          </template>
          <p>End of 1st year:</p><div v-katex="'\\small Balance = Capital Cost + Loan Interest - Amount Saved'"></div>
          
          <p>2nd year and on:</p><div v-katex="'\\small Balance = Previous Balance + Loan Interest - Amount Saved'"></div>
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>Capital Cost</h3>
        </template>
        <div v-katex="'\\small Capital Cost = (System KW \\times Cost/KW Installed) + Interconnection Study Fee + Bidirectional Meter - Grants/Rebates'"></div>
        <p>Per Canadian solar providers Powertec Solar Inc. and HES PV:</p><div v-katex="'\\small Cost/KW Installed = \\$3000'"></div>
       
        <p>Per SaskPower NetMetering Documentation:</p><div v-katex="'\\small Interconnection Study Fee = \\$315'"></div><div v-katex="'\\small Bidirectional Meter = \\$498.75'"></div>
        <template v-slot:footer> </template>
      </ROIText>
    </div>
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>Amount Saved</h3>
        </template>
        <div v-katex="'\\small Amount Saved = (Power Produced \\times Price of Power) - Maintenance Costs'"></div>
        <template v-slot:footer> </template>
      </ROIText>
    </div>
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>ROI</h3>
        </template>
        <div v-katex="'\\small ROI \\% = \\large \\frac{Total Saved}{Total Cost} \\small \\times 100'"></div>
        <div v-katex="'\\small Years Until Paid Back = \\large \\large \\frac{1}{\\large \\frac{ROI \\%}{100}}'"></div>

        <template v-slot:footer> </template>
      </ROIText>
    </div>
    </div>
  </div>  
</template>

<script>
import ROIText from "../ROIText";
import 'katex/dist/katex.min.css';
import VueKatex from 'vue-katex';

export default {
  name: "ROIDetails",
  components: {
    ROIText,
    VueKatex,
  },
}
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
  color: green;
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