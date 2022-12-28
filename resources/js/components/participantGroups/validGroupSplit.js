function validSplit(split, numParticipants) {
  if (!split.name || !split.groups) return false
  if (typeof split.name !== 'string') return false
  if (!split.name.trim()) return false
  const groups = parseInt(split.groups)
  if (isNaN(groups) || groups < 2 || groups > numParticipants) return false
  return true
}

export default validSplit
