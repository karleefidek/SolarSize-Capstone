<template>
  <div class="summary-wrapper">
    <div class="description-items">
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Our App</h3>
          </template>
          <p>Our app utilizes building energy consumption metrics and solar intensity data to calculate accurate ROIs for solar power generation installations. This tool allows users to see how different solar power systems match up to their requirements, so they can make informed decisions. There could also be an extension into other Greenwave business domains such as power storage sizing for cloudy days and the night time.</p>
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Our Why</h3>
          </template>
          <p>Fossil fuels are the primary source of energy in Canada and pose serious threats to the environment. Therefore, they need to be replaced by more renewable and sustainable alternatives. Solar energy is a renewable and sustainable source of green energy that can be used in place of fossil fuels. So, a tool that can analyze and calculate the returns on solar power installations may prove very helpful in persuading more people and business to install their own solar generation solutions. Increasing the use of solar power over fossil fuels can help slow the detrimental environmental effects our world is experiencing today.</p>
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Questions We Aim to Answer</h3>
          </template>
          <p>People may experience uncertainty when considering installing solar power solutions. They may have some questions and the SolarSize application aims to answer some of those questions.</p>
          <p>How do you choose the best panel type?</p>
          <p>How many panels should be installed?</p>
          <p>What level of financial return can be expected?</p>

          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Application Architecture</h3>
          </template>
          <p>Frontend:</p>
          <ul>Vue.js - Page views</ul>
          <ul>Highcharts - Create and display graphs</ul>
          <p>Backend:</p>
          <ul>Laravel - Server and data functions</ul>
          <ul>Python - Solar estimation model</ul>
          <p>Data:</p>
          <ul>NASA POWER API</ul>

          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Future Additions</h3>
          </template>
          <p>We envision a number of future additions and advancements that could be implemented as extensions of the current SolarSize application.</p>
          <p>1. Solar Power Installation Comparisons - For example, comparing 20 panels of type A versus 30 panels of type B</p>
          <p>2. User Accounts - Enable client to access application without company advisors</p>
          <p>3. Save Locations and Related Data - Implement data base for client locations and details to enable faster analysis</p>

          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Acknowledgements</h3>
          </template>
          <p>We would like to thank the following people for their involvement with our project.</p>
          <p>Project Advisor: Dr. Timothy Maciag</p>
          <p>Project Mentor: Dr. Kin-Choong Yow</p>
          <p>Project Idea: Greenwave Innovations</p>

          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Sources</h3>
          </template>
          <p>
            <a href="https://pvpmc.sandia.gov/modeling-steps/" target="_blank" rel="noopener noreferrer">PV Performance Modeling Steps</a>
          </p><p>
            <a href="https://www.researchgate.net/publication/279868352_Dynamic_global-to-direct_irradiance_conversion_models" target="_blank" rel="noopener noreferrer">Global-to-Direct Irradiance</a>
          </p><p>
            <a href="https://www.pveducation.org/pvcdrom/introduction/introduction" target="_blank" rel="noopener noreferrer">Photovoltaics Education</a>
          </p><p>
            <a href="https://www.saskpower.com/Our-Power-Future/Our-Electricity/Connecting-to-the-Power-Grid/~/link.aspx?_id=B296EB6254144587A3448D576A30A40E" target="_blank" rel="noopener noreferrer">SaskPower Net Metering After 2019</a>
          </p><p>
            <a href="https://www.saskpower.com/-/media/SaskPower/Our-Power-Future/Powering-2030/Guidelines-NetMetering.ashx" target="_blank" rel="noopener noreferrer">SaskPower Net Metering User Guide</a>
          </p><p>
            <a href="https://ieeexplore.ieee.org/document/7159231" target="_blank" rel="noopener noreferrer">Solar Power Systems Return on Investment Analysis</a>
          </p>

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
  name: "GeneralDetails",
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

.description-items {
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