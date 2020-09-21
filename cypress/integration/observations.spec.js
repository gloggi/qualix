import { useDatabaseResets } from "../support/databaseTransactions"

describe('observation form', () => {
  useDatabaseResets()

  let courseId

  beforeEach(() => {
    cy.then(() => {
      cy.generate('App\\Models\\User').then(user => {
        cy.login({name: user.name, email: user.email}).then(user => {
          cy.artisan('e2e:scenario', { '--user-id': user.id })
        })

      })
    })
    cy.courseId().then(id => courseId = id)
  })

  it('creates an observation', () => {
    cy.visit(`/course/${courseId}/observation/new`)
    cy.contains('Beobachtung erfassen')

    cy.get('#participants').click()
    cy.get('#participants .multiselect__option').first().click()

    cy.get('#content').type('hat sich mehrmals gut eingebracht')

    cy.get('#block').click()
    cy.get('#block .multiselect__option').first().click()

    cy.get('#requirements').click()
    cy.get('#requirements .multiselect__option').first().click()
    cy.get('#requirements').click()
    cy.get('#requirements .multiselect__option').eq(2).click()

    cy.contains('Positiv').click()

    cy.contains('Speichern').click()

    cy.contains('Beobachtung erfasst.')
    cy.contains('Zurück zu').click()

    cy.contains('hat sich mehrmals gut eingebracht')
  })
})