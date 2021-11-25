<template>
  <div id="btn-app">
    <BottomNavigation
      :options="options"
      v-model="selected"
    />
    <div class="extra">
      <Generation
        :solarData="solarData"
        :dates="dates"
      />
    </div>

  </div>
</template>

<script>
import BottomNavigation from "./BottomNavigation";
import Generation from "./Generation";

export default {
  name: "App",
  props: {
    solar: Array,
    dates: Array
  },
  components: { BottomNavigation, Generation },
  data: function() {
   return { 
      selected: 1,
      options: [
      { id: 1, title: "Generation",},
      { id: 2, title: "Summary", },
      ],
      foregroundColor: "#39dd73",
      badgeColor: "#FBC02D",
      solarData: this.solar,
      dates: this.dates
    }
  }
};
</script>

<style scoped>
#btn-app {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 5px;
  box-sizing: border-box;
  font-family: "Roboto", sans-serif;
  white-space: nowrap;
}
.extra {
  display: inline-flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-top: 100px;
}
</style>
