import { render } from '@testing-library/vue'
import ObservationList from "../../../resources/js/components/ObservationList"

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
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('t.models.observation.content')).toBeNull()
  })

  it('should display the content column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('t.models.observation.content')).toBeInTheDocument()
    expect(list.getByText('war gut drauf')).toBeInTheDocument()
  })

  it('should not display the block column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showBlock: false,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('t.models.observation.block')).toBeNull()
  })

  it('should display the block column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showBlock: true,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('t.models.observation.block')).toBeInTheDocument()
    expect(list.getByText('1.3 my block')).toBeInTheDocument()
  })

  it('should not display the requirements column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showRequirements: false,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('t.models.observation.requirements')).toBeNull()
  })

  it('should display the requirements column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showRequirements: true,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('t.models.observation.requirements')).toBeInTheDocument()
    expect(list.getByText('some requirement')).toBeInTheDocument()
  })

  it('should not display the categories column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showCategories: false,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('t.models.observation.categories')).toBeNull()
  })

  it('should display the categories column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showCategories: true,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('t.models.observation.categories')).toBeInTheDocument()
    expect(list.getByText('my category')).toBeInTheDocument()
  })

  it('should not display the impression column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showImpression: false,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('t.models.observation.impression')).toBeNull()
  })

  it('should display the impression column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showImpression: true,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('t.models.observation.impression')).toBeInTheDocument()
    expect(list.getByText('t.global.positive')).toBeInTheDocument()
  })

  it('should not display the user column when disabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showUser: false,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.queryByText('t.models.observation.user')).toBeNull()
  })

  it('should display the user column when enabled', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showUser: true,
        observations,
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr'],
    })

    expect(list.getByText('t.models.observation.user')).toBeInTheDocument()
    expect(list.getByText('Bari')).toBeInTheDocument()
  })
})

describe('filters', () => {
  it('should display both filters when there are requirements and categories', () => {
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
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.getByText('t.views.participant_details.filter')).toBeInTheDocument()
    expect(list.getByPlaceholderText('t.views.participant_details.filter_by_requirement')).toBeInTheDocument()
    expect(list.getByPlaceholderText('t.views.participant_details.filter_by_category')).toBeInTheDocument()
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
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.getByText('t.views.participant_details.filter')).toBeInTheDocument()
    expect(list.queryByPlaceholderText('t.views.participant_details.filter_by_requirement')).toBeNull()
    expect(list.getByPlaceholderText('t.views.participant_details.filter_by_category')).toBeInTheDocument()
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
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.getByText('t.views.participant_details.filter')).toBeInTheDocument()
    expect(list.getByPlaceholderText('t.views.participant_details.filter_by_requirement')).toBeInTheDocument()
    expect(list.queryByPlaceholderText('t.views.participant_details.filter_by_category')).toBeNull()
  })

  it('should not display the filters at all when there are no requirements and no categories', () => {
    const list = render(ObservationList, {
      props: {
        courseId: '1',
        showContent: true,
        showUser: true,
        observations,
        requirements: [],
        categories: [],
      },
      mocks: {'$t': (key) => key},
      stubs: ['b-table-simple', 'b-thead', 'b-tbody', 'b-tr', 'b-button', 'b-collapse', 'b-row', 'b-col', 'multi-select'],
      directives: {
        bToggle: () => {}
      }
    })

    expect(list.queryByText('t.views.participant_details.filter')).toBeNull()
    expect(list.queryByPlaceholderText('t.views.participant_details.filter_by_requirement')).toBeNull()
    expect(list.queryByPlaceholderText('t.views.participant_details.filter_by_category')).toBeNull()
  })
})
