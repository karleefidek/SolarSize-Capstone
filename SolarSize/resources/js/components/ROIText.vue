<template>
  <div>
    <header>
      <slot name="header"></slot>
    </header>
    <main>
      <slot></slot>
    </main>
    <footer>
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
</style>