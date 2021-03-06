<template>
  <div class="summary-wrapper">
    <div class="model-items">
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>Solar Model</h3>
          </template>
          <p>Our solar estimation model uses “all sky” global irradiance values (GHI) from the NASA POWER API along with dew points, temperatures, pressure at surface, and solar zenith angles to calculate the direct normal irradiance and direct horizontal irradiance on a solar panel at the given location. This is achieved through converted global horizontal irradiance values from the API call to direct normal irradiance and diffuse horizontal irradiance (DNI and DHI) values using equations and the DIRINT model.

          DNI and DHI values are then used in conjunction with specifications of the solar panels given by user inputs (panel efficiency, panel location and direction, overall system efficiency, number of panels) to calculate the expected output in kWh in hourly intervals for the desired data range. As of March 2022, the NASA API has data available from fall 2021 and earlier.
          </p>
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>What is GHI?</h3>
          </template>
          <p>GHI is global horizontal irradiance and is equivalent to the global solar irradiance on a horizontal surface. This is what the NASA POWER data provides.</p>
          <div v-katex="'\\small Global\\ Horizontal\\ Irradiance\\ (GHI) = DHI + DNI \\times \\cos (Panel\\ Angle)'"></div>   
    
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>What is DHI?</h3>
          </template>
          <p>DHI is diffuse horizontal irradiance and is the light that reflects/diffuses off of clouds, dust, the ground, and other surfaces before hitting the panel.</p>
          <div v-katex="'\\small Diffuse\\ Horizontal\\ Irradiance\\ (DHI) = GHI - DNI \\times \\cos (Panel\\ Angle)'"></div>

          <p>The equivalent diffuse radiation that hits an angled panel is:</p>
          <div v-katex="'\\small Panel\\ Angled\\ Diffuse\\ Irradiance = DHI \\times \\large \\frac{180 - Panel\\ Angle}{180}'"></div>
          
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
        <ROIText>
          <template v-slot:header>
            <h3>What is DNI?</h3>
          </template>
          <p>DNI is direct normal irradiance and is the light that directly hits the panel, perpendicular to its angle. In our model, it is estimated from GHI by using the DIRINT model from <a href="https://pvlib-python.readthedocs.io/en/latest/reference/generated/pvlib.irradiance.dirint.html?highlight=DIRINT#pvlib.irradiance.dirint" target="_blank" rel="noopener noreferrer">pvlib</a> which performs complex time derivative math estimations to get a value (more information on this can be found <a href="https://www.semanticscholar.org/paper/Dynamic-global-to-direct-irradiance-conversion-Ineichen-Perez/0da5f4e6bdb0f42eb10d45607cce1df13e08961a" target="_blank" rel="noopener noreferrer">here</a>).</p>
          <p>To get the value of DNI on a tilted panel, we must use the complex equation below from <a href="https://www.pveducation.org/pvcdrom/properties-of-sunlight/making-use-of-tmy-data" target="_blank" rel="noopener noreferrer">pveducation</a>. The Solar Declination Angle and HRA (Solar Hour Angle) will be explained in the Solar Time section on this page.
          </p>
          <div v-katex="'\\small Panel\\ Direct\\ Normal\\ Irradiance = DNI \\times (\\sin (δ) \\sin (φ) \\cos (β) - \\sin (δ) \\cos (φ) \\sin (β) \\cos (ψ) + \\cos (δ) \\cos (φ) \\cos (β) \\cos (HRA)\\ \\newline \\qquad \\qquad \\qquad \\qquad \\qquad \\qquad \\qquad\\qquad + \\cos (δ) \\sin (φ) \\sin (β) \\cos (ψ) \\cos (HRA) + \\cos (δ) \\sin (ψ) \\sin (HRA) \\sin (β)) \\newline \\space \\newline  where\\ \\newline \\space \\newline δ\\ is\\ the\\ Declination\\ Angle\\ \\newline  φ\\ is\\ the\\ Latitude\\ \\newline  β\\ is\\ the\\ panel\\ tilt\\ \\newline  ψ\\ is\\ the\\ panel\\ azimuth\\ angle\\ (panel\\ direction/orientation\\ angle)\\ \\newline HRA\\ is\\ solar\\ hour\\ angle'"></div>
          
          <template v-slot:footer> </template>
        </ROIText>
      </div>
      <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>Local Solar Time</h3>
        </template>
        <p>Local Solar Time is the time according to the position of the Sun in the sky relative to your ground position. It is the angle/direction of the Sun instead of an arbitrary time value.
It can be found with the help of a couple other equations including LT(Local Time), TC (Time Correction Factor), LSTM (Local Standard Time Meridian), EoT(Equation of Time), and HRA (Solar Hour Angle).
        </p>
        <div
            v-katex="'\\small Local\\ Solar\\ Time\\  = \\large LT \\small + \\frac{TC}{60} \\newline \\small Local\\ Time\\ = \\small Longitude\\ \\newline \\small Time\\ Correction = \\large 4 \\small * (Longitude\\ -\\ LSTM)\\ +\\ EoT\\ \\newline \\small Local\\ Standard\\ Time\\ Meridian\\ = \\large ΔTUTC\\ *\\ 15°\\ \\small (Delta\\ is\\ the\\ difference\\ between\\ local\\ time\\ zone\\ and\\ UTC,\\ +\\ =\\ west,\\ -\\ =\\ east)\\ \\newline \\small Hour\\ Angle = \\large 15°\\ \\small *\\ (LST\\ -\\ 12)'"></div>
        <template v-slot:footer> </template>
      </ROIText>
      </div>
      <div class="component-container">
      <ROIText>
        <template v-slot:header>
          <h3>Solar Declination Angle</h3>
        </template>
        <p>The Solar Declination Angle is the angle of the sun between the equator and the center of the Sun and Earth. This varies by season due to the Earth's rotational axis and tilt around the Sun. This is used to calculate DNI.</p>
        <div
            v-katex="'\\small Solar\\ Declination\\ Angle\\  = \\large 23.45 \\small *[ \\frac{360}{365} *\\ (284\\ + day of year )]'"></div> 
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
  name: "SolarDetails",
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