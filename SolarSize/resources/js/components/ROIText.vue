<template>
  <div>
    <header class="header">
      <slot name="header"></slot>
    </header>
    <main class="body">
      <slot></slot>
    </main>
    <footer class="footer">
      <slot name="footer"></slot>
    </footer>
  </div>
</template>

<script>
export default {
  name: "ROIText",
  props: {
    propNumber: Number,
    propText: String,
  },
  data: function () {
    return {
      calculatedNumber: this.propNumber,
      feature: this.propText,
    };
  },
  methods: {},
};
</script>



<style scoped>
.header {
  position: relative;
}

.header:after {
  content: "";
  width: 60%;
  height: 5px;
  background: #96c951;
  position: absolute;
  bottom: -5px;
  left: 0;
}
</style>