import { render, screen, waitFor } from '@testing-library/vue'
import MultiSelect from "../../../resources/js/components/MultiSelect"

test('should activate when autofocus is set', async () => {
  render(MultiSelect, {
    propsData: {
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

test('should not activate automatically when autofocus is not set', () => {
  render(MultiSelect, {
    propsData: {
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
