<template>
  <div>
    <p>{{feature}} <span class="number"> {{calculatedNumber}} </span> </p>
  </div>
</template>

<script>
export default {
    name: "ROIText",
    props:{
        propNumber: Number,
        propText: String
    },
    data: function(){
        return{
            calculatedNumber: this.propNumber,
            feature:  this.propText
        }
    },
    methods: {
    }
};
</script>



<style scoped>
.number{ 
  text-decoration: bold;
  color: green;

}

</style>