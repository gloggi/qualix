<template>
  <div>
    <div class="progress position-relative">
      <div class="progress-bar" role="progressbar" v-bind:style="progressBarStyles" :aria-valuenow="percentage" aria-valuemin="0" :aria-valuemax="limit"></div>
      <span class="justify-content-center align-self-center d-flex position-absolute w-100">{{this.$t('t.views.observations.char_limit')}} <strong class="ml-1"> {{ this.currentValue.length.toFixed(0) }} / {{ limit }}</strong></span>
    </div>

  </div>
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
        width: this.percentage + '%'
      }
    },

  }
}
</script>

<style scoped>

</style>
