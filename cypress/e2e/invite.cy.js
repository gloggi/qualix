import { useDatabaseResets } from "../support/databaseTransactions"

describe('invitation flow', () => {

  useDatabaseResets()

  beforeEach(function () {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })

    cy.courseId().then(function (courseId) {
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
      .type('some-email@gmail.com')
    cy.contains('Einladen')
      .click()

    cy.contains('Wir haben eine Einladung an some-email@gmail.com gesendet.')

    cy.logout()

    cy.login({ email: 'some-email@gmail.com' })

    cy.lastSentMail()
      .then(mail => {
        const verifyLink = mail.match(/http:\/\/qualix\/invitation\/[a-zA-Z0-9]+/)[0]

        cy.visit(verifyLink)

        cy.get('.card-header').contains('Einladung in ')
        cy.contains('Gehört dir die Mailadresse some-email@gmail.com?')

        cy.contains('Nein, diese Einladung ist nicht für mich')
          .should('have.attr', 'href').and('match', /^http:\/\/qualix$/)

        cy.contains('Ja, Einladung annehmen')
          .click()

        cy.contains('Einladung angenommen. Du bist jetzt in der Kursequipe von ')
        cy.contains('Willkommä bim Qualix')
        cy.contains('Qualix soll gegen den Papier-Krieg helfen')
      })
  })

  it('deletes an invitation', () => {
    cy.get('#email')
      .type('some-email@gmail.com')
    cy.contains('Einladen')
      .click()

    cy.contains('Wir haben eine Einladung an some-email@gmail.com gesendet.')

    cy.contains('Einladungen').parent('.card').find('[title="Löschen"]').click()

    cy.contains('Willst du die Einladung für some-email@gmail.com wirklich entfernen?')
      .parent()
      .contains('Löschen')
      .click()

    cy.contains('Die Einladung für some-email@gmail.com wurde erfolgreich gelöscht.')

    cy.contains('Momentan sind keine Einladungen offen.')
  })

})
