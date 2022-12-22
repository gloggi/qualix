import geneticGolferSolver from './geneticGolferSolver.js'

self.onmessage = function(e) {
  // Any message from the host page starts a new computation
  const { numParticipants, rounds } = e.data
  // Compute results and send them back to the host page
  geneticGolferSolver(numParticipants, rounds, (results) => {
    self.postMessage(results)
  })
}
