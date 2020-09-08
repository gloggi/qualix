
export function useDatabaseResets() {
  beforeEach(() => cy.createDBSnapshot())
  afterEach(() => cy.restoreDBSnapshot())
  after(() => cy.cleanupDBSnapshots())
}
