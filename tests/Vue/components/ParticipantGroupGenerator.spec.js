import { render, screen, waitFor, within } from '@testing-library/vue'
import i18n from '../../../resources/js/i18n'
import userEvent from '@testing-library/user-event'
import ParticipantGroupGenerator from '../../../resources/js/components/participantGroups/ParticipantGroupGenerator'
import mockGeneticGolferSolver from '../../../resources/js/components/participantGroups/geneticGolferSolver'

// For some of the tests, the generated groups are random, and we need to try a few times before we get
// a random generation which fits our test expectations.
const NUM_RETRIES = 100

jest.mock('../../../resources/js/components/participantGroups/createWorker.js', () => {
  return () => ({
    addEventListener(message, listener) {
      this.listener = listener
    },
    postMessage({ numParticipants, rounds }) {
      mockGeneticGolferSolver(numParticipants, rounds, (results) => {
        if (!this.listener) {
          throw new Error('No listener registered for geneticGolferSolver')
        }
        this.listener({ data: results })
      })
    }
  })
})

const participants = [
  { id: 101, scout_name: 'Alpha', name_and_group: 'Alpha (Kreta)', group: 'Kreta' },
  { id: 102, scout_name: 'Beta', name_and_group: 'Beta (Kreta)', group: 'Kreta' },
  { id: 103, scout_name: 'Gamma', name_and_group: 'Gamma (Kreta)', group: 'Kreta' },
  { id: 104, scout_name: 'Delta', name_and_group: 'Delta (Kreta)', group: 'Kreta' },
  { id: 105, scout_name: 'Epsilon', name_and_group: 'Epsilon (Mykonos)', group: 'Mykonos' },
  { id: 106, scout_name: 'Pi', name_and_group: 'Pi (Mykonos)', group: 'Mykonos' },
  { id: 107, scout_name: 'Rho', name_and_group: 'Rho (Mykonos)', group: 'Mykonos' },
  { id: 108, scout_name: 'Omega', name_and_group: 'Omega (Mykonos)', group: 'Mykonos' },
]

const participantGroups = [
  { id: 201, group_name: 'Troublemakers', participants: [participants[0], participants[1], participants[7]] }
]

describe('participant group generator', () => {
  it('can generate groups, set the number and name of groups', async () => {
    // given
    render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
    const splitName = screen.getByLabelText('Bezeichnung')
    const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
    const generateButton = screen.getByText('Gruppenvorschlag generieren')

    // when
    await userEvent.click(generateButton)

    // then
    await waitFor(() => {
      expect(screen.getByDisplayValue('Arbeitsgruppe 1')).toBeVisible()
      expect(screen.getByDisplayValue('Arbeitsgruppe 2')).toBeVisible()
      expect(screen.queryByDisplayValue('Arbeitsgruppe 3')).not.toBeInTheDocument()
    })

    // when
    await userEvent.clear(splitName)
    await userEvent.type(splitName, 'Unternehmung')
    await userEvent.clear(numberOfGroups)
    await userEvent.type(numberOfGroups, '4')
    await userEvent.click(generateButton)

    // then
    await waitFor(() => {
      expect(screen.queryByDisplayValue('Arbeitsgruppe 1')).not.toBeInTheDocument()
      expect(screen.queryByDisplayValue('Arbeitsgruppe 2')).not.toBeInTheDocument()
      expect(screen.queryByDisplayValue('Arbeitsgruppe 3')).not.toBeInTheDocument()
      expect(screen.getByDisplayValue('Unternehmung 1')).toBeVisible()
      expect(screen.getByDisplayValue('Unternehmung 2')).toBeVisible()
      expect(screen.getByDisplayValue('Unternehmung 3')).toBeVisible()
      expect(screen.getByDisplayValue('Unternehmung 4')).toBeVisible()
      expect(screen.queryByDisplayValue('Unternehmung 5')).not.toBeInTheDocument()
    })
  })

  describe('single group split', () => {
    describe('name input', () => {
      it('refuses to generate groups if a split has no name', async () => {
        // given
        render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
        const splitName = screen.getByLabelText('Bezeichnung')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        // when
        await userEvent.clear(splitName)

        // then
        await waitFor(() => {
          expect(generateButton).toBeDisabled()
        })
      })
    })

    describe('group size input and indicator', () => {
      it('offers to create groups of size 4 by default', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups}})
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        // when

        // then
        await waitFor(() => {
          expect(screen.getByDisplayValue('2')).toBeVisible()
          expect(screen.getByText('Gruppen mit je 4 TN')).toBeVisible()
          expect(generateButton).not.toBeDisabled()
        })
      })

      it('re-calculates group size', async () => {
        // given
        render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        // when
        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '3')

        // then
        await waitFor(() => {
          expect(screen.queryByText('Gruppen mit je 4 TN')).not.toBeInTheDocument()
          expect(screen.getByText('Gruppen mit je 2-3 TN')).toBeVisible()
          expect(generateButton).not.toBeDisabled()
        })
      })

      it('refuses numbers of groups greater than the number of participants', async () => {
        // given
        render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        // when
        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '9')

        // then
        await waitFor(() => {
          expect(screen.queryByText('Gruppen mit je 4 TN')).not.toBeInTheDocument()
          expect(screen.getByText('Bitte Gruppenanzahl eingeben')).toBeVisible()
          expect(generateButton).toBeDisabled()
        })
      })

      it('refuses numbers of groups smaller than 2', async () => {
        // given
        render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        // when
        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '1')

        // then
        await waitFor(() => {
          expect(screen.queryByText('Gruppen mit je 4 TN')).not.toBeInTheDocument()
          expect(screen.getByText('Bitte Gruppenanzahl eingeben')).toBeVisible()
          expect(generateButton).toBeDisabled()
        })
      })

      it('handles invalid number of groups', async () => {
        // given
        render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        // when
        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, 'x')

        // then
        await waitFor(() => {
          expect(screen.queryByText('Gruppen mit je 4 TN')).not.toBeInTheDocument()
          expect(screen.getByText('Bitte Gruppenanzahl eingeben')).toBeVisible()
          expect(generateButton).toBeDisabled()
        })
      })
    })

    describe('membership group separation toggle', () => {
      it('is hidden if there are no membership groups', async () => {
        // given
        const participantsWithoutMembershipGroups = participants.map(participant => ({
          ...participant,
          group: null
        }))
        render(ParticipantGroupGenerator, {i18n, props: {participants: participantsWithoutMembershipGroups, participantGroups: []}})

        // when

        // then
        // toggle should not become visible / not be found in the document
        await expect(waitFor(() => {
          expect(screen.getByLabelText('Abteilungen unbedingt durchmischen')).toBeVisible()
        })).rejects.toThrow(/Unable to find a label with the text of/)
      })

      it('is hidden if there is only one membership group', async () => {
        // given
        const participantsWithSameMembershipGroup = participants.map(participant => ({
          ...participant,
          group: 'Athen'
        }))
        render(ParticipantGroupGenerator, {i18n, props: {participants: participantsWithSameMembershipGroup, participantGroups: []}})

        // when

        // then
        // toggle should not become visible / not be found in the document
        await expect(waitFor(() => {
          expect(screen.getByLabelText('Abteilungen unbedingt durchmischen')).toBeVisible()
        })).rejects.toThrow(/Received element is not visible/)
      })

      it('separates participants from the same membership group if activated', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const membershipGroupToggle = screen.getByLabelText('Abteilungen unbedingt durchmischen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')

        // when
        await userEvent.click(membershipGroupToggle)

        for(let i = 0; i < NUM_RETRIES; i++) {
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha (Kreta)' && groups[0][0][1] === 'Beta (Kreta)') {
            throw new Error(`The participants Alpha and Beta from the same membership group were generated into the same group`)
          }
        }
      })

      it('does not separate participants from the same membership group if not activated', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')

        for(let i = 0; i < NUM_RETRIES; i++) {
          // when
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha' && groups[0][0][1] === 'Beta') {
            // success
            return
          }
        }
        throw new Error(`The participants Alpha and Beta were never together in a group within ${NUM_RETRIES} tries`)
      })

      it('separates participants only in the group splits which have it activated', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const membershipGroupToggle = screen.getByLabelText('Abteilungen unbedingt durchmischen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')
        const addSplitLink = screen.getByText('Weitere Gruppenaufteilung hinzufügen')
        const splitName = screen.getByLabelText('Bezeichnung')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')
        await userEvent.clear(splitName)
        await userEvent.type(splitName, 'Unternehmung')

        await userEvent.click(addSplitLink)
        const secondSplitNumberOfGroups = screen.getAllByLabelText('Anzahl Gruppen')[1]

        await userEvent.clear(secondSplitNumberOfGroups)
        await userEvent.type(secondSplitNumberOfGroups, '4')

        // when
        await userEvent.click(membershipGroupToggle)

        let secondSplitHadAlphaAndBetaTogether = false
        for(let i = 0; i < NUM_RETRIES; i++) {
          // when
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha (Kreta)' && groups[0][0][1] === 'Beta (Kreta)') {
            throw new Error(`The participants Alpha and Beta from the same membership group were generated into the same group`)
          }
          if (groups[1][0][0] === 'Alpha (Kreta)' && groups[1][0][1] === 'Beta (Kreta)') {
            secondSplitHadAlphaAndBetaTogether = true
          }
        }
        if (!secondSplitHadAlphaAndBetaTogether) {
          throw new Error(`The participants Alpha and Beta were never together in a group in the second group split within ${NUM_RETRIES} tries`)
        }
      })

      it('only adds the membership group to the result display, if the toggle is set', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const membershipGroupToggle = screen.getByLabelText('Abteilungen unbedingt durchmischen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        // when
        await userEvent.click(membershipGroupToggle)
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        expect(groups[0][0][0]).toBe('Alpha (Kreta)')
        expect(groups[0][0][0]).not.toBe('Alpha')

        // when
        await userEvent.click(membershipGroupToggle)
        await userEvent.click(generateButton)

        // then
        const groups2 = await waitForGeneratedGroups()
        expect(groups2[0][0][0]).not.toBe('Alpha (Kreta)')
        expect(groups2[0][0][0]).toBe('Alpha')
      })
    })

    describe('participant separation selection', () => {
      it('separates selected participants in the generated groups', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const separationMultiselect = screen.getByLabelText('Folgende TN-Kombinationen trennen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')

        // when
        // select the first two participants in the multiselect menu
        await userEvent.click(separationMultiselect)
        const firstParticipant = within(separationMultiselect).getByText('Alpha (Kreta)')
        const secondParticipant = within(separationMultiselect).getByText('Beta (Kreta)')
        await userEvent.click(firstParticipant)
        await userEvent.click(secondParticipant)
        await userEvent.click(separationMultiselect)

        for(let i = 0; i < NUM_RETRIES; i++) {
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha (Kreta)' && groups[0][0][1] === 'Beta (Kreta)') {
            throw new Error(`The participants Alpha and Beta from the same membership group were generated into the same group`)
          }
        }
      })

      it('does not separate participants if not selected', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')

        for(let i = 0; i < NUM_RETRIES; i++) {
          // when
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha' && groups[0][0][1] === 'Beta') {
            // success
            return
          }
        }
        throw new Error(`The participants Alpha and Beta were never together in a group within ${NUM_RETRIES} tries`)
      })

      it('separates participants only in the group splits which have it activated', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const separationMultiselect = screen.getByLabelText('Folgende TN-Kombinationen trennen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')
        const addSplitLink = screen.getByText('Weitere Gruppenaufteilung hinzufügen')
        const splitName = screen.getByLabelText('Bezeichnung')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')
        await userEvent.clear(splitName)
        await userEvent.type(splitName, 'Unternehmung')

        await userEvent.click(addSplitLink)
        const secondSplitNumberOfGroups = screen.getAllByLabelText('Anzahl Gruppen')[1]

        await userEvent.clear(secondSplitNumberOfGroups)
        await userEvent.type(secondSplitNumberOfGroups, '4')

        // when
        // select the first two participants in the multiselect menu
        await userEvent.click(separationMultiselect)
        const firstParticipant = within(separationMultiselect).getByText('Alpha (Kreta)')
        const secondParticipant = within(separationMultiselect).getByText('Beta (Kreta)')
        await userEvent.click(firstParticipant)
        await userEvent.click(secondParticipant)
        await userEvent.click(separationMultiselect)

        let secondSplitHadAlphaAndBetaTogether = false
        for(let i = 0; i < NUM_RETRIES; i++) {
          // when
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha' && groups[0][0][1] === 'Beta') {
            throw new Error(`The participants Alpha and Beta were generated into the same group, even though they were specified to be separated`)
          }
          if (groups[1][0][0] === 'Alpha' && groups[1][0][1] === 'Beta') {
            secondSplitHadAlphaAndBetaTogether = true
          }
        }
        if (!secondSplitHadAlphaAndBetaTogether) {
          throw new Error(`The participants Alpha and Beta were never together in a group in the second group split within ${NUM_RETRIES} tries`)
        }
      })
    })

    describe('participant combination selection', () => {
      it('always combines selected participants in the generated groups', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const combinationMultiselect = screen.getByLabelText('Folgende TN-Kombinationen möglichst zusammen in eine Gruppe einteilen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')

        // when
        // select the first two participants in the multiselect menu
        await userEvent.click(combinationMultiselect)
        const firstParticipant = within(combinationMultiselect).getByText('Alpha (Kreta)')
        const secondParticipant = within(combinationMultiselect).getByText('Beta (Kreta)')
        await userEvent.click(firstParticipant)
        await userEvent.click(secondParticipant)
        await userEvent.click(combinationMultiselect)

        for(let i = 0; i < NUM_RETRIES; i++) {
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha' && groups[0][0][1] !== 'Beta') {
            throw new Error(`The participants Alpha and Beta from the same membership group were not generated into the same group`)
          }
        }
      })

      it('does not always combine participants if not selected', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')

        for(let i = 0; i < NUM_RETRIES; i++) {
          // when
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha' && groups[0][0][1] !== 'Beta') {
            // success
            return
          }
        }
        throw new Error(`The participants Alpha and Beta were always together in a group within ${NUM_RETRIES} tries`)
      })

      it('combines participants only in the group splits which have it activated', async () => {
        // given
        render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
        const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
        const combinationMultiselect = screen.getByLabelText('Folgende TN-Kombinationen möglichst zusammen in eine Gruppe einteilen')
        const generateButton = screen.getByText('Gruppenvorschlag generieren')
        const addSplitLink = screen.getByText('Weitere Gruppenaufteilung hinzufügen')
        const splitName = screen.getByLabelText('Bezeichnung')

        await userEvent.clear(numberOfGroups)
        await userEvent.type(numberOfGroups, '4')
        await userEvent.clear(splitName)
        await userEvent.type(splitName, 'Unternehmung')

        await userEvent.click(addSplitLink)
        const secondSplitNumberOfGroups = screen.getAllByLabelText('Anzahl Gruppen')[1]

        await userEvent.clear(secondSplitNumberOfGroups)
        await userEvent.type(secondSplitNumberOfGroups, '4')

        // when
        // select the first two participants in the multiselect menu
        await userEvent.click(combinationMultiselect)
        const firstParticipant = within(combinationMultiselect).getByText('Alpha (Kreta)')
        const secondParticipant = within(combinationMultiselect).getByText('Beta (Kreta)')
        await userEvent.click(firstParticipant)
        await userEvent.click(secondParticipant)
        await userEvent.click(combinationMultiselect)

        let secondSplitHadAlphaAndBetaSeparated = false
        for(let i = 0; i < NUM_RETRIES; i++) {
          // when
          await userEvent.click(generateButton)

          // then
          const groups = await waitForGeneratedGroups()
          if (groups[0][0][0] === 'Alpha' && groups[0][0][1] !== 'Beta') {
            throw new Error(`The participants Alpha and Beta were not generated into the same group, even though they were specified to be combined`)
          }
          if (groups[1][0][0] === 'Alpha' && groups[1][0][1] !== 'Beta') {
            secondSplitHadAlphaAndBetaSeparated = true
          }
        }
        if (!secondSplitHadAlphaAndBetaSeparated) {
          throw new Error(`The participants Alpha and Beta were always together in a group in the second group split within ${NUM_RETRIES} tries`)
        }
      })
    })
  })

  it('can add more group splits', async () => {
    // given
    render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
    const addSplitLink = screen.getByText('Weitere Gruppenaufteilung hinzufügen')

    await waitFor(() => {
      expect(screen.getAllByLabelText('Bezeichnung').length).toBe(1)
      expect(screen.getAllByLabelText('Anzahl Gruppen').length).toBe(1)
    })

    // when
    await userEvent.click(addSplitLink)

    // then
    await waitFor(() => {
      expect(screen.getAllByLabelText('Bezeichnung').length).toBe(2)
      expect(screen.getAllByLabelText('Anzahl Gruppen').length).toBe(2)
    })
  })

  describe('participant selection', () => {
    it('re-calculates group sizes', async () => {
      // given
      render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')
      const collapseLink = screen.getByText('Erweiterte Bedingungen für alle Gruppenaufteilungen')
      const participantSelection = screen.getByLabelText('Zu gruppierende TN')
      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '4')
      await waitFor(() => {
        expect(screen.getByText('Gruppen mit je 2 TN')).toBeVisible()
        expect(generateButton).not.toBeDisabled()
      })

      // when
      toggleCollapse(collapseLink)
      await waitFor(async () => {
        expect(participantSelection).toBeVisible()
      })
      await userEvent.click(participantSelection)
      const firstParticipant = within(participantSelection).getAllByText('Alpha (Kreta)')[1]
      await userEvent.click(firstParticipant) // deselect the first participant
      await userEvent.click(participantSelection) // close the multiselect

      // then
      await waitFor(() => {
        expect(screen.getByText('Gruppen mit je 1-2 TN')).toBeVisible()
        expect(generateButton).not.toBeDisabled()
      })
    })

    it('refuses subsequent generation if group size is larger than number of selected participants', async () => {
      // given
      render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')
      const collapseLink = screen.getByText('Erweiterte Bedingungen für alle Gruppenaufteilungen')
      const participantSelection = screen.getByLabelText('Zu gruppierende TN')
      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '8')
      await waitFor(() => {
        expect(screen.getByText('Gruppen mit je 1 TN')).toBeVisible()
        expect(generateButton).not.toBeDisabled()
      })

      // when
      toggleCollapse(collapseLink)
      await waitFor(async () => {
        expect(participantSelection).toBeVisible()
      })
      await userEvent.click(participantSelection)
      const firstParticipant = within(participantSelection).getAllByText('Alpha (Kreta)')[1]
      await userEvent.click(firstParticipant) // deselect the first participant
      await userEvent.click(participantSelection) // close the multiselect

      // then
      await waitFor(() => {
        expect(screen.getByText('Bitte Gruppenanzahl eingeben')).toBeVisible()
        expect(generateButton).toBeDisabled()
      })
    })

    it('leaves the deselected participant out of the generated groups', async () => {
      // given
      render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
      const generateButton = screen.getByText('Gruppenvorschlag generieren')
      const collapseLink = screen.getByText('Erweiterte Bedingungen für alle Gruppenaufteilungen')
      const participantSelection = screen.getByLabelText('Zu gruppierende TN')

      toggleCollapse(collapseLink)
      await waitFor(async () => {
        expect(participantSelection).toBeVisible()
      })
      await userEvent.click(participantSelection)
      const firstParticipant = within(participantSelection).getAllByText('Alpha (Kreta)')[1]
      await userEvent.click(firstParticipant) // deselect the first participant
      await userEvent.click(participantSelection) // close the multiselect

      // when
      await userEvent.click(generateButton)

      // then
      await waitFor(() => {
        expect(screen.getByDisplayValue('Arbeitsgruppe 1')).toBeVisible()
        expect(screen.getByDisplayValue('Arbeitsgruppe 2')).toBeVisible()
        expect(screen.queryByDisplayValue('Arbeitsgruppe 3')).not.toBeInTheDocument()
        expect(screen.queryByText('Alpha (Kreta)')).not.toBeVisible()
      })
    })
  })

  describe('existing participant group selection', () => {
    it('does not allow the participants in the selected existing participant groups to be in the same group', async () => {
      // given
      render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')
      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '3')
      // by default, all existing participant groups are selected for collision avoidance

      for(let i = 0; i < NUM_RETRIES; i++) {
        // when
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        if (groups[0][0][0] === 'Alpha' && groups[0][0][1] === 'Beta' && groups[0][0][2] === 'Omega') {
          throw new Error(`The participants Alpha, Beta and Omega were generated into the same group`)
        }
      }
    })

    it('allows the participants in the deselected existing participant groups to be in the same group', async () => {
      // given
      render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups } })
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')
      const collapseLink = screen.getByText('Erweiterte Bedingungen für alle Gruppenaufteilungen')
      const participantGroupSelection = screen.getByLabelText('Überschneidungen mit bestehenden TN-Gruppen vermeiden')

      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '3')

      toggleCollapse(collapseLink)
      await waitFor(async () => {
        expect(participantGroupSelection).toBeVisible()
      })
      await userEvent.click(participantGroupSelection)
      const firstParticipant = within(participantGroupSelection).getAllByText('Troublemakers')[1]
      await userEvent.click(firstParticipant) // deselect the first existing participant group
      await userEvent.click(participantGroupSelection) // close the multiselect

      for(let i = 0; i < NUM_RETRIES; i++) {
        // when
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        if (groups[0][0][0] === 'Alpha' && groups[0][0][1] === 'Beta' && groups[0][0][2] === 'Omega') {
          // success
          return
        }
      }
      throw new Error(`The participants Alpha, Beta and Omega were never together in a group within ${NUM_RETRIES} tries`)
    })

    it('does not offer selecting existing participant groups if there are none', async () => {
      // given
      render(ParticipantGroupGenerator, { i18n, props: { participants, participantGroups: [] } })

      // when

      // then
      // multiselect should not become visible / not be found in the document
      await expect(waitFor(() => {
        expect(screen.getByLabelText('Überschneidungen mit bestehenden TN-Gruppen vermeiden')).toBeVisible()
      })).rejects.toThrow(/Unable to find a label with the text of/)
    })
  })

  describe('global membership group separation toggle', () => {
    it('is hidden if there are no membership groups', async () => {
      // given
      const participantsWithoutMembershipGroups = participants.map(participant => ({
        ...participant,
        group: null
      }))
      render(ParticipantGroupGenerator, {i18n, props: {participants: participantsWithoutMembershipGroups, participantGroups: []}})

      // when

      // then
      // toggle should not become visible / not be found in the document
      await expect(waitFor(() => {
        expect(screen.getByLabelText('Abteilungs-durchmischte Gruppen generell bevorzugen')).toBeVisible()
      })).rejects.toThrow(/Unable to find a label with the text of/)
    })

    it('is hidden if there is only one membership group', async () => {
      // given
      const participantsWithSameMembershipGroup = participants.map(participant => ({
        ...participant,
        group: 'Athen'
      }))
      render(ParticipantGroupGenerator, {i18n, props: {participants: participantsWithSameMembershipGroup, participantGroups: []}})

      // when

      // then
      // toggle should not become visible / not be found in the document
      await expect(waitFor(() => {
        expect(screen.getByLabelText('Abteilungs-durchmischte Gruppen generell bevorzugen')).toBeVisible()
      })).rejects.toThrow(/Received element is not visible/)
    })

    it('separates participants from the same membership group if activated', async () => {
      // given
      render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const membershipGroupToggle = screen.getByLabelText('Abteilungs-durchmischte Gruppen generell bevorzugen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')

      await userEvent.click(membershipGroupToggle)

      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '4')

      for(let i = 0; i < NUM_RETRIES; i++) {
        // when
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        if (groups[0][0][0] === 'Alpha (Kreta)' && groups[0][0][1] === 'Beta (Kreta)') {
          throw new Error(`The participants Alpha and Beta from the same membership group were generated into the same group`)
        }
      }
    })

    it('does not separate participants from the same membership group if not activated', async () => {
      // given
      render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')

      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '4')

      for(let i = 0; i < NUM_RETRIES; i++) {
        // when
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        if (groups[0][0][0] === 'Alpha' && groups[0][0][1] === 'Beta') {
          // success
          return
        }
      }
      throw new Error(`The participants Alpha and Beta were never together in a group within ${NUM_RETRIES} tries`)
    })

    it('separates participants in all group splits', async () => {
      // given
      render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const membershipGroupToggle = screen.getByLabelText('Abteilungs-durchmischte Gruppen generell bevorzugen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')
      const addSplitLink = screen.getByText('Weitere Gruppenaufteilung hinzufügen')
      const splitName = screen.getByLabelText('Bezeichnung')

      await userEvent.click(membershipGroupToggle)

      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '4')
      await userEvent.clear(splitName)
      await userEvent.type(splitName, 'Unternehmung')

      await userEvent.click(addSplitLink)
      const secondSplitNumberOfGroups = screen.getAllByLabelText('Anzahl Gruppen')[1]

      await userEvent.clear(secondSplitNumberOfGroups)
      await userEvent.type(secondSplitNumberOfGroups, '4')

      for(let i = 0; i < NUM_RETRIES; i++) {
        // when
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        if (groups[0][0][0] === 'Alpha (Kreta)' && groups[0][0][1] === 'Beta (Kreta)') {
          throw new Error(`The participants Alpha and Beta from the same membership group were generated into the same group`)
        }
        if (groups[1][0][0] === 'Alpha (Kreta)' && groups[1][0][1] === 'Beta (Kreta)') {
          throw new Error(`The participants Alpha and Beta from the same membership group were generated into the same group from the second split`)
        }
      }
    })

    it('only adds the membership group to the result display, if the toggle is set', async () => {
      // given
      render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
      const membershipGroupToggle = screen.getByLabelText('Abteilungs-durchmischte Gruppen generell bevorzugen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')

      // when
      await userEvent.click(membershipGroupToggle)
      await userEvent.click(generateButton)

      // then
      const groups = await waitForGeneratedGroups()
      expect(groups[0][0][0]).toBe('Alpha (Kreta)')
      expect(groups[0][0][0]).not.toBe('Alpha')

      // when
      await userEvent.click(membershipGroupToggle)
      await userEvent.click(generateButton)

      // then
      const groups2 = await waitForGeneratedGroups()
      expect(groups2[0][0][0]).not.toBe('Alpha (Kreta)')
      expect(groups2[0][0][0]).toBe('Alpha')
    })
  })

  describe('global participant separation selection', () => {
    it('separates selected participants in the generated groups', async () => {
      // given
      render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const separationMultiselect = screen.getByLabelText('Folgende TN-Kombinationen nach Möglichkeit trennen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')

      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '4')

      // when
      // select the first two participants in the multiselect menu
      await userEvent.click(separationMultiselect)
      const firstParticipant = within(separationMultiselect).getByText('Alpha (Kreta)')
      const secondParticipant = within(separationMultiselect).getByText('Beta (Kreta)')
      await userEvent.click(firstParticipant)
      await userEvent.click(secondParticipant)
      await userEvent.click(separationMultiselect)

      for(let i = 0; i < NUM_RETRIES; i++) {
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        if (groups[0][0][0] === 'Alpha (Kreta)' && groups[0][0][1] === 'Beta (Kreta)') {
          throw new Error(`The participants Alpha and Beta from the same membership group were generated into the same group`)
        }
      }
    })

    it('does not separate participants if not selected', async () => {
      // given
      render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')

      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '4')

      for(let i = 0; i < NUM_RETRIES; i++) {
        // when
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        if (groups[0][0][0] === 'Alpha' && groups[0][0][1] === 'Beta') {
          // success
          return
        }
      }
      throw new Error(`The participants Alpha and Beta were never together in a group within ${NUM_RETRIES} tries`)
    })

    it('separates participants in all group splits', async () => {
      // given
      render(ParticipantGroupGenerator, {i18n, props: {participants, participantGroups: []}})
      const numberOfGroups = screen.getByLabelText('Anzahl Gruppen')
      const separationMultiselect = screen.getByLabelText('Folgende TN-Kombinationen nach Möglichkeit trennen')
      const generateButton = screen.getByText('Gruppenvorschlag generieren')
      const addSplitLink = screen.getByText('Weitere Gruppenaufteilung hinzufügen')
      const splitName = screen.getByLabelText('Bezeichnung')

      await userEvent.clear(numberOfGroups)
      await userEvent.type(numberOfGroups, '4')
      await userEvent.clear(splitName)
      await userEvent.type(splitName, 'Unternehmung')

      await userEvent.click(addSplitLink)
      const secondSplitNumberOfGroups = screen.getAllByLabelText('Anzahl Gruppen')[1]

      await userEvent.clear(secondSplitNumberOfGroups)
      await userEvent.type(secondSplitNumberOfGroups, '4')

      // when
      // select the first two participants in the multiselect menu
      await userEvent.click(separationMultiselect)
      const firstParticipant = within(separationMultiselect).getByText('Alpha (Kreta)')
      const secondParticipant = within(separationMultiselect).getByText('Beta (Kreta)')
      await userEvent.click(firstParticipant)
      await userEvent.click(secondParticipant)
      await userEvent.click(separationMultiselect)

      for(let i = 0; i < NUM_RETRIES; i++) {
        // when
        await userEvent.click(generateButton)

        // then
        const groups = await waitForGeneratedGroups()
        if (groups[0][0][0] === 'Alpha' && groups[0][0][1] === 'Beta') {
          throw new Error(`The participants Alpha and Beta were generated into the same group, even though they were specified to be separated`)
        }
        if (groups[1][0][0] === 'Alpha' && groups[1][0][1] === 'Beta') {
          throw new Error(`The participants Alpha and Beta were generated into the same group, even though they were specified to be separated`)
        }
      }
    })
  })
})

function toggleCollapse(collapseLink) {
  // Clicking collapse triggers with the v-b-toggle directive doesn't work in tests for some reason
  //await userEvent.click(collapseLink)

  // Search upwards for a true vue component, not just a DOM element
  let vm = null
  let element = collapseLink
  while(vm === null && element.parentElement?.parentElement) {
    if (element.__vue__) {
      vm = element.__vue__
      break
    }
    element = element.parentElement
  }

  vm.$root.$emit('bv::toggle::collapse', collapseLink.__BV_toggle_TARGETS__[0])
}

async function waitForGeneratedGroups(groupName = 'Arbeitsgruppe') {
  await waitFor(() => {
    expect(screen.getByDisplayValue(`${groupName} 1`)).toBeVisible()
  })
  const rounds = [...document.querySelectorAll('.participant-group-generator .round-grid')]
  return rounds.map(round => {
    return [...round.querySelectorAll('.group-grid')].map(group => {
      return [...group.querySelectorAll('.text-center p')].map(el => el.innerHTML)
    })
  })
}
