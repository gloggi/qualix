import { render, screen, waitFor } from '@testing-library/vue'
import userEvent from '@testing-library/user-event'
import MultiSelect from "../../../resources/js/components/MultiSelect"

describe('autofocus', () => {
  it('should open when autofocus is set', async () => {
    render(MultiSelect, {
      props: {
        options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        autofocus: true,
      },
      mocks: {
        '$t': () => {
        }
      }
    })

    await waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })
  })

  it('should not open automatically when autofocus is not set', () => {
    render(MultiSelect, {
      props: {
        options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
      },
      mocks: {
        '$t': () => {
        }
      }
    })

    // The select options should never become visible
    return expect(waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })).rejects.toThrow(/Received element is not visible/)
  })
})

it('should open when clicked', async () => {
  const multiSelect = render(MultiSelect, {
    props: {
      options: [{ id: 1, label: 'Foo'}, { id: 2, label: 'Bar' }],
      'data-testid': 'multiselect-under-test',
    },
    mocks: { '$t': () => {} }
  })

  userEvent.click(screen.getByTestId('multiselect-under-test'))

  await waitFor(() => {
    expect(screen.getByText('Foo')).toBeVisible()
    expect(screen.getByText('Bar')).toBeVisible()
  })
})

it('should display no selected option initially', async () => {
  const multiSelect = render(MultiSelect, {
    props: {
      options: [{ id: 1, label: 'Foo'}, { id: 2, label: 'Bar' }],
    },
    mocks: { '$t': () => {} }
  })

  expect(multiSelect.container).not.toHaveVisibleTextContent()
})

it('should display the placeholder initially', async () => {
  const multiSelect = render(MultiSelect, {
    props: {
      options: [{ id: 1, label: 'Foo'}, { id: 2, label: 'Bar' }],
      placeholder: 'Select one',
    },
    mocks: { '$t': () => {} }
  })

  expect(multiSelect.container).toHaveVisibleTextContent('Select one')
})

it('should display the selected option', async () => {
  const multiSelect = render(MultiSelect, {
    props: {
      options: [{ id: 1, label: 'Foo'}, { id: 2, label: 'Bar' }],
      'data-testid': 'multiselect-under-test',
    },
    mocks: { '$t': () => {} }
  })

  userEvent.click(screen.getByTestId('multiselect-under-test'))

  await waitFor(() => {
    expect(screen.getByText('Foo')).toBeVisible()
    expect(screen.getByText('Bar')).toBeVisible()
  })

  userEvent.click(screen.getByText('Bar'))

  await waitFor(() => {
    expect(screen.getByText('Foo')).not.toBeVisible()
  })

  expect(multiSelect.container).toHaveVisibleTextContent('Bar')
})

describe('with name prop', () => {
  it('should display no selected option initially', async () => {
    const multiSelect = render(MultiSelect, {
      props: {
        options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        name: 'my-input',
      },
      mocks: {
        '$t': () => {
        }
      }
    })

    expect(multiSelect.container).not.toHaveVisibleTextContent()
    expect(screen.getByTestId('formValue')).toHaveValue('')
  })

  it('should display the selected option', async () => {
    const multiSelect = render(MultiSelect, {
      props: {
        options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        name: 'my-input',
        'data-testid': 'multiselect-under-test',
      },
      mocks: {
        '$t': () => {
        }
      }
    })

    userEvent.click(screen.getByTestId('multiselect-under-test'))

    await waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.click(screen.getByText('Bar'))

    await waitFor(() => {
      expect(screen.getByText('Foo')).not.toBeVisible()
    })

    expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    expect(screen.getByTestId('formValue')).toHaveValue('2')
  })
})
