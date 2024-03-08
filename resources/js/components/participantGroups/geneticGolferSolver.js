/*
 * Code copied and adapted from https://github.com/islemaster/good-enough-golfers
 * This code is licensed under the MIT license.
 * Originally written by Brad Buchanan.
 */
import { shuffle, range, sortBy, zip, unzip } from 'lodash'

const GENERATIONS = 30
const RANDOM_MUTATIONS = 2
const MAX_DESCENDANTS_TO_EXPLORE = 100
const MAX_PERMUTATIONS_TO_TRY = 100

function geneticGolferSolver(numParticipants, roundSpecifications, onProgress) {
  function score(round, weights) {
    const groupScores = round.map(group => {
      let groupCost = 0
      forEachPair(group, (a, b) => groupCost += Math.sign(weights[a][b]) * Math.pow(weights[a][b], 2))
      return groupCost
    })
    return {
      groups: round,
      groupsScores: groupScores,
      total: groupScores.reduce((sum, next) => sum + next, 0),
    }
  }

  function potentialFor(group, weights) {
    return -group.map(member => {
      return weights[member]
        .filter((_, index) => !group.includes(index))
        .reduce((sum, weight) => sum + Math.sign(weight) * Math.pow(weight, 2), 0)
    }).reduce((sum, memberSum) => sum + memberSum, 0)
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
      const scoredGroups = candidate.groups.map((g, i) => ({group: g, score: candidate.groupsScores[i], potential: potentialFor(g, weights)}))
      const sortedScoredGroups = sortBy(scoredGroups, ['score', 'potential']).reverse()
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

  function createWeights({ groups, ofSize, forbiddenPairings: forbiddenPairs, discouragedPairings: discouragedGroups, encouragedPairings: encouragedPairs }) {
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
      forEachPair(range(numParticipants, totalSize), (emptySlot1, emptySlot2) => {
        weights[emptySlot1][emptySlot2] = weights[emptySlot2][emptySlot1] = Infinity
      })
    }

    discouragedGroups.forEach(group => {
      forEachPair(group, (a, b) => {
        if (a >= totalSize || b >= totalSize) return
        weights[a][b] = weights[b][a] = (weights[a][b] + 1)
      })
    })

    // Encouraged pairings override all previous discouragement and even make it beneficial to pair the participants
    encouragedPairs.forEach(group => {
      forEachPair(group, (a, b) => {
        if (a >= totalSize || b >= totalSize) return
        weights[a][b] = weights[b][a] = -1
      })
    })

    return weights
  }

  function cleanUpGroups(groups) {
    return sortBy(groups
      // Remove any empty slot placeholder participants
      .map(group => group.filter(participant => participant < numParticipants))
      // Sort the participants inside the groups in their original ordering
        .map(group => sortBy(group)),
      // Sort smaller groups to the back, and then sort alphabetically by the first group member
      [(group => -group.length), (group => group[0])]
    )
  }

  function optimizeOrderedRounds(roundSpecifications) {
    const rounds = []
    const roundScores = []
    let roundsWithoutViolations = 0

    roundSpecifications.forEach((roundSpecification, index) => {
      const weights = createWeights(roundSpecification)
      // Previous rounds also count towards the weights
      rounds.forEach(previousRound => updateWeights(previousRound, weights))
      let topOptions = range(5).map(() => score(generatePermutation(roundSpecification), weights))
      let generation = 0
      // We can exit early only if there are no encouraged pairings, because in that case, there can't be any
      // negative weights
      while (generation < GENERATIONS && (roundSpecification.encouragedPairings.length || topOptions[0].total > 0)) {
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
      if (bestOption.total < Infinity) {
        roundsWithoutViolations++
      }
      // Filter out all empty slots from any groups
      rounds.push(cleanUpGroups(bestOption.groups))
      roundScores.push(bestOption.total)
      updateWeights(bestOption.groups, weights)
    })

    return {
      rounds,
      roundScores,
      roundsWithoutViolations,
    }
  }

  function* permute(permutation) {
    const length = permutation.length
    const c = Array(length).fill(0)
    let i = 1
    let k
    let p

    yield permutation.slice();
    while (i < length) {
      if (c[i] < i) {
        k = i % 2 && c[i];
        p = permutation[i];
        permutation[i] = permutation[k];
        permutation[k] = p;
        ++c[i];
        i = 1;
        yield permutation.slice();
      } else {
        c[i] = 0;
        ++i;
      }
    }
  }

  function reorder(array, ordering) {
    return ordering.map(originalLocation => array[originalLocation])
  }

  let progress = 0
  let bestResult = null
  let bestScore = null
  // The primary score can easily reach infinity (as bad as possible) if there are many rounds or large group sizes,
  // in combination with forbidden pairings.
  // So we need a secondary and tertiary score to rank permutations with infinite primary score.
  let bestSecondaryScore = null
  let bestTertiaryScore = null
  const numRounds = roundSpecifications.length
  const numPossiblePermutations = range(1, numRounds+1).reduce((factorial, i) => factorial*i, 1)
  const numTriedPermutations = Math.min(MAX_PERMUTATIONS_TO_TRY, numPossiblePermutations)
  for (let permutation of permute(range(numRounds))) {
    // randomly sample whether to skip this permutation
    if (Math.random() * numPossiblePermutations > numTriedPermutations) {
      continue
    }

    // create the permutation of the input
    const reorderedRounds = reorder(roundSpecifications, permutation)
    // run the genetic algorithm
    const result = optimizeOrderedRounds(reorderedRounds)
    // record the best result (lower score is better)
    const totalScore = result.roundScores.reduce((sum, score) => sum + score, 0)
    const secondaryScore = -result.roundsWithoutViolations // with the number of rounds without violations, higher is better
    const tertiaryScore = result.roundScores.filter(score => score < Infinity).reduce((sum, score) => sum + score, 0)
    if (bestScore === null ||
      totalScore < bestScore ||
      (totalScore === bestScore && secondaryScore < bestSecondaryScore) ||
      (totalScore === bestScore && secondaryScore === bestSecondaryScore && tertiaryScore < bestTertiaryScore)
    ) {
      bestScore = totalScore
      bestSecondaryScore = secondaryScore
      bestTertiaryScore = tertiaryScore

      const inversePermutation = unzip(sortBy(zip(permutation, range(numRounds)), e => e[0]))[1]
      bestResult = {
        rounds: reorder(result.rounds, inversePermutation),
        roundScores: reorder(result.roundScores, inversePermutation),
      }
    }
    progress++
    onProgress({ ...result, done: false, progress: Math.min(100, Math.round(100. * progress / numTriedPermutations)) })
  }
  onProgress({ ...bestResult, done: true, progress: 100 })
}

function forEachPair(array, callback) {
  for (let i = 0; i < array.length - 1; i++) {
    for (let j = i + 1; j < array.length; j++) {
      callback(array[i], array[j])
    }
  }
}

export default geneticGolferSolver
