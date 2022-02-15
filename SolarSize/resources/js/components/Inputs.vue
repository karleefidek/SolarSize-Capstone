<template>
  <div>
    <form @submit="submit" v-on:keydown.enter.prevent>
      <div class="main-container flex">
        <div class="container">
          <div class="component-container">
            <div class="inputContainerMap">
              <p>
                <v-select
                  id="location"
                  v-model="location"
                  :options="['Test Fill Entry']"
                  placeholder="Select a Location"
                  label="Location"
                >
                </v-select>
              </p>
              <b-tooltip target="location" placement="right" triggers="hover">
                Either a previously saved location or new location (existing
                locations will auto-populate fields).
              </b-tooltip>

              <div>
                <span class="errorMsg" v-if="msg.latInput">{{
                  msg.latInput
                }}</span>
                <VueInputUi
                  id="latitude"
                  v-model="latInput"
                  label="Latitude"
                  type="number"
                  :dark="darkMode"
                  required
                  :loader="loading"
                />
                <b-tooltip target="latitude" placement="right" triggers="hover">
                  The latitude of the building location (auto-populated by
                  selecting a location on the map).
                </b-tooltip>
              </div>
              <div>
                <span class="errorMsg" v-if="msg.longInput">{{
                  msg.longInput
                }}</span>
                <VueInputUi
                  id="longitude"
                  v-model="longInput"
                  label="Longitude"
                  type="number"
                  :dark="darkMode"
                  required
                  :loader="loading"
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

              <VueInputUi
                id="timeZone"
                v-model="zoneInput"
                label="Time Zone"
                type="number"
                :dark="darkMode"
                required
                :loader="loading"
              />
              <b-tooltip target="timeZone" placement="right" triggers="hover">
                The time zone the building is located in.
              </b-tooltip>

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
                v-model="fileRecords"
                uploadUrl="/api/uploadCSV"
                :uploadHeaders="{}"
                @select="getData($event)"
              ></VueFileAgent>
              <b-tooltip target="fileUpload" placement="right" triggers="hover">
                Upload a .csv file containing building consumption data for
                consumption/generation overlay
              </b-tooltip>

              <div style="grid-row: 1/-2">
                <Map ref="map" :center="mapCenter" />
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
      <div class="main-container flex">
        <div class="container">
          <div class="component-container">
            <div class="inputContainer">
              <div>
                <span class="errorMsg" v-if="msg.directionInput">{{
                  msg.directionInput
                }}</span>
                <VueInputUi
                  id="direction"
                  v-model="directionInput"
                  label="Panel Direction"
                  :dark="darkMode"
                  required
                  :loader="loading"
                  clearable
                />
                <b-tooltip
                  target="direction"
                  placement="right"
                  triggers="hover"
                >
                  The direction the panel is facing. Ex. S30W
                </b-tooltip>
              </div>

              <div>
                <span class="errorMsg" v-if="msg.tiltInput">{{
                  msg.tiltInput
                }}</span>
                <VueInputUi
                  id="tilt"
                  v-model="tiltInput"
                  label="Module Tilt"
                  type="number"
                  :dark="darkMode"
                  required
                  :loader="loading"
                />
                <b-tooltip target="tilt" placement="right" triggers="hover">
                  The angle at which the solar panel is installed.
                </b-tooltip>
              </div>

              <div>
                <span class="errorMsg" v-if="msg.areaInput">{{
                  msg.areaInput
                }}</span>
                <VueInputUi
                  id="area"
                  v-model="areaInput"
                  label="Module Area"
                  type="number"
                  :dark="darkMode"
                  required
                  :loader="loading"
                  clearable
                />
                <b-tooltip target="area" placement="right" triggers="hover">
                  The area of a single panel. (Size of the panel LxH)
                </b-tooltip>
              </div>

              <div>
                <span class="errorMsg" v-if="msg.efficiencyInput">{{
                  msg.efficiencyInput
                }}</span>
                <VueInputUi
                  id="efficiency"
                  v-model="efficiencyInput"
                  label="Module Efficiency"
                  :dark="darkMode"
                  required
                  :loader="loading"
                  clearable
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

              <div>
                <span class="errorMsg" v-if="msg.lossInput">{{
                  msg.lossInput
                }}</span>
                <VueInputUi
                  id="loss"
                  v-model="lossInput"
                  label="Loss Coefficient"
                  :dark="darkMode"
                  required
                  :loader="loading"
                  clearable
                />
                <b-tooltip target="loss" placement="right" triggers="hover">
                  The coefficient of losses from environmental factors and
                  efficiency losses in inverters, cables, and panels. (Entered
                  as a decimal)
                </b-tooltip>
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
              <v-select
                id="billing"
                v-model="billing"
                :options="['Residential', 'Industrial']"
                placeholder="Select Billing Type"
                label="Billing Type"
              >
              </v-select>
              <b-tooltip target="billing" placement="right" triggers="hover">
                The type of SaskPower billing.
              </b-tooltip>

              <div style="grid-row: 1/-1">
                <p id="startDate">
                  <VueCtkDateTimePicker
                    v-model="startInput"
                    only-date
                    format="YYYY-MM-DD"
                    label="Start Date"
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
                    v-model="endInput"
                    only-date
                    format="YYYY-MM-DD"
                    label="End Date"
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
            v-if="!loading"
            type="submit"
            @click="submit"
            class="submit-button"
            key="button"
          >
            Calculate
          </button>

          <LoadingIcon v-else key="loading-icon" />
        </transition>
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
      this.latInput = latLong[0];
      this.longInput = latLong[1];
      this.displayLatLong = true;
    });
  },
  data: function () {
    return {
      mapCenter: L.latLng(50.4452, -104.6189),
      location: "",
      latInput: "",
      longInput: "",
      consumptionInput: "",
      directionInput: "",
      zoneInput: "",
      tiltInput: "30",
      areaInput: "",
      efficiencyInput: "",
      lossInput: "",
      startInput: "",
      endInput: "",
      billing: "",
      darkMode: false,
      loading: false,
      fileRecords: [],
      consumption: [],
      addressAutoFill: [],
      address: "",
      msg: [],
    };
  },
  watch: {
    //TEST FILL METHOD TO FILL WITH PRESET VALUES
    location: function (value) {
      console.log(value);
      if (value === "Test Fill Entry") {
        this.latInput = "50.4583783";
        this.longInput = "-104.62211";
        this.directionInput = "0";
        this.zoneInput = "-6";
        this.areaInput = "1.5";
        this.efficiencyInput = "0.8";
        this.startInput = "2021-01-01";
        this.endInput = "2021-05-01";
        this.lossInput = "0.127";
      }
    },
    latInput(value) {
      this.latInput = value;
      this.validateLat(value);
    },
    longInput(value) {
      this.longInput = value;
      this.validateLong(value);
    },
    tiltInput(value) {
      this.tiltInput = value;
      this.validateTilt(value);
    },
    directionInput(value) {
      this.directionInput = value;
      this.validateDirection(value);
    },
    areaInput(value) {
      this.areaInput = value;
      this.validateArea(value);
    },
    efficiencyInput(value) {
      this.afficiencyInput = value;
      this.validateEfficiency(value);
    },
    lossInput(value) {
      this.lossInput = value;
      this.validateLoss(value);
    },
  },
  methods: {
    getData(fileRecords) {
      let file = fileRecords[0].file;

      var results;
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
    },
    submit(e) {
      e.preventDefault();
      this.loading = true;
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
      let params = {
        lat: this.latInput,
        long: this.longInput,
        timezone: this.zoneInput,
        moduleTilt: this.tiltInput,
        startDate: this.startInput,
        endDate: this.endInput,
        moduleArea: this.areaInput,
        moduleEfficiency: this.efficiencyInput,
        lossCoefficient: this.lossInput,
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
          this.series = [
            {
              name: "Estimate",
              data: response.data[0],
            },
            {
              name: "Estimate",
              data: [],
            },
          ];

          this.loading = false;
          var dateData = response.data[1];
          var powerData = response.data[0];
          var formattedDataGeneration = dateData.map((e, i) => [
            new Date(e).getTime(),
            isNaN(Number(powerData[i])) ? 0 : Number(powerData[i]) / 1000, // Converts NaN to 0, WH to KWH
          ]);
          var startDate = new Date(this.startInput);
          var endDate = new Date(this.endInput);
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
          this.latInput = response.data["lat"];
          this.longInput = response.data["long"];
          this.mapCenter = L.latLng(this.latInput, this.longInput);
        })
        .catch((error) => {
          if (error.response) {
            console.log(error.response);
            this.loading = false;
          }
        });
    },
    validateLat(value) {
      if (value >= -90 && value <= 90) {
        this.msg["latInput"] = "";
      } else {
        this.msg["latInput"] = "Invalid latitude - must be between -90 and 90";
      }
    },
    validateLong(value) {
      if (value >= -180 && value <= 180) {
        this.msg["longInput"] = "";
      } else {
        this.msg["longInput"] =
          "Invalid longitude - must be between -180 and 180";
      }
    },
    validateTilt(value) {
      if (value >= 0 && value < 90) {
        this.msg["tiltInput"] = "";
      } else {
        this.msg["tiltInput"] =
          "Invalid module tilt - must be an angle between 0 and 89";
      }
    },
    validateDirection(value) {
      if (value >= 0 && value < 360) {
        this.msg["directionInput"] = "";
      } else {
        this.msg["directionInput"] =
          "Invalid panel direction - must be an angle between 0 and 359";
      }
    },
    validateArea(value) {
      if (value > 0) {
        this.msg["areaInput"] = "";
      } else {
        this.msg["areaInput"] = "Invalid module area - must be greater than 0";
      }
    },
    validateEfficiency(value) {
      if (value > 0 && value < 1) {
        this.msg["efficiencyInput"] = "";
      } else {
        this.msg["efficiencyInput"] =
          "Invalid module efficiency - must be between 0 and 1 (1 - decimal value of percentage)";
      }
    },
    validateLoss(value) {
      if (value > 0 && value < 1) {
        this.msg["lossInput"] = "";
      } else {
        this.msg["lossInput"] =
          "Invalid loss coefficient - must be between 0 and 1";
      }
    },
  },
};
</script>

<style scoped src="../../css/app.css">
</style>