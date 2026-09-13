<template>
  <div class="matrix-analyzer" @click="jump">
    <div class="score">
      <div>Score: {{ score }}</div>
      <div class="highscore">Highscore: {{ highScore }}</div>
    </div>

    <div class="canvas-container">
      <canvas ref="gameCanvas" width="1600" height="800"></canvas>
      <div class="moon-glow">
        <img class="moon-mascot" :src="gameOver ? '/images/was-gaffsch.svg' : '/images/was_gaffsch.svg'" alt="Moon Mascot">
      </div>

      <div v-if="gameOver" class="game-over">
        <h2>Game Over</h2>
        <b-button @click.stop="resetGame" variant="primary">Try Again</b-button>
        <b-button @click.stop="$emit('exit')" variant="secondary" class="mt-3">Exit</b-button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RequirementsMatrixAnalyzer',
  data() {
    return {
      ctx: null,
      animationFrameId: null,
      score: 0,
      highScore: 0,
      gameOver: false,
      player: {
        x: 50,
        y: 240,
        width: 62,
        height: 100,
        dy: 0,
        gravity: 0.8,
        jumpForce: -16,
        grounded: true,
        images: {
          run1: null,
          run2: null,
          jump: null
        }
      },
      obstacles: [],
      frame: 0,
      nextSpawnFrame: 0,
      bgMusic: null
    }
  },
  mounted() {
    this.highScore = parseInt(localStorage.getItem('matrix_optimization_level') || '0', 10);
    this.bgMusic = new Audio('/sounds/md.webm');
    this.bgMusic.loop = true;
    this.bgMusic.volume = 0.5;

    this.initGame();
    window.addEventListener('keydown', this.handleKeydown);
  },
  beforeUnmount() {
    cancelAnimationFrame(this.animationFrameId);
    window.removeEventListener('keydown', this.handleKeydown);
    if (this.bgMusic) {
      this.bgMusic.pause();
      this.bgMusic.currentTime = 0;
    }
  },
  methods: {
    initGame() {
      const canvas = this.$refs.gameCanvas;
      this.ctx = canvas.getContext('2d');
      this.ctx.scale(2, 2);

      const loadImg = (b64Path) => {
        return new Promise(resolve => {
          const img = new Image();
          img.onload = () => resolve(img);
          img.onerror = () => resolve(null);
          img.src = atob(b64Path);
        });
      };

      Promise.all([
        loadImg('L3Nwcml0ZXMvd2FzLWdhZmZzY2gtcjEucG5n'),
        loadImg('L3Nwcml0ZXMvd2FzLWdhZmZzY2gtcjIucG5n'),
        loadImg('L3Nwcml0ZXMvd2FzLWdhZmZzY2gtai5wbmc=')
      ]).then(([run1, run2, jump]) => {
        this.player.images.run1 = run1;
        this.player.images.run2 = run2;
        this.player.images.jump = jump;
        this.resetGame();
      });
    },
    resetGame() {
      this.score = 0;
      this.gameOver = false;
      this.obstacles = [];
      this.player.y = 340 - this.player.height;
      this.player.dy = 0;
      this.frame = 0;
      this.nextSpawnFrame = 60 + Math.floor(Math.random() * 60);

      if (this.bgMusic) {
        this.bgMusic.currentTime = 0;
        this.bgMusic.play().catch(e => console.log('Audio play blocked', e));
      }

      this.gameLoop();
    },
    handleKeydown(e) {
      if (e.code === 'Space') {
        e.preventDefault();
        this.jump();
      }
    },
    jump() {
      if (this.gameOver) return;
      if (this.player.grounded) {
        this.player.dy = this.player.jumpForce;
        this.player.grounded = false;
      }
    },
    gameLoop() {
      if (this.gameOver) return;

      this.update();
      this.draw();

      this.animationFrameId = requestAnimationFrame(this.gameLoop);
    },
    update() {
      this.frame++;

      this.player.y += this.player.dy;
      if (this.player.y + this.player.height < 340) {
        this.player.dy += this.player.gravity;
        this.player.grounded = false;
      } else {
        this.player.dy = 0;
        this.player.grounded = true;
        this.player.y = 340 - this.player.height;
      }

      if (this.frame >= this.nextSpawnFrame) {
        let obsWidth = 30 + Math.random() * 10;
        let obsHeight = 40 + Math.random() * 20;
        this.obstacles.push({
          x: 800,
          y: 340 - obsHeight,
          width: obsWidth,
          height: obsHeight,
          speed: 6 + Math.floor(this.score / 5)
        });

        this.nextSpawnFrame = this.frame + 60 + Math.floor(Math.random() * 70);
      }

      for (let i = 0; i < this.obstacles.length; i++) {
        let obs = this.obstacles[i];
        obs.x -= obs.speed;

        let hitBoxX = this.player.x + 15;
        let hitBoxW = this.player.width - 30;
        let hitBoxY = this.player.y + 10;
        let hitBoxH = this.player.height - 20;

        if (
          hitBoxX < obs.x + obs.width - 10 &&
          hitBoxX + hitBoxW > obs.x + 10 &&
          hitBoxY < obs.y + obs.height - 10 &&
          hitBoxY + hitBoxH > obs.y + 10
        ) {
          this.gameOver = true;
          if (this.bgMusic) {
            this.bgMusic.pause();
          }
          if (this.score > this.highScore) {
            this.highScore = this.score;
            localStorage.setItem('matrix_optimization_level', this.highScore);
          }
        }
      }

      if (this.obstacles.length > 0 && this.obstacles[0].x < -50) {
        this.obstacles.shift();
        this.score++;
      }
    },
    drawMountainLayer(speed, color, heightBase, amplitude) {
      this.ctx.fillStyle = color;
      this.ctx.beginPath();
      this.ctx.moveTo(0, 400);
      let offset = (this.frame * speed) % 800;
      for (let x = 0; x <= 800; x += 20) {
          let worldX = x + offset;
          let y = heightBase
                - Math.sin(worldX * Math.PI / 400) * amplitude
                - Math.cos(worldX * Math.PI / 200) * (amplitude * 0.5)
                - Math.sin(worldX * Math.PI / 100) * (amplitude * 0.25);
          this.ctx.lineTo(x, y);
      }
      this.ctx.lineTo(800, 400);
      this.ctx.fill();
    },
    drawFlame(x, y, width, height) {
      const drawLayer = (color, scale, phaseOffset) => {
        this.ctx.fillStyle = color;
        this.ctx.beginPath();
        let fx = x + width/2;
        let fy = y + height;
        let flickerX = Math.sin(this.frame * 0.3 + phaseOffset) * 8 * scale;
        let flickerY = Math.cos(this.frame * 0.4 + phaseOffset) * 5 * scale;

        this.ctx.moveTo(fx, fy);
        this.ctx.quadraticCurveTo(
            fx + width*scale, fy,
            fx + flickerX, fy - height*scale + flickerY
        );
        this.ctx.quadraticCurveTo(
            fx - width*scale, fy,
            fx, fy
        );
        this.ctx.fill();
      };

      this.ctx.fillStyle = '#5C4033';
      this.ctx.fillRect(x + width*0.1, y + height - 5, width*0.8, 5);

      drawLayer('#ff4500', 1.0, 0);
      drawLayer('#ffa500', 0.7, 2);
      drawLayer('#ffff00', 0.4, 4);
    },
    draw() {
      this.ctx.clearRect(0, 0, 800, 400);

      let grad = this.ctx.createLinearGradient(0,0,0,340);
      grad.addColorStop(0, '#0f172a');
      grad.addColorStop(1, '#332940');
      this.ctx.fillStyle = grad;
      this.ctx.fillRect(0,0,800,400);


      this.drawMountainLayer(0.5, '#1e1b4b', 220, 50);
      this.drawMountainLayer(1.5, '#312e81', 280, 40);
      this.drawMountainLayer(3.0, '#4338ca', 330, 20);

      this.ctx.fillStyle = '#171717';
      this.ctx.fillRect(0, 340, 800, 60);

      let currentImg = this.player.images.run1;
      if (!this.player.grounded) {
        currentImg = this.player.images.jump;
      } else if (this.frame % 20 < 10) {
        currentImg = this.player.images.run2;
      }

      if (currentImg) {
        this.ctx.drawImage(currentImg, this.player.x, this.player.y, this.player.width, this.player.height);
      } else {
        this.ctx.fillStyle = 'red';
        this.ctx.fillRect(this.player.x, this.player.y, this.player.width, this.player.height);
      }

      for (let obs of this.obstacles) {
        this.drawFlame(obs.x, obs.y, obs.width, obs.height);
      }
    }
  }
}
</script>

<style scoped>
.matrix-analyzer {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.85);
  z-index: 2000;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.score {
  position: absolute;
  top: 20px;
  color: white;
  font-size: 24px;
  font-weight: bold;
  text-align: center;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
  z-index: 2010;
}
.highscore {
  font-size: 16px;
  color: #fbbf24;
}
.canvas-container {
  position: relative;
  width: 95vw;
  max-width: 1400px;
  aspect-ratio: 2 / 1;
}
canvas {
  width: 100%;
  height: 100%;
  border-radius: 8px;
  box-shadow: 0 0 30px rgba(0,0,0,0.8);
}
.moon-glow {
  position: absolute;
  top: 10%;
  left: 76.25%; /* 610px / 800px equivalent */
  width: 10%;
  height: 20%;
  background: #fdf5c9;
  border-radius: 50%;
  box-shadow: 0 0 30px #fef08a;
  pointer-events: none;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}
.moon-mascot {
  width: 293%;
  height: 293%;
  object-fit: contain;
  opacity: 0.4;
  pointer-events: none;
  mix-blend-mode: multiply;
  transform: translate(6%, 12%);
}
.game-over {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: rgba(30, 30, 30, 0.95);
  color: #ffffff;
  padding: 40px;
  border-radius: 12px;
  border: 1px solid #444;
  text-align: center;
  display: flex;
  flex-direction: column;
  box-shadow: 0 10px 30px rgba(0,0,0,0.8);
  z-index: 2020;
}
.game-over h2 {
  color: #ffffff;
  margin-bottom: 20px;
}
</style>
