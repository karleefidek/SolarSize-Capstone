<template>
  <div
    id="VueInputUiDemo"
    class="vue-input-ui-demo flex"
    :class="{'dark': darkMode}"
  >
    <div class="container">
      <div class="component-container">  
      <form @submit="submit" >
        <table >
            <tr>
                <td style="width:10vw"><br><br></td>
                <td style="width:35vw">
                        <VueInputUi
                            id="VueInputUi1"
                            v-model="value1"
                            label="Latitude"
                            :dark="darkMode"
                            :loader="loading"
                            clearable
                        />
                </td>
                <td style="width:10vw"><button style="width:5vw" class ="map-button" type="button" @click="showMap">🌐</button></td>
                <td style="width:35vw">
                    <VueInputUi
                        id="VueInputUi2"
                        v-model="value2"
                        label="Longitude"
                        :dark="darkMode"
                        :loader="loading"
                        clearable
                    />
                </td>
                <td style="width:10vw"><br><br></td>
            </tr>
            <tr>
                <td style="width=10vw"><br><br></td>
                <td style="width=35vw">
                    <VueInputUi
                        id="VueInputUi3"
                        v-model="value3"
                        label="Time Zone"
                        :dark="darkMode"
                        :loader="loading"
                        clearable
                    />
                </td>
                <td style="width=10vw"><br><br></td>
                <td style="width=35vw">
                    <VueInputUi
                        id="VueInputUi4"
                        v-model="value4"
                        label="Module Tilt"
                        :dark="darkMode"
                        :loader="loading"
                        clearable
                    />
                </td>
                <td style="width:10vw"><br><br></td>
            </tr>
            <tr>
                <td style="width=10vw"><br><br></td>
                <td style="width=35vw">
                    <VueInputUi
                        id="VueInputUi3"
                        v-model="value5"
                        label="Start Date"
                        :dark="darkMode"
                        :loader="loading"
                        clearable
                    />
                </td>
                <td style="width:10vw"><br><br></td>
                <td style="width:35vw">
                    <VueInputUi
                        id="VueInputUi4"
                        v-model="value6"
                        label="End Date"
                        :dark="darkMode"
                        :loader="loading"
                        clearable
                    />
                </td>
                <td style="width:10vw"><br><br></td>
            </tr>
        </table>
        <button type="submit" @click="submit" class="submit-button">Calculate</button>
      </form>
      </div>
      <div id="chart" margin-top=50px>
        <apexchart type="area" height="350" :options="chartOptions" :series="series"></apexchart>
      </div>
    </div>
  </div>
</template>

<script>
import VueInputUi from 'vue-input-ui';
import 'vue-input-ui/dist/vue-input-ui.css';
import VueApexCharts from 'vue-apexcharts';
import axios from 'axios';
import Modal from './Modal';
import { bus } from '../app';
export default {
  name: 'Generation',
  components: {
      VueInputUi, 
      apexchart: VueApexCharts,
      Modal,
    },
    created(){
        bus.$on('latlongAdded',(latLong) =>{
        this.value1 = latLong[0]
        this.value2 = latLong[1]
        this.displayLatLong = true;

        })
        },
  data: function() {
      return {
        value1: '',
        value2: '',
        value3: '',
        value4: '',
        value5: '',
        value6: '',
        value7: null,
        value8: null,
        value9: null,
        value10: null,
        darkMode: false,
        loading: false,
        series: [{
            name: 'Estimate',
            data: []
          }, {
            name: 'series2',
            data: []
        }],
        chartOptions:{
            chart: {
                id: 'generation-overlay'
            },
            xaxis: {
                type: 'datetime',
                categories: []
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                }
            }
        }
      }
  },
  methods: {
    submit (e) {
        e.preventDefault();
        //console.log(e);
        //console.log(this.value1);
        this.loading = true;
        this.series = [{
            name: 'Estimate',
            data: []
            },
            {
            name: 'Estimate',
            data: []
            }
        ]
        this.chartOptions = {
            noData: {
                text: "Loading...",
                align: 'center',
                verticalAlign: 'middle',
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: "Black",
                    fontSize: '48px'
                }
            }               
        };

        let params = {
            lat: this.value1,
            long: this.value2,
            timezone: this.value3,
            moduleTilt: this.value4,
            startDate: this.value5,
            endDate: this.value6
        };

        console.log(params);
        axios.get('/api/estimate', {params: params},{
            headers: {
                "Content-Type": "application/json"
            }
        } )
        .then((response) => {
            this.series = [{
                name: 'Estimate',
                data: response.data[0]
                },
                {
                name: 'Estimate',
                data: []
            }];
            
            this.chartOptions = {
                chart: {
                    id: 'generation-overlay'
                },
                xaxis: {
                    type: 'datetime',
                    categories: response.data[1],
                    title: {
                        text: "Dates"
                    }
                },
                yaxis: {
                    title: {
                        text: "units!"
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    }
                }
            };
            this.loading = false;
            
            console.log(response);
        })
        .catch(error => {
            if(error.response){
                console.log(error.response);
                this.chartOptions = {
                    noData: {
                        text: "An Error calling the API has occured",
                        align: 'center',
                        verticalAlign: 'middle',
                        offsetX: 0,
                        offsetY: 0,
                        style: {
                            color: "Red",
                            fontSize: '48px'
                        }
                    }
                }   
                this.loading = false;           
             }

            });
    },
    showMap(){
        this.$emit("show");
    }
  }
}
</script>

<style scoped>
html, body {
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
    border-radius: .5rem;
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
    border-radius: .5rem;
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

.vue-input-ui-demo {
    background-color: white;
}
.vue-input-ui-demo .container {
    text-align: center;
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
}
.container {
    width: 100vw;
}
.vue-input-ui-demo .component {
    padding: 10px;
    background: #fff;
    border-radius: 4px;
    border: 1px solid #ebebeb;
}
.vue-input-ui-demo .component:hover {
    box-shadow: 0 0 8px 0 rgba(232, 237, 250, 0.6), 0 2px 4px 0 rgba(232, 237, 250, 0.5);
}
.vue-input-ui-demo .component-container {
    margin: 0 10px 20px 10px;
    padding: 20px;
    background: #fff;
    border-radius: 4px;
    border: 1px solid #ebebeb;
    min-width: 300px;
    transition: all 0.25s cubic-bezier(0.645, 0.045, 0.355, 1);
    flex: 1 0 48%;
}
.vue-input-ui-demo .component-container:hover {
    box-shadow: 0 0 8px 0 rgba(232, 237, 250, 0.6), 0 2px 4px 0 rgba(232, 237, 250, 0.5);
}
.vue-input-ui-demo .component-container.dark {
    background-color: #292929;
    color: #fff;
}
.vue-input-ui-demo.dark {
    background-color: #0f0f0f;
}
.vue-input-ui-demo.dark .component-container, .vue-input-ui-demo.dark .component {
    border: 1px solid #424242;
    background-color: #292929;
}
.vue-input-ui-demo.dark .component-container:hover, .vue-input-ui-demo.dark .component:hover {
    box-shadow: 0 0 8px 0 rgba(0, 0, 0, 0.6), 0 2px 4px 0 rgba(0, 0, 0, 0.5);
}
.vue-input-ui-demo.dark .container {
    color: white;
}
</style>