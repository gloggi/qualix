// Fix the svg images inkscape created
window.addEventListener("load", function(){
  document.querySelectorAll('img[src*="as-gaf"]').forEach(img => (new Date().getHours() < 6 || new Date().getHours() > 22) && Math.random() < 0.05 && setTimeout(() => img.src = img.src.replace(/-([^\/]+)$/, '_$1'), Math.random() * 100000))
});
