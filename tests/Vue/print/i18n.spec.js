import createI18n from '../../../resources/js/components/print/i18n.js'

describe('print i18n', () => {
  it('translates a simple message', () => {
    const { translate } = createI18n('de')

    expect(translate('t.models.feedback.users')).toBe('Zuständig')
  })

  it('compiles messages with placeholders', () => {
    const { translate } = createI18n('de')

    expect(translate('t.header.welcome', { user: 'Bari' })).toBe('Willkommen, Bari')
  })

  it('translates into the requested language', () => {
    const { translate } = createI18n('fr')

    expect(translate('t.header.welcome', { user: 'Bari' })).toBe('Bienvenue, Bari')
  })
})
