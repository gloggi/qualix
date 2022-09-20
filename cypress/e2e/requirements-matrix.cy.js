import { useDatabaseResets } from "../support/databaseTransactions"

describe('requirements matrix', () => {
  useDatabaseResets()

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })
    cy.courseId().then((courseId) => {
      cy.create('App\\Models\\RequirementStatus', 1, { course_id: courseId, name: 'E2E status', color: 'green', icon: 'book' })

      cy.visit(`/course/${courseId}/feedbacks`)
      cy.contains('Anforderungs-Matrix')
      cy.get('.card-header > [role="button"]').then(($el) => {
        return $el.get(0).innerHTML
      }).as('feedbackName')
    })
  })

  it('can be displayed and edited', function () {
    cy.contains('Anforderungs-Matrix').click()
    cy.contains(`Anforderungs-Matrix ${this.feedbackName}`)
    cy.get('[data-label]').eq(1).as('cell')

    cy.get('@cell').click()
    cy.get('#requirement-status').click()
    cy.get('#requirement-status .multiselect__option').eq(3).click()
    cy.get('#comment').type('{selectall}Test E2E Notes which should be truncated because they are way too long to fit into the matrix cell')
    cy.contains('Speichern...')
    cy.contains('Automatisch gespeichert')

    cy.get('[aria-label="Close"]').click()
    cy.contains('Automatisch gespeichert').should('not.exist')
    cy.contains('Test E2E Notes which should be truncated because…')
    cy.get('@cell').should('have.class', 'bg-green')
  })
})
