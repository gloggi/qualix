<template>
  <b-progress class="position-relative" :max="limit" >
    <span class="justify-content-center align-self-center d-flex position-absolute w-100"><strong class="ms-1"> {{ this.currentValue.length.toFixed(0) }} / {{ limit }}</strong></span>
    <b-progress-bar :value="this.currentValue.length" :class="progressBarClass"></b-progress-bar>
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
    progressBarClass() {
      let color = 'lightgreen';
      if(this.percentage >= 100){
        color = 'red';
      } else if(this.percentage > 90){
        color = 'orange';
      } else if(this.percentage > 75){
        color = 'yellow';
      }
      return `bg-${color}`;
    },
  }
}
</script>

<style scoped>

</style>
