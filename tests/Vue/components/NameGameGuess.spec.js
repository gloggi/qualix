import { describe, it, expect } from 'vitest'
import { render, screen } from '@testing-library/vue'
import userEvent from '@testing-library/user-event'
import NameGameGuess from '../../../resources/js/components/nameGame/NameGameGuess'
import GuessPrompt from '../../../resources/js/components/nameGame/GuessPrompt'
import AnswerInput from '../../../resources/js/components/nameGame/AnswerInput'
import RowText from '../../../resources/js/components/form/RowText'
import InputText from '../../../resources/js/components/form/InputText'

const renderGame = (props) => render(NameGameGuess, {
  props,
  global: { components: { GuessPrompt, AnswerInput, RowText, InputText } },
})

describe('multiple choice mode', () => {
  it('awards the point when clicking a different participant who shares the correct name (#382)', async () => {
    const target = { id: 1, scout_name: 'Anna Müller' }
    const duplicate = { id: 2, scout_name: 'Anna Müller' }
    const wrongOption = { id: 3, scout_name: 'Beat Meier' }

    const { container } = renderGame({
      participant: target,
      participants: [target, duplicate, wrongOption],
      gameMode: 'multipleChoice',
    })

    const sameNameButtons = screen.getAllByRole('button', { name: 'Anna Müller' })
    expect(sameNameButtons).toHaveLength(2)
    const duplicateButton = sameNameButtons.find(button => button.getAttribute('value') === `${duplicate.id}`)

    await userEvent.click(duplicateButton)

    expect(container.querySelector('.fa-check')).toBeInTheDocument()
    expect(container.querySelector('.fa-xmark')).not.toBeInTheDocument()
  })

  it('does not award the point when clicking a wrongly named option', async () => {
    const target = { id: 1, scout_name: 'Anna Müller' }
    const wrongOption = { id: 3, scout_name: 'Beat Meier' }

    const { container } = renderGame({
      participant: target,
      participants: [target, { id: 2, scout_name: 'Cara Notz' }, wrongOption],
      gameMode: 'multipleChoice',
    })

    await userEvent.click(screen.getByRole('button', { name: 'Beat Meier' }))

    expect(container.querySelector('.fa-xmark')).toBeInTheDocument()
    expect(container.querySelector('.fa-check')).not.toBeInTheDocument()
  })
})

describe('free text mode', () => {
  it.each([
    ['Müller', 'Muller'],
    ['José', 'jose'],
  ])('awards the point when typing "%s" as "%s" without diacritics', async (actualName, typedName) => {
    const target = { id: 1, scout_name: actualName }

    const { container } = renderGame({
      participant: target,
      participants: [target],
      gameMode: 'manualNameInput',
    })

    await userEvent.type(screen.getByLabelText('Name'), typedName)
    await userEvent.click(screen.getByRole('button', { name: 'Abschicken' }))

    expect(container.querySelector('.fa-check')).toBeInTheDocument()
    expect(container.querySelector('.fa-xmark')).not.toBeInTheDocument()
  })

  it('does not award the point for an unrelated name', async () => {
    const target = { id: 1, scout_name: 'Müller' }

    const { container } = renderGame({
      participant: target,
      participants: [target],
      gameMode: 'manualNameInput',
    })

    await userEvent.type(screen.getByLabelText('Name'), 'Schmidt')
    await userEvent.click(screen.getByRole('button', { name: 'Abschicken' }))

    expect(container.querySelector('.fa-xmark')).toBeInTheDocument()
    expect(container.querySelector('.fa-check')).not.toBeInTheDocument()
  })
})
