import { render } from '@testing-library/vue'
import TableObservationOverview from "../../../resources/js/components/TableObservationOverview"

it('should display the correct color classes', () => {
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

it('should not display the feedback column when feedbackData is null', () => {
  const table = render(TableObservationOverview, {
    props: {
      users: [{ id: 1, name: 'Bari' }, { id: 2, name: 'Lindo' }, { id: 3, name: 'Cosinus' }],
      participants: [{ id: 101, scout_name: 'Pflock', course_id: 7, observation_counts_by_user: { 1: 30, 2: 20, 3: 19 } }],
      feedbackData: null,
      multiple: true,
      redThreshold: 7,
      greenThreshold: 20,
    },
    mocks: { '$t': () => {}, '$te': () => {}, 'routeUri': () => {} },
    stubs: [ 'b-table-simple', 'b-thead', 'b-tbody', 'b-tr' ],
  })

  expect(table.queryAllByText('Zwischenquali')).toHaveLength(0)
})

it('should display the feedback column when feedbackData is passed', () => {
  const table = render(TableObservationOverview, {
    props: {
      users: [{ id: 1, name: 'Bari' }, { id: 2, name: 'Lindo' }, { id: 3, name: 'Cosinus' }],
      participants: [{ id: 101, scout_name: 'Pflock', course_id: 7, observation_counts_by_user: { 1: 30, 2: 20, 3: 19 } }],
      feedbackData: {
        name: 'Zwischenquali',
        feedbacks: [],
      },
      multiple: true,
      redThreshold: 7,
      greenThreshold: 20,
    },
    mocks: { '$t': () => {}, '$te': () => {}, 'routeUri': () => {} },
    stubs: [ 'b-table-simple', 'b-thead', 'b-tbody', 'b-tr' ],
  })

  expect(table.queryAllByText('Zwischenquali')).toHaveLength(1)
})

it('should generate the correct link to the feedback of a participant', () => {
  let passedRouteParams = null
  render(TableObservationOverview, {
    props: {
      users: [{ id: 1, name: 'Bari' }, { id: 2, name: 'Lindo' }, { id: 3, name: 'Cosinus' }],
      participants: [{ id: 101, scout_name: 'Pflock', course_id: 7, observation_counts_by_user: { 1: 30, 2: 20, 3: 19 } }],
      feedbackData: {
        name: 'Zwischenquali',
        course_id: 42,
        feedbacks: [{
          id: 123,
          participant_id: 101,
          requirements: [],
        }],
      },
      multiple: true,
      redThreshold: 7,
      greenThreshold: 20,
    },
    mocks: { '$t': () => {}, '$te': () => {}, '$tc': () => {}, 'routeUri': (...params) => {
        if (params[0] === 'feedbackContent.edit') passedRouteParams = params
      } },
    stubs: [ 'b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-progress', 'b-progress-bar' ],
  })

  expect(passedRouteParams).toEqual(['feedbackContent.edit', {course: 42, participant: 101, feedback: 123}])
})

it('should display the requirements progress bar of a participant', () => {
  let passedRouteParams = null
  const table = render(TableObservationOverview, {
    props: {
      users: [{ id: 1, name: 'Bari' }, { id: 2, name: 'Lindo' }, { id: 3, name: 'Cosinus' }],
      participants: [{ id: 101, scout_name: 'Pflock', course_id: 7, observation_counts_by_user: { 1: 30, 2: 20, 3: 19 } }],
      feedbackData: {
        name: 'Zwischenquali',
        course_id: 42,
        feedbacks: [{
          id: 123,
          participant_id: 101,
          requirements: [
            { pivot: { passed: 1 } },
            { pivot: { passed: 1 } },
            { pivot: { passed: 0 } },
            { pivot: { passed: null } },
          ],
        }],
      },
      multiple: true,
      redThreshold: 7,
      greenThreshold: 20,
    },
    mocks: { '$t': () => {}, '$te': () => {}, '$tc': (key, count) => `${key} ${count}`, 'routeUri': (...params) => {
        if (params[0] === 'feedbackContent.edit') passedRouteParams = params
      } },
    stubs: [ 'b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-progress', 'b-progress-bar' ],
  })

  expect(table.queryAllByText('t.views.participant_details.feedbacks.requirements_passed 2')).toHaveLength(1)
  expect(table.queryAllByText('t.views.participant_details.feedbacks.requirements_failed 1')).toHaveLength(1)
})
