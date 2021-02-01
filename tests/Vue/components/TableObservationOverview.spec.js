import { render } from '@testing-library/vue'
import TableObservationOverview from "../../../resources/js/components/TableObservationOverview"

test('should display the correct color classes', () => {
  const table = render(TableObservationOverview, {
    props: {
      users: [{ id: 1, name: 'Bari' }, { id: 2, name: 'Lindo' }, { id: 3, name: 'Cosinus' },
              { id: 4, name: 'Bari2' }, { id: 5, name: 'Lindo2' }, { id: 6, name: 'Cosinus2' }],
      participants: [{ id: 101, scout_name: 'Pflock', course_id: 7, observation_counts_by_user: {
          1: 30, 2: 20, 3: 19, 4: 7, 5: 6, 6: 2
      } }],
      redThreshold: 7,
      greenThreshold: 20,
    },
    mocks: { '$t': () => {} },
    stubs: [ 'b-table-simple', 'b-thead', 'b-tbody', 'b-tr' ],
  })

  expect(table.getByText('30')).not.toHaveClass('bg-danger-light')
  expect(table.getByText('30')).toHaveClass('bg-success-light')
  expect(table.getByText('20')).not.toHaveClass('bg-danger-light')
  expect(table.getByText('20')).toHaveClass('bg-success-light')
  expect(table.getByText('19')).not.toHaveClass('bg-danger-light')
  expect(table.getByText('19')).not.toHaveClass('bg-success-light')
  expect(table.getByText('7')).not.toHaveClass('bg-danger-light')
  expect(table.getByText('7')).not.toHaveClass('bg-success-light')
  expect(table.getByText('6')).toHaveClass('bg-danger-light')
  expect(table.getByText('6')).not.toHaveClass('bg-success-light')
  expect(table.getByText('2')).toHaveClass('bg-danger-light')
  expect(table.getByText('2')).not.toHaveClass('bg-success-light')
})
