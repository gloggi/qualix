import { render, screen, waitFor } from '@testing-library/vue'
import userEvent from '@testing-library/user-event'
import MultiSelect from "../../../resources/js/components/MultiSelect"

test('should open when autofocus is set', async () => {
  render(MultiSelect, {
    props: {
      options: [{ id: 1, label: 'test'}, { id: 2, label: 'test2' }],
      autofocus: true,
    },
    mocks: { '$t': () => {} }
  })

  await waitFor(() => {
    expect(screen.getByText('test')).toBeVisible()
    expect(screen.getByText('test2')).toBeVisible()
  })
})

test('should not open automatically when autofocus is not set', () => {
  render(MultiSelect, {
    props: {
      options: [{ id: 1, label: 'test'}, { id: 2, label: 'test2' }],
    },
    mocks: { '$t': () => {} }
  })

  // The select options should never become visible
  return expect(waitFor(() => {
    expect(screen.getByText('test')).toBeVisible()
    expect(screen.getByText('test2')).toBeVisible()
  })).rejects.toThrow(/Received element is not visible/)
})

test('should open when clicked', async () => {
  const multiSelect = render(MultiSelect, {
    props: {
      options: [{ id: 1, label: 'test'}, { id: 2, label: 'test2' }],
      placeholder: 'click here'
    },
    mocks: { '$t': () => {} }
  })

  userEvent.click(screen.getByPlaceholderText('click here'))

  await waitFor(() => {
    expect(screen.getByText('test')).toBeVisible()
    expect(screen.getByText('test2')).toBeVisible()
  })
})
