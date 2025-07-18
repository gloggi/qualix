function validSplit(split, numParticipants) {
  if (!split.name) return false
  if (typeof split.name !== 'string') return false
  if (!split.name.trim()) return false
  return validSplitGroups(split.groups, numParticipants)
}

function validSplitGroups(groups, numParticipants) {
  if (!groups) return false
  const groupsInt = parseInt(groups)
  if (isNaN(groupsInt) || groupsInt < 2 || groupsInt > numParticipants) return false
  return true
}

export default validSplit
export { validSplitGroups }
