import { useDatabaseResets } from "../support/databaseTransactions"

describe('invitation flow', () => {

  let courseId

  useDatabaseResets()

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })

    cy.courseId().then(id => {
      courseId = id

      cy.visit(`/course/${courseId}/admin/equipe`)
    })
  })

  it('displays the page title', () => {
    cy.get('.card-header').contains('Equipenmitglied einladen')
  })

  it('requires email', () => {
    cy.get('#email')
      .should('have.attr', 'required')
  })

  it('validates email', () => {
    cy.get('#email')
      .type('something-that-is-not-an-email{enter}')

    cy.contains('E-Mail muss eine gültige E-Mail-Adresse sein.')
  })

  it('successfully invites another equipe member', () => {
    cy.get('#email')
      .type('some-email@example.org')
    cy.contains('Einladen')
      .click()

    cy.contains('Wir haben eine Einladung an some-email@example.org gesendet.')

    cy.logout()

    cy.login({ email: 'some-email@example.org' })

    cy.lastSentMail()
      .then(mail => {
        const verifyLink = mail.match(/http:\/\/qualix\/invitation\/[a-zA-Z0-9]+/)[0]

        cy.visit(verifyLink)

        cy.get('.card-header').contains('Einladung in ')
        cy.contains('Gehört dir die Mailadresse some-email@example.org?')

        cy.contains('Nein, diese Einladung ist nicht für mich')
          .should('have.attr', 'href').and('match', /^http:\/\/qualix$/)

        cy.contains('Ja, Einladung annehmen')
          .click()

        cy.contains('Einladung angenommen. Du bist jetzt in der Kursequipe von ')
        cy.contains('Willkommä bim Qualix')
        cy.contains('Qualix soll gegen den Papier-Krieg helfen')
      })
  })

})
