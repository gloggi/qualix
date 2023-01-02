export default () => new Worker(new URL('./index.worker.js', import.meta.url))
