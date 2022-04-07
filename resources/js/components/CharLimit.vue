<template>
  <b-progress class="position-relative" :max="limit" >
    <span class="justify-content-center align-self-center d-flex position-absolute w-100"><strong class="ml-1"> {{ this.currentValue.length.toFixed(0) }} / {{ limit }}</strong></span>
    <b-progress-bar :value="this.currentValue.length" :style="progressBarStyles"></b-progress-bar>
  </b-progress>
</template>

<script>
export default {
  name: "CharLimit",
  props: {
    currentValue: {type: String, required: true},
    limit: {type: Number, required: true },
  },
  computed: {
    percentage: function (){
      return 100 * this.currentValue.length / this.limit;
    },
    progressBarColor() {
      let color = 'lightgreen';
      if(this.percentage >= 100){
        color = 'red';
      } else if(this.percentage > 90){
        color = 'salmon';
      } else if(this.percentage > 75){
        color = 'lemonchiffon';
      }
      return color;
    },
    progressBarStyles() {
      return {
        'background-color': this.progressBarColor,
      }
    },

  }
}
</script>

<style scoped>

</style>
