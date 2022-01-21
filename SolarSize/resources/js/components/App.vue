<template>
<div class = "background">
    <div id="btn-app">
      <Modal v-if="modalVisible" @close="closeModal" @show="showModal" @select="updateLatLong" ref="modal" />  
      <BottomNavigation
        :options="options"
        v-model="selected"
        ref="nav"
      
      />
      <div class="extra">
        <Generation
          @show="showModal"
          v-show="selected=='generation'"
          
        />
        <Summary
          v-show="selected=='summary'" 
        />
      </div>
        <p>This test model is using an array of 260 REC 345 panels, oriented due south with 17.2% efficiency </p>
</div>

  </div>
</template>

<script>
import BottomNavigation from "./BottomNavigation";
import Generation from "./Generation";
import Summary from "./Summary";
import Modal from "./Modal"
import { bus } from '../app';

export default {
  name: "App",
  components: { BottomNavigation, Generation, Modal, Summary },
  data: function() {
   return { 
      modalVisible: false,
      selected: "generation",
      options: [
      { id: "generation", title: "Generation",},
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
  background: #FFFFFF;
}
.extra {
  display: inline-flex;
  width: 100%;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-top: 100px;
}
</style>
