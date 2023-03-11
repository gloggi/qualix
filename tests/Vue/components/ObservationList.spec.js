import {render, within} from '@testing-library/vue'
import ObservationList from '../../../resources/js/components/ObservationList'

const observations = [{
  block: {
    blockname_and_number: '1.3 my block'
  },
  categories: [{
    name: 'my category'
  }],
  content: 'war gut drauf',
  impression: 2,
  participants: [{ id: '1', scout_name: 'Pflock' }],
  requirements: [{
    content: 'some requirement'
  }],
  user: {
    name: 'Bari'
  }
}]

describe('visible columns', () => {
  it('should not display the content column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: false,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('Beobachtung')).toBeNull()
  })

  it('should display the content column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('Beobachtung')).toBeInTheDocument()
    expect(list.getByText('war gut drauf')).toBeInTheDocument()
  })

  it('should not display the block column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showBlock: false,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })
    const observationsTable = list.getByRole('table')

    expect(within(observationsTable).queryByText('Block')).toBeNull()
  })

  it('should display the block column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showBlock: true,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })
    const observationsTable = list.getByRole('table')

    expect(within(observationsTable).getByText('Block')).toBeInTheDocument()
    expect(within(observationsTable).getByText('1.3 my block')).toBeInTheDocument()
  })

  it('should not display the requirements column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showRequirements: false,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('Anforderungen')).toBeNull()
  })

  it('should display the requirements column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showRequirements: true,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('Anforderungen')).toBeInTheDocument()
    expect(list.getByText('some requirement')).toBeInTheDocument()
  })

  it('should not display the categories column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showCategories: false,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('Kategorien')).toBeNull()
  })

  it('should display the categories column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showCategories: true,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('Kategorien')).toBeInTheDocument()
    expect(list.getByText('my category')).toBeInTheDocument()
  })

  it('should not display the impression column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showImpression: false,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('Eindruck')).toBeNull()
  })

  it('should display the impression column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showImpression: true,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('Eindruck')).toBeInTheDocument()
    expect(list.getByText('Positiv')).toBeInTheDocument()
  })

  it('should not display the user column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showUser: false,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })
    const observationsTable = list.getByRole('table')

    expect(within(observationsTable).queryByText('Beobachtet von')).toBeNull()
  })

  it('should display the user column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showUser: true,
        observations,
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })
    const observationsTable = list.getByRole('table')

    expect(within(observationsTable).getByText('Beobachtet von')).toBeInTheDocument()
    expect(within(observationsTable).getByText('Bari')).toBeInTheDocument()
  })
})

describe('filters', () => {
  it('should display all filters when there are requirements and categories and used observations', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        showUser: true,
        observations,
        requirements: [{
          content: 'some requirement'
        }],
        categories: [{
          name: 'my category'
        }],
        usedObservations: [ 1 ],
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.getByText('Beobachtungen filtern')).toBeInTheDocument()
    expect(list.getByPlaceholderText('Anforderung')).toBeInTheDocument()
    expect(list.getByPlaceholderText('Kategorie')).toBeInTheDocument()
  })

  it('should not display the requirement filter when there are no requirements', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        showUser: true,
        observations,
        requirements: [],
        categories: [{
          name: 'my category'
        }],
        usedObservations: [ 1 ],
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.getByText('Beobachtungen filtern')).toBeInTheDocument()
    expect(list.queryByPlaceholderText('Anforderung')).toBeNull()
    expect(list.getByPlaceholderText('Kategorie')).toBeInTheDocument()
  })

  it('should not display the category filter when there are no categories', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        showUser: true,
        observations,
        requirements: [{
          content: 'some requirement'
        }],
        categories: [],
        usedObservations: [ 1 ],
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.getByText('Beobachtungen filtern')).toBeInTheDocument()
    expect(list.getByPlaceholderText('Anforderung')).toBeInTheDocument()
    expect(list.queryByPlaceholderText('Kategorie')).toBeNull()
  })

  it('should not display the used observation filter when no used observations are passed in', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        showUser: true,
        observations,
        requirements: [{
          content: 'some requirement'
        }],
        categories: [{
          name: 'my category'
        }],
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.getByText('Beobachtungen filtern')).toBeInTheDocument()
    expect(list.getByPlaceholderText('Anforderung')).toBeInTheDocument()
    expect(list.getByPlaceholderText('Kategorie')).toBeInTheDocument()
  })

  it('should display the filters, even when there are no requirements and no categories', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        showUser: true,
        observations,
        requirements: [],
        categories: [],
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.queryByText('Beobachtungen filtern')).toBeInTheDocument()
    expect(list.queryByPlaceholderText('Anforderung')).toBeNull()
    expect(list.queryByPlaceholderText('Kategorie')).toBeNull()
    expect(list.queryByText('Beobachtungen ausblenden, wenn sie in dieser Rückmeldung schon erwähnt wurden')).toBeNull()
  })

  it('should display the used observation filter when used observations are passed in', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        showUser: true,
        observations,
        requirements: [],
        categories: [],
        usedObservations: [],
      },
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.queryByText('Beobachtungen filtern')).toBeInTheDocument()
    expect(list.queryByText('Beobachtungen ausblenden, wenn sie in dieser Rückmeldung schon erwähnt wurden')).toBeInTheDocument()
  })
})
