
export function useDatabaseResets() {
  beforeEach(() => cy.request(`/_cypress/create_snapshot`))
  afterEach(() => cy.request(`/_cypress/restore_snapshot`))
  after(() => cy.request(`/_cypress/cleanup_snapshots`))
}
