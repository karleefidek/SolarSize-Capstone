<template>
  <div>
    <form @submit="submit">
      <div class="main-container flex">
        <br />
        <table>
          <tr>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <v-select
                v-model="location"
                :options="['New']"
                placeholder="Select a Location"
                label="Location"
              >
              </v-select>
            </td>
          </tr>
          <tr>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueInputUi
                v-model="latInput"
                label="Latitude"
                type="number"
                :dark="darkMode"
                :loader="loading"
                clearable
              />
            </td>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width=35vw">
              <button
                style="width: 5vw"
                class="map-button"
                type="button"
                @click="showMap"
              >
                🌐
              </button>
            </td>
          </tr>
          <tr>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueInputUi
                v-model="longInput"
                label="Longitude"
                type="number"
                :dark="darkMode"
                :loader="loading"
                clearable
              />
            </td>
          </tr>
          <tr>
            <td style="width=10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueInputUi
                v-model="zoneInput"
                label="Time Zone"
                type="number"
                :dark="darkMode"
                :loader="loading"
                clearable
              />
            </td>
          </tr>
          <tr>
            <td style="width=10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueFileAgent
                ref="vueFileAgent"
                :theme="'list'"
                :multiple="false"
                :deletable="true"
                :meta="true"
                :accept="'.csv'"
                :maxSize="'10MB'"
                :maxFiles="1"
                :helpText="'Choose .csv files'"
                :errorText="{
                  type: 'Invalid file type. Only .csv files allowed',
                  size: 'Files should not exceed 10MB in size',
                }"
                v-model="fileRecords"
                uploadUrl="/api/uploadCSV"
                :uploadHeaders="{}"
                @select="getData($event)"
              ></VueFileAgent>
            </td>
          </tr>
        </table>
        <br />
      </div>
      <br />
      <div class="main-container flex">
        <br />
        <table>
          <tr>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueInputUi
                v-model="directionInput"
                label="Panel Direction"
                :dark="darkMode"
                :loader="loading"
                clearable
              />
            </td>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueInputUi
                v-model="tiltInput"
                label="Module Tilt"
                type="number"
                :dark="darkMode"
                :loader="loading"
                clearable
              />
            </td>
            <td style="width: 10vw"><br /><br /></td>
          </tr>
          <tr>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueInputUi
                v-model="areaInput"
                label="Module Area"
                :dark="darkMode"
                :loader="loading"
                clearable
              />
            </td>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueInputUi
                v-model="efficiencyInput"
                label="Module Efficiency"
                :dark="darkMode"
                :loader="loading"
                clearable
              />
            </td>
            <td style="width: 10vw"><br /><br /></td>
          </tr>
          <tr>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueInputUi
                v-model="lossInput"
                label="Loss Coefficient"
                :dark="darkMode"
                :loader="loading"
                clearable
              />
            </td>
          </tr>
        </table>
        <br />
      </div>
      <br />
      <div class="main-container flex">
        <br />
        <table>
          <tr>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <v-select
                v-model="billing"
                :options="['Residential', 'Industrial']"
                placeholder="Select Billing Type"
                label="Billing Type"
              >
              </v-select>
            </td>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueCtkDateTimePicker
                v-model="startInput"
                only-date
                format="YYYY-MM-DD"
                label="Start Date"
              />
            </td>
          </tr>
          <tr>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw"><br /><br /></td>
            <td style="width: 10vw"><br /><br /></td>
            <td style="width: 35vw">
              <VueCtkDateTimePicker
                v-model="endInput"
                only-date
                format="YYYY-MM-DD"
                label="End Date"
              />
            </td>
            <td style="width: 10vw"><br /><br /></td>
          </tr>
        </table>
        <br />
      </div>
      <div class="submit-container">
        <button type="submit" @click="submit" class="submit-button">
          Calculate
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
import axios from "axios";
import Modal from "./Modal";
import { bus } from "../app";
export default {
  name: "Generation",
  components: {
    VueInputUi,
    Modal,
    VueCtkDateTimePicker,
    vSelect,
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
      consumption: []
    };
  },
  methods: {
    getData(fileRecords) {
      let reader = new FileReader();
      let file = fileRecords[0].file;
      let test = this.$papa.parse(file);

      var results;
      this.$papa.parse(file, {
        header: true,
        dynamicTyping: true,
        complete: (parsedResults) => {
          results = parsedResults;
          var arrResults = [];
          for (var entry of results.data) {
            arrResults.push([new Date(entry["Date/Time"]).getTime(), Math.max(entry["Main Power"]*1000,0)]);
          }
          this.consumption = arrResults;
          console.log(this.consumption);
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
            Number(powerData[i]),
          ]);
          bus.$emit("generationSuccess", formattedDataGeneration,this.consumption);
        })
        .catch((error) => {
          if (error.response) {
            console.log(error.response);
            this.loading = false;
          }
        });
    },
    showMap() {
      this.$emit("show");
    },
  },
};
</script>

<style scoped>
html,
body {
  margin: 0;
  min-height: 100%;
}
td {
  align-self: center;
}
#chart {
  margin-top: 75px;
}
.submit-button {
  margin-top: 20px;
  border: 2px solid #39dd73;
  border-radius: 0.5rem;
  background: none;
  font-weight: bold;
  cursor: pointer;
  transition: 0.8s;
  position: relative;
  overflow: hidden;
  color: #838080;
  padding: 10px 10px;
}
.map-button {
  margin: auto;
  display: inline;
  border: 2px solid #3986dd;
  border-radius: 0.5rem;
  background: none;
  font-weight: bold;
  cursor: pointer;
  transition: 0.8s;
  position: relative;
  overflow: hidden;
  color: #838080;
}
.submit-button:hover {
  color: #39dd73;
}

.submit-container {
  width: 100vw;
  background-color: white;
  text-align: center;
  padding: 10px;
}
.main-container {
  width: 100vw;
  background-color: white;
  border: 1px solid #dad9d9;
  border-radius: 0.5rem;
}
.main-container .container {
  text-align: center;
  font-family: "Avenir", Helvetica, Arial, sans-serif;
}
.container {
  width: 100vw;
}
.main-container .component {
  padding: 10px;
  background: #fff;
  border-radius: 4px;
  border: 1px solid #ebebeb;
}
.main-container .component:hover {
  box-shadow: 0 0 8px 0 rgba(232, 237, 250, 0.6),
    0 2px 4px 0 rgba(232, 237, 250, 0.5);
}
.main-container .component-container {
  margin: 0 10px 20px 10px;
  padding: 20px;
  background: #fff;
  border-radius: 4px;
  border: 1px solid #ebebeb;
  min-width: 300px;
  transition: all 0.25s cubic-bezier(0.645, 0.045, 0.355, 1);
  flex: 1 0 48%;
}
.main-container .component-container:hover {
  box-shadow: 0 0 8px 0 rgba(232, 237, 250, 0.6),
    0 2px 4px 0 rgba(232, 237, 250, 0.5);
}
.main-container .component-container.dark {
  background-color: #292929;
  color: #fff;
}
.main-container.dark {
  background-color: #0f0f0f;
}
.main-container.dark .component-container,
.main-container.dark .component {
  border: 1px solid #424242;
  background-color: #292929;
}
.main-container.dark .component-container:hover,
.main-container.dark .component:hover {
  box-shadow: 0 0 8px 0 rgba(0, 0, 0, 0.6), 0 2px 4px 0 rgba(0, 0, 0, 0.5);
}
.main-container.dark .container {
  color: white;
}
</style>