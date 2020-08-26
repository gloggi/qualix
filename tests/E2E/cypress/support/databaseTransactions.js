
export function useDatabaseResets() {
  beforeEach(() => cy.request(`/_cypress/create_snapshot`))
  afterEach(() => cy.request(`/_cypress/restore_snapshot`))
}

export function cleanupDatabaseSavepoints() {
  before(() => cy.request(`/_cypress/cleanup_snapshots`))
  after(() => cy.request(`/_cypress/cleanup_snapshots`))
}
