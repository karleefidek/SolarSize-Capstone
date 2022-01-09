<template>
  <l-map style="height: 100%" :zoom="zoom" :center="center" @click="addMarker">
    <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
    <l-marker :lat-lng="markerLatLng" ></l-marker>
  </l-map>
</template>

<script>
import 'leaflet/dist/leaflet.css'
import L from "leaflet"
import {LMap, LTileLayer, LMarker} from 'vue2-leaflet';
import { bus } from '../app';
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png').default,
  iconUrl: require('leaflet/dist/images/marker-icon.png').default,
  shadowUrl: require('leaflet/dist/images/marker-shadow.png').default,
})


export default {
  components: {
    LMap,
    LTileLayer,
    LMarker
  },
  data () {
    return {
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution:
        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      zoom: 10,
      center: L.latLng(50.4452, -104.6189),
      markerLatLng: L.latLng(50.4452, -104.6189)
    };
  },
  mounted(){
    //this.$refs.map.mapObject._onResize();
  },
  methods:{
    addMarker(clickEvent){
      this.markerLatLng = clickEvent.latlng;
      console.log(clickEvent);
      var latLong = [clickEvent.latlng.lat, clickEvent.latlng.lng]
      bus.$emit("latlongAdded",latLong)
    }
  }
}
</script>


<style scoped>
.leaflet-default-icon-path {
		background-image: url('/leaflet/dist/marker-icon.png');
	}

	.leaflet-pane .leaflet-shadow-pane {
	  display: none
	}
</style>
