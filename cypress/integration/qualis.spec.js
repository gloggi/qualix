import { useDatabaseResets } from "../support/databaseTransactions"

describe('quali editor', () => {
  useDatabaseResets()

  let courseId

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })
    cy.courseId().then(id => courseId = id)
  })

  it('creates a quali', () => {
    cy.visit(`/course/${courseId}/admin/qualis`)

    cy.get('[autofocus]').type('End-to-end test quali{enter}')

    cy.contains('Das Quali "End-to-end test quali" wurde erfolgreich erstellt.')
  })

  it('edits a quali', () => {
    cy.visit(`/course/${courseId}/participants`)
    cy.get('img.card-img-top').first().click()
    cy.get('td[data-label=Titel] a').first().click()
    cy.contains('Quali Details')

    cy.get('div.editor.form-control [contenteditable]').first().type("Text from end-to-end test\n")
    cy.contains('Speichern...')
    cy.contains('Automatisch gespeichert')
    cy.get('a.btn-link').click()

    cy.get('[href$="/print"]')
      .then(link => {
        cy.visit(link.prop('href'))
      });

    cy.contains('Text from end-to-end test')
  })
})
