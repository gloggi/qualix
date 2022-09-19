import { useDatabaseResets } from "../support/databaseTransactions"

describe('feedback editor', () => {
  useDatabaseResets()

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })
    cy.courseId()
  })

  it('creates a feedback', function () {
    cy.visit(`/course/${this.courseId}/admin/feedbacks`)

    cy.get('[autofocus]').type('End-to-end test feedback{enter}')

    cy.contains('Die Rückmeldung "End-to-end test feedback" wurde erfolgreich erstellt.')
  })

  it('edits a feedback', function () {
    cy.visit(`/course/${this.courseId}/participants`)
    cy.get('img.card-img-top').first().click()
    cy.get('td[data-label=Titel] a').first().click()
    cy.contains('Rückmeldung Details')

    cy.get('div.editor.form-control [contenteditable]').first().type("Text from end-to-end test\n")
    cy.contains('Speichern...')
    cy.contains('Automatisch gespeichert')
    cy.get('a.btn-link').first().click()

    cy.get('[href$="/print"]')
      .then(link => {
        cy.visit(link.prop('href'))
      });

    cy.contains('Text from end-to-end test')
  })
})
