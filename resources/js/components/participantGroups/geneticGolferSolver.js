/*
 * Code copied and adapted from https://github.com/islemaster/good-enough-golfers
 * This code is licensed under the MIT license.
 * Originally written by Brad Buchanan.
 */
import { shuffle, range, sortBy } from 'lodash'

const GENERATIONS = 30
const RANDOM_MUTATIONS = 2
const MAX_DESCENDANTS_TO_EXPLORE = 100

function geneticGolferSolver(numParticipants, roundSpecifications, onProgress) {
  function score(round, weights) {
    const groupScores = round.map(group => {
      let groupCost = 0
      forEachPair(group, (a, b) => groupCost += Math.pow(weights[a][b], 2))
      return groupCost
    })
    return {
      groups: round,
      groupsScores: groupScores,
      total: groupScores.reduce((sum, next) => sum + next, 0),
    }
  }

  function generatePermutation({ groups, ofSize }) {
    const people = shuffle(range(groups * ofSize))
    return range(groups).map(i =>
      range(ofSize).map(j =>
        people[i * ofSize + j]
      )
    )
  }

  function generateMutations(candidates, weights, roundSpecification) {
    const { ofSize } = roundSpecification
    const mutations = []
    candidates.forEach(candidate => {
      const scoredGroups = candidate.groups.map((g, i) => ({group: g, score: candidate.groupsScores[i]}))
      const sortedScoredGroups = sortBy(scoredGroups, sg => sg.score).reverse()
      const sorted = sortedScoredGroups.map(ssg => ssg.group)

      // Always push the original candidate back onto the list
      mutations.push(candidate)

      // Add every mutation that swaps somebody out of the most expensive group
      // (The first group is the most expensive now that we've sorted them)
      for (let i = 0; i < ofSize; i++) {
        for (let j = ofSize; j < weights.length; j++) {
          mutations.push(score(swap(sorted, i, j, roundSpecification), weights))
        }
      }

      // Add some random mutations to the search space to help break out of local peaks
      for (let i = 0; i < RANDOM_MUTATIONS; i++) {
        mutations.push(score(generatePermutation(roundSpecification), weights))
      }
    })
    return mutations;
  }

  function swap(groups, i, j, { ofSize }) {
    const copy = groups.map(group => group.slice())
    copy[Math.floor(i / ofSize)][i % ofSize] = groups[Math.floor(j / ofSize)][j % ofSize]
    copy[Math.floor(j / ofSize)][j % ofSize] = groups[Math.floor(i / ofSize)][i % ofSize]
    return copy
  }

  function updateWeights(round, weights) {
    for (const group of round) {
      forEachPair(group, (a, b) => {
        weights[a][b] = weights[b][a] = (weights[a][b] + 1)
      })
    }
  }

  function createWeights({ groups, ofSize, forbiddenPairs, discouragedGroups }) {
    const totalSize = groups * ofSize
    const weights = range(totalSize).map(() => range(totalSize).fill(0))

    // Fill some initial restrictions
    forbiddenPairs.forEach(group => {
      forEachPair(group, (a, b) => {
        if (a >= totalSize || b >= totalSize) return
        weights[a][b] = weights[b][a] = Infinity
      })
    })

    // Forbid leaving more than one empty slot per group
    if (totalSize > numParticipants) {
      range(numParticipants + 1, totalSize).forEach(emptySlot1 => {
        range(numParticipants + 1, totalSize).forEach(emptySlot2 => {
          weights[emptySlot1][emptySlot2] = weights[emptySlot2][emptySlot1] = Infinity
        })
      })
    }

    discouragedGroups.forEach(group => {
      forEachPair(group, (a, b) => {
        if (a >= totalSize || b >= totalSize) return
        weights[a][b] = weights[b][a] = (weights[a][b] + 1)
      })
    })

    return weights
  }

  function cleanUpGroups(groups) {
    return sortBy(groups
      // Remove any empty slot placeholder participants
      .map(group => group.filter(participant => participant < numParticipants)),
      // Sort smaller groups to the back
      (group) => -group.length
    )
  }

  const rounds = []
  const roundScores = []

  roundSpecifications.forEach((roundSpecification, index) => {
    const weights = createWeights(roundSpecification)
    // Previous rounds also count towards the weights
    rounds.forEach(previousRound => updateWeights(previousRound, weights))
    let topOptions = range(5).map(() => score(generatePermutation(roundSpecification), weights))
    let generation = 0
    while (generation < GENERATIONS && topOptions[0].total > 0) {
      const candidates = generateMutations(topOptions, weights, roundSpecification)
      let sorted = sortBy(candidates, c => c.total)
      const bestScore = sorted[0].total
      // Reduce to all the options that share the best score
      topOptions = sorted.slice(0, sorted.findIndex(opt => opt.total > bestScore))
      // Shuffle those options and only explore some maximum number of them
      topOptions = shuffle(topOptions).slice(0, MAX_DESCENDANTS_TO_EXPLORE)
      generation++;
    }
    const bestOption  = topOptions[0]
    // Filter out all empty slots from any groups
    rounds.push(cleanUpGroups(bestOption.groups))
    roundScores.push(bestOption.total)
    updateWeights(bestOption.groups, weights)

    onProgress({
      rounds,
      roundScores,
      weights,
      done: (index+1) >= roundSpecifications.length,
    })
  })
}

function forEachPair(array, callback) {
  for (let i = 0; i < array.length - 1; i++) {
    for (let j = i + 1; j < array.length; j++) {
      callback(array[i], array[j])
    }
  }
}

export default geneticGolferSolver
