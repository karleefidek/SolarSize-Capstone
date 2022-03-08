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
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>How do we calculate the solar generation of a solar panel?</h3>
        </template>
        <div>We calculate the output of a solar panel by utilizing solar irradiation values. These values are measured by NASA satellites as Global Horizontal Irradiance (GHI) and we pull them from their Power API for the given locations. These measurements are historical only and are generally ~6 months behind real-time. The API also provides pressures, temperatures, wind speed, cloud cover, and dew point.
Once we have all this data from the API, we need to convert the GHI back into its components, Direct Normal Irradiation (DNI) and Direct Horizontal Irradiation (DHI), to calculate what a solar panel should produce at a given time. This is done by utilizing a model called DIRINT, to estimate the DHI and DNI components from GHI values with dewpoint and temperature values. The result of this is DHI and DNI irradiance values that are used to calculate solar panel output in conjunction with the other inputted values such as efficiency, angle, etc.
</div>
        <template v-slot:footer> </template>
      </ROIText>
    </div>
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>What is System Loss Coefficient?</h3>
        </template>
        <div>System loss coefficient is the number of total electrical losses that will occur in the system, not including solar panel efficiency. This will vary by each system and is influenced by inverter efficiency, length of cable installations, high temperatures, dust, snow cover, shading and other variables. Generally, a system can expect to lose anywhere between 20-30%. In this case the system loss coefficient would be 0.8 to 0.7.</div>
        <template v-slot:footer> </template>
      </ROIText>
    </div>
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>What is Module Efficiency?</h3>
        </template>
        <div>Module efficiency is the efficiency of the solar panel that you are wanting to install. This is a value that can be found in the specifications of the panel. It represents how much of the solar irradiation that hits the surface will be converted into electricity. You can expect this value to be between 12 to ~20% (0.12 to 0.20) with higher efficiencies coming with higher initial costs.</div>
        <template v-slot:footer> </template>
      </ROIText>
    </div>
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>What is Module Area?</h3>
        </template>
        <div>Module area is the area of 1 specific solar panel in meters squared. This is simply a measurement of length times height (L x H) in meters.</div>
        <template v-slot:footer> </template>
      </ROIText>
    </div>
    <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>What is Module Tilt?</h3>
        </template>
        <div>Module tilt is the angle that the solar panels will be tilted at. For optimal solar production, aim for a tilt around 30 degrees or roughly angled to the latitude of the location. For our calculations, the angle will not change once set as the cost of tracking products to move with the sun’s position is prohibitive.</div>
        <template v-slot:footer> </template>
      </ROIText>
      </div>
      <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>What is Roof Orientation?</h3>
        </template>
        <div>The roof orientation is the direction of the roof that the panels will be installed on. In the Northern hemisphere, optimal orientation is south facing. A north facing installation on a reasonably pitched roof will lose upwards of 30% production, with more losses the bigger the pitch. In this case, it will not be feasible to install panels on north facing roofs as the tool will demonstrate.</div>
        <template v-slot:footer> </template>
      </ROIText>
      </div>
      <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>What is the required format of the uploaded CSV file?</h3>
        </template>
        <div>The format of an uploaded CSV file is expected to be in hourly intervals and have three specific columns. These columns are “Main Power”, “Inverter #1”, “Inverters #2 - #5”. This is based off of our test files from GreenWave. 
The “Main Power” column corresponds to the building meter readings, power consumed – power generated. The “Inverter #1” and “Inverters #2 - #5” columns correspond to the power generated by solar panel arrays. It is possible to just have a column of 0’s for “Inverters #2 - #5” if all the generation data is stored in one column. All of these values are all expected to be in kWh.
</div>
        <template v-slot:footer> </template>
      </ROIText>
    </div>
    </div>
  </div>  
</template>

<script>
import ROIText from "./ROIText";
import 'katex/dist/katex.min.css';
import VueKatex from 'vue-katex';

export default {
  name: "Summary",
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