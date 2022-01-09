<template>
  <div class="modal-backdrop">
    <div class="modal">
  <header class="modal-header">
          <slot name="header">
          </slot>
          <button class="button-close" @click="close" > X </button>
  </header>

      <section class ="modal-body">
          <slot name="body"><Map ref = "map"/>
          </slot>
      </section>

     <footer class="modal-footer">
          <slot name="footer">
          <span v-if="displayLatLong">{{lat}} {{long}}</span>
          </slot>
           <button class="button-close" @click="close"> OK </button>
     </footer>
    </div>
  </div>
</template>


<script>
import { bus } from '../app';
import Map from "./Map"
  export default {
    name: 'Modal',
    components: {Map},
    data: function() {
      return { 
        displayLatLong: false,
        lat: 0.0,
        long:0.0,
      }
  },
    methods: {
      close() {
        this.$emit('close');
        console.log(this.$refs);
      },
      select(){
        this.$emit("selectLatLong",this.lat,this.long)
      }
     
    },
    created(){
      bus.$on('latlongAdded',(latLong) =>{
        this.lat = latLong[0]
        this.long = latLong[1]
        this.displayLatLong = true;

      })
      }
  };
</script>

<style>
  .modal-backdrop {
    z-index:4;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.445);
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .modal {
    position: absolute;
    top: 25%;
    left: 25%;
    margin: auto;
    width: 50%;
    height: 50%;
    background: hsl(0, 0%, 100%);
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
  }

  .modal-header,
  .modal-footer {
    padding: 15px;
    display: flex;
  }

  .modal-header {
    position: relative;
    border-bottom: 1px solid #eeeeee;
    color: #4AAE9B;
    justify-content: space-between;
  }

  .modal-footer {
    border-top: 1px solid #eeeeee;
    flex-direction: column;
    justify-content: flex-end;
  }

  .modal-body {
    position: relative;
    padding: 20px 10px;
  }

  .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    border: none;
    font-size: 20px;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    color: #4AAE9B;
    background: transparent;
  }

  .btn-green {
    color: white;
    background: #4AAE9B;
    border: 1px solid #4AAE9B;
    border-radius: 2px;
  }
</style>