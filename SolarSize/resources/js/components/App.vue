<template>
  <div class="background">
    <div id="app">
      <BottomNavigation :options="options" v-model="selected" ref="nav" />
      <div class="extra">
        <Inputs v-show="selected == 'inputs'" />
        <Summary v-show="selected == 'summary'" />
        <About v-show="selected == 'about'" />
      </div>
    </div>
  </div>
</template>

<script>
import BottomNavigation from "./BottomNavigation";
import Inputs from "./Inputs";
import Summary from "./Summary";
import About from "./About";
import Modal from "./Modal";
import { bus } from "../app";

export default {
  name: "App",
  components: { BottomNavigation, Inputs, Modal, Summary, About },
  data: function () {
    return {
      modalVisible: false,
      selected: "inputs",
      options: [
        { id: "inputs", title: "Inputs" },
        { id: "summary", title: "Summary" },
        { id: "about", title: "About" },
      ],
      foregroundColor: "#39dd73",
      badgeColor: "#FBC02D",
    };
  },
  methods: {},
  watch: {
    selected: function (value) {
      if (value === "summary") {
        bus.$emit("summaryTabFocus");
      }
    },
  },
  created() {
    bus.$on("generationSuccess", () => {
      this.selected = "summary";
      this.$refs.nav.$data.localOptions[1].isActive = true; //sets the navbar to Summary
    });
    bus.$on("generationSuccessOptimized", () => {
      this.selected = "summary";
      this.$refs.nav.$data.localOptions[1].isActive = true; //sets the navbar to Summary
    });
  },
};
</script>

<style scoped src="../../css/app.css">
</style>
