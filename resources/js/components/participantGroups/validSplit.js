function validSplit(split, numParticipants) {
  if (!split.name) return false
  if (typeof split.name !== 'string') return false
  if (!split.name.trim()) return false
  return validSplitGroups(split, numParticipants)
}

function validSplitGroups(split, numParticipants) {
  if (!split.groups) return false
  const groups = parseInt(split.groups)
  if (isNaN(groups) || groups < 2 || groups > numParticipants) return false
  return true
}

export default validSplit
export { validSplitGroups }
