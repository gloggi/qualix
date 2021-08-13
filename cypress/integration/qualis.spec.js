import { useDatabaseResets } from "../support/databaseTransactions"

describe('quali editor', () => {
  useDatabaseResets()

  let courseId
  let userId

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        userId = user.id
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
    cy.get('.btn-primary').click()

    cy.get('[href$="/print"]')
      .then(link => {
        cy.visit(link.prop('href'))
      });

    cy.contains('Text from end-to-end test')
  })

  it('restores edits to a quali after logging out and back in in another tab', () => {
    const random = Math.floor(Math.random() * 1000000)
    const text = 'This text should be restored ' + random
    cy.visit(`/course/${courseId}/participants`)
    cy.get('img.card-img-top').first().click()
    cy.get('td[data-label=Titel] a').first().click()
    cy.contains('Quali Details')

    cy.get('div.editor.form-control [contenteditable]').first().type(text).type("\n")

    cy.expireSession()
    cy.login({ id: userId })

    cy.get('.btn-primary').click()
    cy.contains('419')
    cy.contains('Seite ist abgelaufen')

    cy.go('back')
    cy.contains('Deine vormalig eingegebenen Änderungen wurden wiederhergestellt, sie sind aber noch nicht gespeichert.')
    cy.contains(text)
  })
})
