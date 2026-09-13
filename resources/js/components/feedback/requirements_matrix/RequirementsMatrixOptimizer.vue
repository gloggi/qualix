<template>
  <div class="matrix-optimizer" @click="$emit('deactivate')">
    <div class="dark-overlay"></div>

    <div class="campfire-container" @click.stop="$emit('analyze')" title="Lagerfüür">
      <div class="campfire">
        <div class="flames">
          <div class="flame"></div>
          <div class="flame"></div>
          <div class="flame"></div>
        </div>
        <div class="logs">
          <div class="log"></div>
          <div class="log"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RequirementsMatrixOptimizer',
  data() {
    return {
      fireSound: null
    }
  },
  mounted() {
    this.fireSound = new Audio('/sounds/fire.mp3');
    this.fireSound.loop = true;
    this.fireSound.volume = 1.0;
    this.fireSound.play().catch(e => console.log('Audio play blocked', e));
  },
  beforeUnmount() {
    if (this.fireSound) {
      this.fireSound.pause();
      this.fireSound.currentTime = 0;
    }
  }
}
</script>

<style scoped>
.matrix-optimizer {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: flex-end;
  padding-bottom: 50px;
  cursor: pointer;
}

.dark-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.85);
  pointer-events: none;
}

.campfire-container {
  position: relative;
  z-index: 1001;
  transform: scale(3); /* Make the fire much bigger */
  transform-origin: bottom center;
  cursor: pointer;
  transition: transform 0.2s;
}
.campfire-container:hover {
  transform: scale(3.2);
}

.campfire {
  position: relative;
  width: 100px;
  height: 100px;
}
.logs {
  position: absolute;
  bottom: 0;
  width: 100%;
  height: 30px;
}
.log {
  position: absolute;
  width: 80px;
  height: 20px;
  background-color: #5C4033;
  border-radius: 10px;
  bottom: 5px;
  left: 10px;
}
.log:nth-child(1) { transform: rotate(15deg); }
.log:nth-child(2) { transform: rotate(-15deg); }

.flames {
  position: absolute;
  bottom: 20px;
  width: 100%;
  height: 80px;
  display: flex;
  justify-content: center;
  align-items: flex-end;
}
.flame {
  width: 30px;
  height: 30px;
  background-color: #FF4500;
  border-radius: 50% 0 50% 50%;
  transform: rotate(-45deg);
  animation: flicker 1s infinite alternate;
  position: absolute;
  bottom: 0;
}
.flame:nth-child(1) { background-color: #FF8C00; width: 40px; height: 40px; z-index: 2; animation-delay: 0.2s; }
.flame:nth-child(2) { left: 20px; bottom: -5px; animation-delay: 0.1s; }
.flame:nth-child(3) { right: 20px; bottom: -10px; animation-delay: 0.3s; }

@keyframes flicker {
  0% { transform: rotate(-45deg) scale(0.9); opacity: 0.8; }
  100% { transform: rotate(-45deg) scale(1.1); opacity: 1; }
}
</style>
