import quotedPrintable from 'quoted-printable'

Cypress.Commands.add("lastSentMail", () => {
  return cy.request({url: 'http://mail:1080/messages', log: false})
    .its('body', { log: false })
    .should(list => expect(list, 'list of sent out emails').not.to.be.empty)
    .invoke({ log: false }, 'slice', -1)
    .its(0, { log: false })
    .then(lastMail => {

      return cy.request({url: `http://mail:1080/messages/${lastMail.id}.json`, log: false}).its('body', { log: false }).then(responseBody => {

        const recipients = responseBody.recipients.join(', ')

        Cypress.log({
          name: 'lastSentMail',
          message: `Found latest email with subject "${responseBody.subject}", sent out to "${recipients}"`,
          consoleProps: () => {
            return responseBody
          }
        })

        return quotedPrintable.decode(responseBody.source)
      })
  })
}, {prevSubject: false})
