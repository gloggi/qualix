import { useDatabaseResets } from "../support/databaseTransactions"

describe('participant group generator', () => {
  useDatabaseResets()

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })
    cy.courseId()
  })

  it('generates and saves some participant groups', function () {
    cy.visit(`/course/${this.courseId}/admin/participantGroups`)

    cy.contains('TN-Gruppen-Generator').click()

    cy.contains('Gruppenvorschlag generieren').click()

    cy.get('#participant-groups-0-0-group-name').should('have.value', 'Arbeitsgruppe 1')
    cy.get('#participant-groups-0-0-group-name').clear().type('E2E test group')
    cy.get('#participant-groups-0-2-group-name').should('have.value', 'Arbeitsgruppe 3')

    cy.contains('Speichern').click()

    cy.contains('TN-Gruppen wurden erfolgreich erstellt')
    cy.contains('E2E test group')
  })
})
