import { render } from '@testing-library/vue'
import TableObservationOverview from "../../../resources/js/components/TableObservationOverview"

test('should display the correct color classes', () => {
  const table = render(TableObservationOverview, {
    props: {
      users: [{ id: 1, name: 'Bari' }, { id: 2, name: 'Lindo' }, { id: 3, name: 'Cosinus' }],
      participants: [{ id: 101, scout_name: 'Pflock', course_id: 7, observation_counts_by_user: {
          1: 20, 2: 7, 3: 2
      } }],
      redThreshold: 7,
      greenThreshold: 20,
    },
    mocks: { '$t': () => {} },
    stubs: [ 'b-table-simple', 'b-thead', 'b-tbody', 'b-tr' ],
  })

  expect(table.getByText('20').classList).not.toContain('bg-danger-light')
  expect(table.getByText('20').classList).toContain('bg-success-light')
  expect(table.getByText('7').classList).not.toContain('bg-danger-light')
  expect(table.getByText('7').classList).not.toContain('bg-success-light')
  expect(table.getByText('2').classList).toContain('bg-danger-light')
  expect(table.getByText('2').classList).not.toContain('bg-success-light')
})
