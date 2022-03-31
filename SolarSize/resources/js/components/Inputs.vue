<template>
  <div>
    <form id="mainForm" @submit="submit" v-on:keydown.enter.prevent>
      <div class="main-container flex">
        <div class="container">
          <div class="component-container">
            <div class="inputContainerMap">
              <p>
                <v-select
                  id="location"
                  name="location"
                  v-model="location"
                  :options="['Test Fill Entry']"
                  placeholder="Select a Location"
                  label="Location"
                >
                </v-select>
                <!-- <v-select
                  id="generation-type"
                  v-model="generationType"
                  :options="['Custom Generation', 'Optimized Generation']"
                  placeholder="Select a type of generation"
                  label="Generation Type"
                  @input="resetValues"
                >
                </v-select> -->
              </p>
              <b-tooltip target="location" placement="right" triggers="hover">
                Either a previously saved location or new location (existing
                locations will auto-populate fields).
              </b-tooltip>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.latInput">{{
                  msg.latInput
                }}</span>
                <VueInputUi
                  id="latitude"
                  name="latitude"
                  v-model.number="formInputs.latInput"
                  label="Latitude"
                  :dark="darkMode"
                  :loader="loading"
                  :error="!!msg.latInput"
                />
                <b-tooltip target="latitude" placement="right" triggers="hover">
                  The latitude of the building location (auto-populated by
                  selecting a location on the map).
                </b-tooltip>
              </div>
              <div class="inputAndMessage">
                <span
                  class="errorMsg"
                  v-if="msg.longInput"
                  :class="!msg.longInput ? 'shake' : ''"
                  >{{ msg.longInput }}</span
                >
                <VueInputUi
                  id="longitude"
                  name="longitude"
                  v-model="formInputs.longInput"
                  label="Longitude"
                  :dark="darkMode"
                  :loader="loading"
                  :error="!!msg.longInput"
                />
                <b-tooltip
                  target="longitude"
                  placement="right"
                  triggers="hover"
                >
                  The longitude of the building location (auto-populated by
                  selecting a location on the map).
                </b-tooltip>
              </div>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.consumption">{{
                  msg.consumption
                }}</span>
                <VueFileAgent
                  id="fileUpload"
                  ref="vueFileAgent"
                  :theme="'list'"
                  :multiple="false"
                  :deletable="true"
                  :meta="true"
                  :accept="'.csv'"
                  :maxSize="'10MB'"
                  :maxFiles="1"
                  :helpText="'Choose consumption .csv files'"
                  :errorText="{
                    type: 'Invalid file type. Only .csv files allowed',
                    size: 'Files should not exceed 10MB in size',
                  }"
                  v-model="formInputs.fileRecords"
                  uploadUrl="/api/uploadCSV"
                  :uploadHeaders="{}"
                  @select="getData($event)"
                ></VueFileAgent>
                <b-tooltip
                  target="fileUpload"
                  placement="right"
                  triggers="hover"
                >
                  Upload a .csv file containing building consumption data for
                  consumption/generation overlay. Columns to include for data
                  are Main Power, Inverter #1, Inverters #2 - #5
                </b-tooltip>
              </div>

              <div style="grid-row: 1/-2">
                <Map ref="map" :center="mapCenter" id="mapSelect" />
                <div class="input-address-grid">
                  <VueInputUi
                    id="address"
                    v-model="address"
                    :options="addressAutoFill"
                    placeholder="Select a Location"
                    label="Enter your Address"
                    :dark="darkMode"
                    :loader="loading"
                    @keyup.enter.prevent="getCoordsFromAddress"
                  />
                  <button
                    v-if="!loading"
                    type="button"
                    @click="getCoordsFromAddress"
                    class="map-button"
                    key="addressButton"
                  >
                    Get
                  </button>
                  <b-tooltip
                    target="address"
                    placement="right"
                    triggers="hover"
                  >
                    Enter an address to find on the map.
                  </b-tooltip>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <br />
      <div class="main-container">
        <div class="container">
          <div class="component-container">
            <div v-for="(panel, index) in panels" :key="index" class="panels">
              <div class="symbol-input">
                <VueInputUi
                  :id="'panel_name' + index"
                  v-model="panel.Name"
                  :ref="'panel_name' + index"
                  label="Panel Name"
                  :dark="darkMode"
                  :loader="loading"
                  @focus="
                    $refs[
                      'panel_name' + index
                    ][0].$el.nextElementSibling.classList.add('focused')
                  "
                  @blur="
                    $refs[
                      'panel_name' + index
                    ][0].$el.nextElementSibling.classList.remove('focused')
                  "
                />
                <i>ID</i>
              </div>
              <div class="symbol-input">
                <VueInputUi
                  :id="'panel_eff' + index"
                  v-model="panel.ModuleEfficiency"
                  label="Module Efficiency"
                  :ref="'panel_eff' + index"
                  :dark="darkMode"
                  :loader="loading"
                  @focus="
                    $refs[
                      'panel_eff' + index
                    ][0].$el.nextElementSibling.classList.add('focused')
                  "
                  @blur="
                    $refs[
                      'panel_eff' + index
                    ][0].$el.nextElementSibling.classList.remove('focused')
                  "
                />
                <i>0.xx</i>
              </div>
              <div class="symbol-input">
                <VueInputUi
                  :id="'panel_area' + index"
                  v-model="panel.Area"
                  label="Panel Area"
                  :ref="'panel_area' + index"
                  :dark="darkMode"
                  :loader="loading"
                  @focus="
                    $refs[
                      'panel_area' + index
                    ][0].$el.nextElementSibling.classList.add('focused')
                  "
                  @blur="
                    $refs[
                      'panel_area' + index
                    ][0].$el.nextElementSibling.classList.remove('focused')
                  "
                />
                <i>M²</i>
              </div>
              <div class="symbol-input">
                <VueInputUi
                  :id="'panel_cost' + index"
                  v-model="panel.Cost"
                  label="Panel Unit Cost"
                  :ref="'panel_cost' + index"
                  :dark="darkMode"
                  :loader="loading"
                  @focus="
                    $refs[
                      'panel_cost' + index
                    ][0].$el.nextElementSibling.classList.add('focused')
                  "
                  @blur="
                    $refs[
                      'panel_cost' + index
                    ][0].$el.nextElementSibling.classList.remove('focused')
                  "
                />
                <i style="line-height: 50%"
                  >$<br />
                  ― <br />
                  Unt</i
                >
              </div>
              <div class="symbol-input">
                <VueInputUi
                  :id="'panel_watt' + index"
                  v-model="panel.Wattage"
                  :ref="'panel_watt' + index"
                  label="Panel Wattage"
                  :dark="darkMode"
                  :loader="loading"
                  @focus="
                    $refs[
                      'panel_watt' + index
                    ][0].$el.nextElementSibling.classList.add('focused')
                  "
                  @blur="
                    $refs[
                      'panel_watt' + index
                    ][0].$el.nextElementSibling.classList.remove('focused')
                  "
                />
                <i>W</i>
              </div>
              <div>
                <span
                  v-show="panels.length > 1"
                  @click="removePanel(index)"
                  class="delete-btn"
                  >X</span
                >
              </div>
              <br />
            </div>
            <button
              v-if="!loading && panels.length < 7"
              type="button"
              @click="addPanel"
              class="add-panel-button"
              key="addressButton"
            >
              Add Panel
            </button>
          </div>
        </div>
      </div>
      <div class="main-container flex">
        <div class="container">
          <div class="component-container">
            <div
              v-if="generationType == 'Custom Generation'"
              class="inputContainer"
            >
              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.tiltInput">{{
                  msg.tiltInput
                }}</span>
                <VueInputUi
                  id="tilt"
                  v-model="formInputs.tiltInput"
                  label="Module Tilt"
                  :dark="darkMode"
                  :loader="loading"
                  :error="!!msg.tiltInput"
                />
                <b-tooltip target="tilt" placement="right" triggers="hover">
                  The angle at which the solar panel is installed.
                </b-tooltip>
              </div>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.areaInput">{{
                  msg.areaInput
                }}</span>
                <VueInputUi
                  id="area"
                  v-model="formInputs.areaInput"
                  label="Module Area"
                  :dark="darkMode"
                  :loader="loading"
                  clearable
                  :error="!!msg.areaInput"
                />
                <b-tooltip target="area" placement="right" triggers="hover">
                  The area of a single panel. (Size of the panel LxH)
                </b-tooltip>
              </div>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.efficiencyInput">{{
                  msg.efficiencyInput
                }}</span>
                <VueInputUi
                  id="efficiency"
                  v-model="formInputs.efficiencyInput"
                  label="Module Efficiency"
                  :dark="darkMode"
                  :loader="loading"
                  clearable
                  :error="!!msg.efficiencyInput"
                />
                <b-tooltip
                  target="efficiency"
                  placement="right"
                  triggers="hover"
                >
                  The percentage of sunlight that hits the panel and is
                  converted into electricity. (Entered as a decimal)
                </b-tooltip>
              </div>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.lossInput">{{
                  msg.lossInput
                }}</span>
                <VueInputUi
                  id="loss"
                  v-model="formInputs.lossInput"
                  label="Loss Coefficient"
                  :dark="darkMode"
                  :loader="loading"
                  clearable
                  :error="!!msg.lossInput"
                />
                <b-tooltip target="loss" placement="right" triggers="hover">
                  The coefficient of losses from environmental factors and
                  efficiency losses in inverters, cables, and panels. (Entered
                  as a decimal)
                </b-tooltip>
              </div>
              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.directionInput">{{
                  msg.directionInput
                }}</span>

                <div class="inputAndMessage">
                  <round-slider
                    v-model="formInputs.directionInput"
                    label="Roof Direction"
                    :dark="darkMode"
                    :loader="loading"
                    clearable
                    :error="!!msg.directionInput"
                    :min="360"
                    :max="0"
                    :step="-1"
                    :pathColor="'#42644e'"
                    :rangeColor="'#7FB82C'"
                    :disabled="loading"
                    :radius="60"
                    :startValue="188"
                    :tooltipFormat="sliderTooltip"
                    @touchmove="$refs.input.blur()"
                  ></round-slider>
                  <span class="slider-text" id="direction"
                    >Roof Orientation: {{ panelDirection }}</span
                  >
                  <b-tooltip
                    target="direction"
                    placement="right"
                    triggers="hover"
                  >
                    The direction the roof is facing. Ex. 0 or 360 = North, 90 =
                    East, 180 = South, 270 = West. In the Northern Hemisphere,
                    south orientation will get the best results.
                  </b-tooltip>
                </div>
              </div>
            </div>
            <div
              v-if="generationType == 'Optimized Generation'"
              class="inputContainer"
            >
              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.roofInput">{{
                  msg.roofInput
                }}</span>
                <div class="symbol-input">
                  <VueInputUi
                    id="area"
                    ref="area"
                    v-model="formInputs.roofInput"
                    label="Available Roof Area "
                    :dark="darkMode"
                    :loader="loading"
                    :error="!!msg.roofInput"
                    @focus="
                      $refs.area.$el.nextElementSibling.classList.add('focused')
                    "
                    @blur="
                      $refs.area.$el.nextElementSibling.classList.remove(
                        'focused'
                      )
                    "
                  />
                  <i>M²</i>
                </div>
                <b-tooltip target="area" placement="right" triggers="hover">
                  How much area is available for solar panels to be installed
                  on. (M²)
                </b-tooltip>
              </div>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.interestInput">{{
                  msg.interestInput
                }}</span>
                <div class="symbol-input">
                  <VueInputUi
                    id="interest"
                    ref="interest"
                    v-model="formInputs.interestInput"
                    label="Interest Rate"
                    :dark="darkMode"
                    :loader="loading"
                    clearable
                    :error="!!msg.interestInput"
                    @focus="
                      $refs.interest.$el.nextElementSibling.classList.add(
                        'focused'
                      )
                    "
                    @blur="
                      $refs.interest.$el.nextElementSibling.classList.remove(
                        'focused'
                      )
                    "
                  />
                  <i>%</i>
                </div>
                <b-tooltip target="interest" placement="right" triggers="hover">
                  The interest rate of the installation.
                </b-tooltip>
              </div>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.grantInput">{{
                  msg.grantInput
                }}</span>
                <div class="symbol-input">
                  <VueInputUi
                    id="grant"
                    ref="grant"
                    v-model="formInputs.grantInput"
                    label="Available Grants"
                    :dark="darkMode"
                    :loader="loading"
                    clearable
                    :error="!!msg.grantInput"
                    @focus="
                      $refs.grant.$el.nextElementSibling.classList.add(
                        'focused'
                      )
                    "
                    @blur="
                      $refs.grant.$el.nextElementSibling.classList.remove(
                        'focused'
                      )
                    "
                  />
                  <i>$</i>
                </div>
                <b-tooltip target="grant" placement="right" triggers="hover">
                  The amount of project offset by grants
                </b-tooltip>
              </div>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.powerCostInput">{{
                  msg.powerCostInput
                }}</span>
                <div class="symbol-input">
                  <VueInputUi
                    id="powercost"
                    ref="powercost"
                    v-model="formInputs.powerCostInput"
                    label="Cost of a kWh"
                    :dark="darkMode"
                    :loader="loading"
                    clearable
                    :error="!!msg.powerCostInput"
                    @focus="
                      this.$el.nextElementSibling.classList.add('focused')
                    "
                    @blur="
                      $refs.powercost.$el.nextElementSibling.classList.remove(
                        'focused'
                      )
                    "
                  />
                  <i style="line-height: 50%"
                    >$<br />
                    ― <br />
                    kWh</i
                  >
                </div>
                <b-tooltip
                  target="powercost"
                  placement="right"
                  triggers="hover"
                >
                  The cost of a kilowatt hour (kWh) in dollars.
                </b-tooltip>
              </div>

              <div class="inputAndMessage">
                <span class="errorMsg" v-if="msg.directionInput">{{
                  msg.directionInput
                }}</span>

                <div style="margin-left: 33%; margin-right: 33%">
                  <round-slider
                    v-model="formInputs.directionInput"
                    label="Roof Direction"
                    dark="darkMode"
                    startValue="180"
                    :loader="loading"
                    clearable
                    :error="!!msg.directionInput"
                    :start-angle="90"
                    :end-angle="90"
                    :min="0"
                    :max="360"
                    :step="1"
                    :pathColor="'#42644e'"
                    :rangeColor="'#42644e'"
                    :disabled="loading"
                    :radius="60"
                    :tooltipFormat="sliderTooltip"
                    path-color="#094818"
                    width="6"
                    handle-size="35,4"
                    handle-shape="square"
                    @touchmove="$refs.input.blur()"
                  ></round-slider>
                  <span class="slider-text" id="direction"
                    ><br />Roof Direction: {{ panelDirection }}</span
                  >
                  <b-tooltip
                    target="direction"
                    placement="right"
                    triggers="hover"
                  >
                    The direction the roof is facing. South to West Cardinality
                    Ex. 0 or 360 = South, 90 = East, 180 = North, 270 = West. In
                    the Northern Hemisphere, south orientation will get the best
                    results.
                  </b-tooltip>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <br />
      <div class="main-container flex">
        <div class="container">
          <div class="component-container">
            <div class="inputContainer">
              <b-tooltip target="billing" placement="right" triggers="hover">
                The type of SaskPower billing.
              </b-tooltip>

              <div style="grid-row: 1/-1">
                <p id="startDate">
                  <VueCtkDateTimePicker
                    v-model="formInputs.startInput"
                    only-date
                    format="YYYY-MM-DD"
                    label="Start Date"
                    id="startDatePicker"
                  />
                </p>
                <b-tooltip
                  target="startDate"
                  placement="right"
                  triggers="hover"
                >
                  The start date of analysis period.
                </b-tooltip>

                <br />

                <p id="endDate">
                  <VueCtkDateTimePicker
                    v-model="formInputs.endInput"
                    only-date
                    format="YYYY-MM-DD"
                    label="End Date"
                    id="endDatePicker"
                  />
                </p>
                <b-tooltip target="endDate" placement="right" triggers="hover">
                  The end date of analysis period.
                </b-tooltip>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="submit-container">
        <transition name="component-fade" mode="out-in">
          <button
            v-if="canSubmit"
            type="submit"
            @click="submit"
            class="submit-button"
            key="button"
            :disabled="validated"
          >
            Calculate
          </button>

          <LoadingIcon v-else-if="loading" key="loading-icon" />

          <button
            v-else
            class="disabled-button"
            key="button"
            @click="validateInputsBeforeSubmit"
          >
            Calculate
          </button>
        </transition>
        <button
          v-if="!loading"
          class="reset-button"
          @click.prevent="resetValues"
        >
          Reset
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import VueInputUi from "vue-input-ui";
import "vue-input-ui/dist/vue-input-ui.css";
import VueCtkDateTimePicker from "vue-ctk-date-time-picker";
import "vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css";
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import VueFileAgent from "vue-file-agent";
import VueFileAgentStyles from "vue-file-agent/dist/vue-file-agent.css";
import RoundSlider from "vue-round-slider";
import axios from "axios";
import Modal from "./Modal";
import Map from "./Map";
import LoadingIcon from "./LoadingIcon";
import L from "leaflet";
import { bus } from "../app";
export default {
  name: "Generation",
  components: {
    VueInputUi,
    Modal,
    VueCtkDateTimePicker,
    vSelect,
    Map,
    LoadingIcon,
  },
  created() {
    bus.$on("latlongAdded", (latLong) => {
      this.formInputs.latInput = latLong[0] % 90;
      this.formInputs.longInput = latLong[1] % 180;
      this.displayLatLong = true;
    });
  },
  data: function () {
    return {
      formInputs: {
        latInput: "",
        longInput: "",
        consumptionInput: "",
        directionInput: "180",
        zoneInput: "",
        tiltInput: "0",
        areaInput: "",
        efficiencyInput: "",
        lossInput: "",
        startInput: "",
        endInput: "",
        billing: "",
        powerCostInput: "",
        grantInput: "",
        interestInput: "",
        roofInput: "",
      },
      panels: [
        {
          ModuleEfficiency: 0.184,
          Area: 1.6864,
          Name: "310W Black Frame 60 cell Mono-PERC 35mm T4 CANADIAN MADE",
          Cost: 222.0,
          Wattage: 310,
        },
        {
          ModuleEfficiency: 0.198,
          Area: 1.82169,
          Name: "Longi – LR4-60HPB-360M – Mono – Black",
          Cost: 281.76,
          Wattage: 360,
        },
        {
          ModuleEfficiency: 0.184,
          Area: 2.290836,
          Name: "Longi ‐ LR4‐72HPH‐450M, Monofacial, 35mm",
          Cost: 322.79,
          Wattage: 450,
        },
      ],

      mapCenter: L.latLng(50.4452, -104.6189),
      generationType: "Optimized Generation",
      location: "",
      darkMode: false,
      loading: false,
      fileRecords: [],
      consumption: [],
      addressAutoFill: [],
      address: "",
      msg: {},
      panelDirection: "South",
      validated: true,
    };
  },
  computed: {
    canSubmit: function () {
      return (
        !this.validated &&
        !this.loading &&
        this.emptyMessages &&
        this.filledInputs
      );
    },

    emptyMessages: function () {
      if (this.generationType == "Custom Generation") {
        var currentInputs = [
          "latInput",
          "longInput",
          "consumptionInput",
          "directionInput",
          "tiltInput",
          "areaInput",
          "efficiencyInput",
          "lossInput",
        ];
      } else {
        var currentInputs = [
          "latInput",
          "longInput",
          "consumptionInput",
          "directionInput",
          "powerCostInput",
          "grantInput",
          "interestInput",
          "roofInput",
        ];
      }
      let noMessages = true;
      let filledInputs = true;

      for (const input in currentInputs[input]) {
        if (this.msg[input]) {
          noMessages = false;
        }
      }
      return noMessages;
    },
    filledInputs: function () {
      if (this.generationType == "Custom Generation") {
        var currentInputs = [
          "latInput",
          "longInput",
          "directionInput",
          "tiltInput",
          "areaInput",
          "efficiencyInput",
          "lossInput",
        ];
      } else if (this.generationType == "Optimized Generation") {
        var currentInputs = [
          "latInput",
          "longInput",
          "directionInput",
          "powerCostInput",
          "grantInput",
          "interestInput",
          "roofInput",
        ];
      }
      var filledInputs = true;
      for (const input in currentInputs) {
        if (!this.formInputs[currentInputs[input]]) {
          filledInputs = false;
        }
      }
      if (this.consumption.length == 0) {
        filledInputs == false;
      }
      return filledInputs;
    },
  },
  watch: {
    //TEST FILL METHOD TO FILL WITH PRESET VALUES
    location: function (value) {
      if (value === "Test Fill Entry") {
        this.formInputs.latInput = "50.4583783";
        this.formInputs.longInput = "-104.62211";
        this.formInputs.directionInput = "180";
        this.formInputs.zoneInput = "-6";
        this.formInputs.areaInput = "1.5";
        this.formInputs.efficiencyInput = "0.127";
        this.formInputs.startInput = "2021-01-01";
        this.formInputs.endInput = "2021-05-01";
        this.formInputs.lossInput = "0.8";
        this.formInputs.grantInput = "500";
        this.formInputs.interestInput = "15";
        this.formInputs.powerCostInput = "20";
        this.formInputs.roofInput = "400";
        this.location = "";
      }
    },
    "formInputs.latInput": function (value) {
      this.formInputs.latInput = value;
      this.validateLat(value);
    },
    "formInputs.longInput": function (value) {
      this.formInputs.longInput = value;
      this.validateLong(value);
    },
    "formInputs.tiltInput": function (value) {
      this.formInputs.tiltInput = value;
      this.validateTilt(value);
    },
    "formInputs.directionInput": function (value) {
      this.formInputs.directionInput = value;
      this.validateDirection(value);
    },
    "formInputs.areaInput": function (value) {
      this.formInputs.areaInput = value;
      this.validateArea(value);
    },
    "formInputs.efficiencyInput": function (value) {
      this.formInputs.afficiencyInput = value;
      this.validateEfficiency(value);
    },
    "formInputs.lossInput": function (value) {
      this.formInputs.lossInput = value;
      this.validateLoss(value);
    },
    "formInputs.powerCostInput": function (value) {
      this.formInputs.powerCostInput = value;
      this.validatePower(value);
    },
    "formInputs.grantInput": function (value) {
      this.formInputs.grantInput = value;
      this.validateGrant(value);
    },
    "formInputs.interestInput": function (value) {
      this.formInputs.interestInput = value;
      this.validateInterest(value);
    },
    "formInputs.roofInput": function (value) {
      this.formInputs.roofInput = value;
      this.validateRoof(value);
    },
    consumption: function (value) {
      this.consumption = value;
      this.validateConsumption(value);
    },
  },
  methods: {
    addPanel() {
      this.panels.push({
        ModuleEfficiency: "",
        Area: "",
        Name: "",
        Cost: "",
        Wattage: "",
      });
    },
    removePanel(index) {
      this.panels.splice(index, 1);
    },
    sliderTooltip(e) {
      return e.value + "°";
    },
    resetValues() {
      this.msg = {};
      this.formInputs = {
        latInput: "",
        longInput: "",
        consumptionInput: "",
        directionInput: "",
        zoneInput: "",
        tiltInput: "",
        areaInput: "",
        efficiencyInput: "",
        lossInput: "",
        startInput: "",
        endInput: "",
        billing: "",
        powerCostInput: "",
        grantInput: "",
        interestInput: "",
        roofInput: "",
      };
      this.panels = [
        {
          ModuleEfficiency: 0.184,
          Area: 1.6864,
          Name: "310W Black Frame 60 cell Mono-PERC 35mm T4 CANADIAN MADE",
          Cost: 222.0,
          Wattage: 310,
        },
        {
          ModuleEfficiency: 0.198,
          Area: 1.82169,
          Name: "Longi – LR4-60HPB-360M – Mono – Black",
          Cost: 281.76,
          Wattage: 360,
        },
        {
          ModuleEfficiency: 0.184,
          Area: 2.290836,
          Name: "Longi ‐ LR4‐72HPH‐450M, Monofacial, 35mm",
          Cost: 322.79,
          Wattage: 450,
        },
      ];
    },
    getData(fileRecords) {
      let file = fileRecords[0].file;

      var results;
      try {
        this.$papa.parse(file, {
          header: true,
          dynamicTyping: true,
          complete: (parsedResults) => {
            results = parsedResults;
            var arrResults = [];
            for (var entry of results.data) {
              let totalConsumption = 0;
              let mainPower = entry["Main Power"];
              let inverter1 = entry["Inverter #1"];
              let inverters = entry["Inverters #2 - #5"];
              let totalInverters = Math.abs(inverter1 + inverters);
              totalConsumption = totalInverters + mainPower;
              arrResults.push([
                new Date(entry["Date/Time"]).getTime(),
                Math.max(totalConsumption, 0),
              ]);
            }
            this.consumption = arrResults;
          },
        });
      } catch (error) {
        Vue.set(
          this.msg,
          "consumption",
          "CSV Error: Please ensure that the file has the columns: Main Power, Inverter #1, Inverters #2 - #5"
        );
        this.validated = false;
      }
    },
    submit(e) {
      e.preventDefault();
      this.series = [
        {
          name: "Estimate",
          data: [],
        },
        {
          name: "Estimate",
          data: [],
        },
      ];
      this.loading = true;
      if (this.generationType == "Custom Generation") {
        this.submitCustom();
      } else if (this.generationType == "Optimized Generation") {
        this.submitOptimized();
      }
    },

    addFocus(event) {
      console.log(event);
    },
    submitOptimized() {
      let params = {
        lat: this.formInputs.latInput,
        long: this.formInputs.longInput,
        timezone: this.formInputs.zoneInput,
        moduleTilt: this.formInputs.directionInput,
        startDate: this.formInputs.startInput,
        endDate: this.formInputs.endInput,
        panels: this.panels,
      };
      axios
        .get(
          "/api/estimateOptimized",
          { params: params },
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        )
        .then((response) => {
          this.loading = false;
          var formattedGenerationArr = [];
          for (const index in response.data) {
            var dateData = response.data[index]["Dates"];
            var powerData = response.data[index]["Power"];
            var formattedDataGeneration = dateData.map((e, i) => [
              new Date(e).getTime(),
              isNaN(Number(powerData[i])) ? 0 : Number(powerData[i]) / 1000, // Converts NaN to 0, WH to KWH
            ]);

            var generationEstimateObject = {
              Name: response.data[index]["Name"][0],
              Cost: response.data[index]["Cost"][0],
              Area: response.data[index]["Area"][0],
              Wattage: response.data[index]["Wattage"][0],
              Data: formattedDataGeneration,
            };

            formattedGenerationArr.push(generationEstimateObject);
          }
          var startDate = new Date(this.formInputs.startInput);
          var endDate = new Date(this.formInputs.endInput);

          endDate.setDate(endDate.getDate() + 2);
          bus.$emit(
            "generationSuccessOptimized",
            formattedGenerationArr,
            this.consumption,
            startDate.getTime(),
            endDate.getTime(),
            this.formInputs.zoneInput,
            this.formInputs.powerCostInput,
            this.formInputs.grantInput,
            this.formInputs.interestInput,
            this.formInputs.roofInput
          );
        })
        .catch((error) => {
          if (error.response) {
            console.log(error.response);
            this.loading = false;
          }
        });
    },
    submitCustom(e) {
      let params = {
        lat: this.formInputs.latInput,
        long: this.formInputs.longInput,
        timezone: this.formInputs.zoneInput,
        moduleTilt: this.formInputs.tiltInput,
        startDate: this.formInputs.startInput,
        endDate: this.formInputs.endInput,
        moduleArea: this.formInputs.areaInput,
        moduleEfficiency: this.formInputs.efficiencyInput,
        lossCoefficient: this.formInputs.lossInput,
      };

      axios
        .get(
          "/api/estimate",
          { params: params },
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        )
        .then((response) => {
          this.loading = false;
          var dateData = response.data[1];
          var powerData = response.data[0];
          var formattedDataGeneration = dateData.map((e, i) => [
            new Date(e).getTime(),
            isNaN(Number(powerData[i])) ? 0 : Number(powerData[i]) / 1000, // Converts NaN to 0, WH to KWH
          ]);
          var startDate = new Date(this.formInputs.startInput);
          var endDate = new Date(this.formInputs.endInput);
          endDate.setDate(endDate.getDate() + 2);
          bus.$emit(
            "generationSuccess",
            formattedDataGeneration,
            this.consumption,
            startDate.getTime(),
            endDate.getTime(),
            this.zoneInput
          );
        })
        .catch((error) => {
          if (error.response) {
            console.log(error.response);
            this.loading = false;
          }
        });
    },

    getCoordsFromAddress(e) {
      let params = { address: this.address };

      axios
        .get(
          "api/getCoords",
          { params },
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        )
        .then((response) => {
          this.formInputs.latInput = response.data["lat"];
          this.formInputs.longInput = response.data["long"];
          this.mapCenter = L.latLng(
            this.formInputs.latInput,
            this.formInputs.longInput
          );
        })
        .catch((error) => {
          if (error.response) {
            console.log(error.response);
            this.loading = false;
          }
        });
    },
    validateInputsBeforeSubmit(e) {
      e.preventDefault();

      for (const input in this.formInputs) {
        if (this.formInputs[input] == "") {
          Vue.set(this.msg, input, "Please fill in");
          this.validated = true;
          if (this.msg["directionInput"] == "Please fill in") {
            this.validated = false;
            this.msg["directionInput"] = "";
          }
        }
      }
      if (this.consumption.length == 0) {
        Vue.set(this.msg, "consumption", "Please upload file");
        this.validated = true;
      }
    },
    validateLat(value) {
      if (!isNaN(value) && value >= -90 && value <= 90) {
        this.msg["latInput"] = "";
        this.validated = false;
      } else {
        this.msg["latInput"] = "Invalid latitude - must be between -90 and 90";
        this.validated = true;
      }
    },
    validateLong(value) {
      if (!isNaN(value) && value >= -180 && value <= 180) {
        this.msg["longInput"] = "";
        this.validated = false;
      } else {
        this.msg["longInput"] =
          "Invalid longitude - must be between -180 and 180";
        this.validated = true;
      }
    },
    validateTilt(value) {
      if (!isNaN(value) && value >= 0 && value < 90) {
        this.msg["tiltInput"] = "";
        this.validated = false;
      } else if (this.generationType == "Custom Generation") {
        this.msg["tiltInput"] =
          "Invalid module tilt - must be an angle between 0 and 89";
        this.validated = true;
      }
    },
    validateDirection(value) {
      if (value >= 0 && value <= 360) {
        this.msg["directionInput"] = "";
        this.validated = false;
      } else {
        this.msg["directionInput"] =
          "Invalid panel direction - must be an angle between 0 and 360";
        this.validated = true;
      }
      var compassArray = [
        "North",
        "North North East",
        "North East",
        "East North East",
        "East",
        "East South East",
        "South East",
        "South South East",
        "South",
        "South South West",
        "South West",
        "West South West",
        "West",
        "West North West",
        "North West",
        "North North West",
      ];
      this.panelDirection = compassArray[Math.floor(value / 22.5 + 0.5) % 16];
    },
    validateArea(value) {
      if (value == "" || (!isNaN(value) && value > 0)) {
        this.msg["areaInput"] = "";
        this.validated = false;
      } else if (this.generationType == "Custom Generation") {
        this.msg["areaInput"] = "Invalid module area - must be greater than 0";
        this.validated = true;
      }
    },
    validateEfficiency(value) {
      if (!isNaN(value) && value > 0 && value < 1) {
        this.msg["efficiencyInput"] = "";
        this.validated = false;
      } else if (this.generationType == "Custom Generation") {
        this.msg["efficiencyInput"] =
          "Invalid module efficiency - must be between 0 and 1 (1 - decimal value of percentage)";
        this.validated = true;
      }
    },
    validateLoss(value) {
      if (value == "" || (!isNaN(value) && value > 0 && value < 1)) {
        this.msg["lossInput"] = "";
        this.validated = false;
      } else if (this.generationType == "Custom Generation") {
        this.msg["lossInput"] =
          "Invalid loss coefficient - must be between 0 and 1";
        this.validated = true;
      }
    },
    validatePower(value) {
      if (value == "" || (!isNaN(value) && value > 0)) {
        this.msg["powerCostInput"] = "";
        this.validated = false;
      } else if (this.generationType == "Optimized Generation") {
        this.msg["powerCostInput"] =
          "Invalid power cost - must be greater than 0";
        this.validated = true;
      }
    },
    validateGrant(value) {
      if (!isNaN(value) && value >= 0) {
        this.msg["grantInput"] = "";
        this.validated = false;
      } else if (this.generationType == "Optimized Generation") {
        this.msg["grantInput"] =
          "Invalid grant cost - must be greater than positive";
        this.validated = true;
      }
    },
    validateInterest(value) {
      if (!isNaN(value) && value >= 0 && value <= 100) {
        this.msg["interestInput"] = "";
        this.validated = false;
      } else if (this.generationType == "Optimized Generation") {
        this.msg["interestInput"] =
          "Invalid interest rate - must be between 0% and 100%";
        this.validated = true;
      }
    },
    validateRoof(value) {
      if (value == "" || (!isNaN(value) && value > 3)) {
        this.msg["roofInput"] = "";
        this.validated = false;
      } else if (this.generationType == "Optimized Generation") {
        this.msg["roofInput"] =
          "Invalid loss coefficient - must be atleast room for 1 panel";
        this.validated = true;
      }
    },
    validateConsumption(value) {
      if (value) {
        this.msg["consumption"] = "";
        this.validated = false;
      } else {
        this.msg["consumption"] =
          "Invalid consumption file - please upload a file of the correct type";
        this.validated = true;
      }
    },
  },
};
</script>

<style scoped src="../../css/app.css">
.slider {
  display: table;
  margin-right: 20px;
  margin-left: 200px;
}
</style>