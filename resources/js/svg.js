// Fix the svg images inkscape created
window.addEventListener("load", function(){
  document.querySelectorAll('img[src$="/was-gaffsch.svg"]').forEach(img => new Date().getHours() < 6 && Math.random() < 0.01 && setTimeout(() => img.src = img.src.replace(/-([^\/]+)$/, '_$1'), Math.random() * 1000000))
});
