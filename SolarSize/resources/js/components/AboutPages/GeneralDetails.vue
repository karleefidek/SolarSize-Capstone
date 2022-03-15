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