<template>
  <span class="number">{{ animatedNumber.toFixed(2) }}</span>
</template>

<script>
export default {
  name: "AnimatedNumber",
  props: {
    number: Number,
  },
  data: function () {
    return {
      animatedNumber: 0,
      interval: false,
    };
  },
  mounted() {
    this.animatedNumber = this.number ? this.number : 0;
  },
  watch: {
    number: function () {
      clearInterval(this.interval);
      this.number = Number(this.number);
      if (this.number == this.animatedNumber) {
        return;
      }

      this.interval = window.setInterval(
        function () {
          if (this.animatedNumber != this.number) {
            var change = (this.number - this.animatedNumber) / 10;

            change = change >= 0 ? Math.ceil(change) : Math.floor(change);

            this.animatedNumber = this.animatedNumber + change;
          }
        }.bind(this),
        20
      );
    },
  },
};
</script>



<style scoped>
</style>