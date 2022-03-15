<template>
  <div class="summary-wrapper">
    <b-sidebar
      id="sidebar-1"
      title="Sidebar"
      visible="true"
      bg-variant="white"
      width="12%"
    >
      <div class="px-3 py-2" bg-white>
        <br /><br />
        <b-button
          pill
          variant="outline-secondary"
          block
          @click="selected = 'general'"
          >General</b-button
        >
        <b-button
          pill
          variant="outline-secondary"
          block
          @click="selected = 'roi'"
          >ROI</b-button
        >
        <b-button
          pill
          variant="outline-secondary"
          block
          @click="selected = 'inputs'"
          >Inputs</b-button
        >
        <b-button
          pill
          variant="outline-secondary"
          block
          @click="selected = 'solarModel'"
          >Solar Model</b-button
        >
      </div>
    </b-sidebar>
    <div class="extra">
      <General v-show="selected == 'general'" />
      <Rois v-show="selected == 'roi'" />
      <Inputs v-show="selected == 'inputs'" />
      <Solar v-show="selected == 'solarModel'" />
    </div>
  </div>
</template>

<script>
import ROIText from "./ROIText";
import "katex/dist/katex.min.css";
import VueKatex from "vue-katex";
import Rois from "./ROIDetails";
import Inputs from "./InputDetails";

export default {
  name: "Summary",
  components: {
    ROIText,
    VueKatex,
    Rois,
    Inputs,
  },
  data: function () {
    return {
      selected: "general",
    };
  },
};
</script>

<style scoped>
b- .summary-wrapper {
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

.extra {
  padding-left: 15%;
  width: 115%;
}
</style>