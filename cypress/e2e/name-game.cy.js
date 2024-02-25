import { useDatabaseResets } from "../support/databaseTransactions"

describe('name game', () => {
  useDatabaseResets()

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })
    cy.courseId()
  })

  it('plays through the easy version of the name game', function () {
    cy.visit(`/course/${this.courseId}/participants`)

    cy.contains('Namen lernen').click()

    cy.contains('Name Game')

    cy.get('#participants').click()
    cy.get('#participants .multiselect__option').first().click()
    cy.get('#participants .multiselect__option').eq(2).click()
    cy.get('#participants .multiselect__option').eq(3).click()

    // Click outside the multiselect to close the dropdown menu
    cy.get('.card-body').click('right')

    cy.contains('Los geht\'s').click()

    cy.contains('Zeit:')

    for (let i = 0; i < 3; i++) {
      // 3 participants with 2 clicks each
      cy.get('.name-game button[type=submit]').first().click()
      cy.get('.name-game button[type=submit]').first().click()
    }

    cy.contains('Nochmals').click()

    cy.contains('Los geht\'s')
  })

  it('plays through the hard version of the name game', function () {
    cy.visit(`/course/${this.courseId}/participants`)

    cy.contains('Namen lernen').click()

    cy.contains('Name Game')

    cy.get('#participants').click()
    cy.get('#participants .multiselect__option').first().click()
    cy.get('#participants .multiselect__option').eq(2).click()
    cy.get('#participants .multiselect__option').eq(3).click()

    // Click outside the multiselect to close the dropdown menu
    cy.get('.card-body').click('right')

    cy.get('#gameMode').click()
    cy.contains('Schwierig (Namen eintippen)').click()

    cy.contains('Los geht\'s').click()

    cy.contains('Zeit:')

    for (let i = 0; i < 3; i++) {
      // 3 participants with 2 clicks each
      cy.get('.name-game button[type=submit]').first().click()
      cy.get('.name-game button[type=submit]').first().click()
    }

    cy.contains('Nochmals').click()

    cy.contains('Los geht\'s')
    cy.contains('Einfach (multiple choice)').should('not.be.visible')
  })
})
