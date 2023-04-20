import { useDatabaseResets } from "../support/databaseTransactions"

describe('requirements matrix', () => {
  useDatabaseResets()

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })
  })

  it('can be displayed and edited', function () {
    cy.courseId().then((courseId) => {
      cy.create('App\\Models\\RequirementStatus', 1, { course_id: courseId, name: 'E2E status', color: 'green', icon: 'book' })

      cy.php(`App\\Models\\FeedbackData::orderBy('id', 'desc')->first();`).then(feedback => {
        cy.visit(`/course/${courseId}`)
        cy.contains(feedback.name).click()

        cy.contains('Anforderungs-Matrix')

        cy.contains(`Anforderungs-Matrix ${feedback.name}`)
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
  })

  it('can be displayed and edited if there are multiple feedbacks in course', function () {
    cy.courseId().then((courseId) => {
      cy.create('App\\Models\\RequirementStatus', 1, { course_id: courseId, name: 'E2E status', color: 'green', icon: 'book' })
      cy.create('App\\Models\\FeedbackData', 1, { course_id: courseId, name: 'E2E Feedback' })

      cy.php(`App\\Models\\FeedbackData::orderBy('id', 'desc')->offset(1)->first();`).then(feedback => {
        cy.visit(`/course/${courseId}`)
        cy.contains('Rückmeldungen').click()
        cy.contains('E2E Feedback')
        cy.contains(feedback.name).click()

        cy.contains('Anforderungs-Matrix')

        cy.contains(`Anforderungs-Matrix ${feedback.name}`)
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
  })
})
