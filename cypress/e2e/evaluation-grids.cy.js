import { useDatabaseResets } from "../support/databaseTransactions"

describe('evaluation grids', () => {
  useDatabaseResets()

  beforeEach(() => {
    cy.then(() => {
      cy.login().then(user => {
        cy.artisan('e2e:scenario', { '--user-id': user.id })
      })
    })
    cy.courseId()
  })

  it('deletes, creates and prints evaluation grid templates, and fills in an evaluation grid', function () {
    cy.visit(`/course/${this.courseId}/admin/evaluation_grids`)

    cy.get('.fa-circle-minus').last().click()
    cy.get('button[type=submit]').contains('Löschen').click()

    cy.get('.fa-circle-minus').last().click()
    cy.get('button[type=submit]').contains('Löschen').click()

    cy.get('[autofocus]').type('End-to-end test evaluation grid{enter}')

    cy.get('#blocks').click()
    cy.get('#blocks .multiselect__option').first().click()
    // Click outside the multiselect to close the dropdown menu
    cy.get('.card-body').first().click('right')

    cy.get('#requirements').click()
    cy.get('#requirements .multiselect__option').first().click()
    // Click outside the multiselect to close the dropdown menu
    cy.get('.card-body').first().click('right', { force: true })

    cy.get('#row-templates-0-criterion').type('End-to-end test criterion')
    cy.get('#row-templates-0-control-type').click()
    cy.get('#row-templates-0-control-type .multiselect__option').contains('Zwischentitel').click()
    // Click outside the multiselect to close the dropdown menu
    cy.get('.card-body').first().click('right')

    cy.contains('Zeile hinzufügen').click()
    cy.contains('Zeile hinzufügen').click()
    cy.contains('Zeile hinzufügen').click()

    cy.get('#row-templates-1-criterion').type('End-to-end test scale')
    cy.get('#row-templates-1-control-type').click()
    cy.get('#row-templates-1-control-type .multiselect__option').contains('Skala').click()
    // Click outside the multiselect to close the dropdown menu
    cy.get('.card-body').first().click('right', { force: true })

    cy.get('#row-templates-2-criterion').type('End-to-end test radio buttons')
    cy.get('#row-templates-2-control-type').click()
    cy.get('#row-templates-2-control-type .multiselect__option').contains('✨').click()
    // Click outside the multiselect to close the dropdown menu
    cy.get('.card-body').first().click('right', { force: true })

    cy.get('#row-templates-3-criterion').type('End-to-end test checkbox')
    cy.get('#row-templates-3-control-type').click()
    cy.get('#row-templates-3-control-type .multiselect__option').contains('Checkbox').click()
    // Click outside the multiselect to close the dropdown menu
    cy.get('.card-body').first().click('right', { force: true })

    cy.contains('Erstellen').click()

    cy.contains('Das Beurteilungsraster "End-to-end test evaluation grid" wurde erfolgreich erstellt.')

    cy.get('td').contains('End-to-end test evaluation grid').parent().within(() => {
      cy.get('[title="Drucken"]').click()
    })
    cy.contains('PDF wird generiert...')
    cy.contains('PDF wurde heruntergeladen')

    cy.task('findFiles', 'cypress/downloads/*').then((foundPdf) => {
      expect(foundPdf).to.be.a('string')
      cy.log(`found PDF ${foundPdf}`)
      cy.task('parsePdf', foundPdf).then((parsedPdf) => {
        expect(parsedPdf.text).to.include('End-to-end test evaluation grid')
        expect(parsedPdf.text).to.include('End-to-end test criterion')
        expect(parsedPdf.text).to.include('End-to-end test scale')
        expect(parsedPdf.text).to.include('End-to-end test radio buttons')
        expect(parsedPdf.text).to.include('End-to-end test checkbox')
        expect(parsedPdf.text).not.to.include('Some other text which certainly is not present in the pdf')
      })
    })

    cy.task('deleteFiles', 'cypress/downloads/*')

    cy.visit(`/course/${this.courseId}/participants`)
    cy.contains('Beobachtung erfassen').first().click()

    cy.get('#block').click()
    cy.get('#block .multiselect__option').first().click()

    cy.contains('End-to-end test evaluation grid').click()

    cy.get('textarea').first().type('foobar e2e test')

    cy.contains('Speichern').click()

    cy.contains('Beurteilungsraster erfasst.')
    cy.get('a i.fa-arrow-right').click()

    cy.get('td').contains('End-to-end test evaluation grid').parents('table').within(() => {
      cy.get('[title="Drucken"]').click()
    })
    cy.contains('PDF wird generiert...')
    cy.contains('PDF wurde heruntergeladen')

    cy.task('findFiles', 'cypress/downloads/*').then((foundPdf) => {
      expect(foundPdf).to.be.a('string')
      cy.log(`found PDF ${foundPdf}`)
      cy.task('parsePdf', foundPdf).then((parsedPdf) => {
        expect(parsedPdf.text).to.include('End-to-end test evaluation grid')
        expect(parsedPdf.text).to.include('End-to-end test criterion')
        expect(parsedPdf.text).to.include('End-to-end test scale')
        expect(parsedPdf.text).to.include('End-to-end test radio buttons')
        expect(parsedPdf.text).to.include('End-to-end test checkbox')
        expect(parsedPdf.text).to.include('foobar e2e test')
        expect(parsedPdf.text).not.to.include('Some other text which certainly is not present in the pdf')
      })
    })

    cy.task('deleteFiles', 'cypress/downloads/*')
  })
})
