<template>
  <div class = "background">
    <div id="app">
      <Modal v-if="modalVisible" @close="closeModal" @show="showModal" @select="updateLatLong" ref="modal" />  
      <BottomNavigation
        :options="options"
        v-model="selected"
        ref="nav"
      
      />
      <div class="extra">
        <Inputs
          @show="showModal"
          v-show="selected=='inputs'"
          
        />
        <Summary
          v-show="selected=='summary'" 
        />
      </div>
      </div>
  </div>
</template>

<script>
import BottomNavigation from "./BottomNavigation";
import Inputs from "./Inputs";
import Summary from "./Summary";
import Modal from "./Modal"
import { bus } from '../app';

export default {
  name: "App",
  components: { BottomNavigation, Inputs, Modal, Summary },
  data: function() {
   return { 
      modalVisible: false,
      selected: "inputs",
      options: [
      { id: "inputs", title: "Inputs",},
      { id: "summary", title: "Summary", },
      ],
      foregroundColor: "#39dd73",
      badgeColor: "#FBC02D",
    }
  },
  methods:{
     showModal(){
        this.modalVisible = true;
    },
    closeModal(){
        this.modalVisible = false;
    }
  },
  watch:{
    selected: function(value){
      if(value === "summary"){
        bus.$emit("summaryTabFocus");
      }
    }
  },
  created(){
    bus.$on("generationSuccess" ,() => {
      this.selected = "summary"
      this.$refs.nav.$data.localOptions[1].isActive = true; //sets the navbar to Summary
    })
  }
};
</script>

<style scoped src="../../css/app.css">
</style>
