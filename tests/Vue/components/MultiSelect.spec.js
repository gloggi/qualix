import { render, screen, waitFor } from '@testing-library/vue'
import userEvent from '@testing-library/user-event'
import MultiSelect from "../../../resources/js/components/MultiSelect"

describe('single select', () => {
  describe('autofocus', () => {
    it('should open when autofocus is set', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          autofocus: true,
        },
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
      })

      // The select options should never become visible
      return expect(waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })).rejects.toThrow(/Received element is not visible/)
    })
  })

  describe('opening and closing', () => {
    it('should open when clicked', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })
    })

    it('should close when the arrow is clicked', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })

      userEvent.click(screen.getByText('', {selector: '.multiselect__select'}))

      await waitFor(() => {
        expect(screen.getByText('Foo')).not.toBeVisible()
        expect(screen.getByText('Bar')).not.toBeVisible()
      })
    })

    it('should close when an option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Bar')).toBeVisible()
      })

      userEvent.click(screen.getByText('Foo'))

      await waitFor(() => {
        expect(screen.getByText('Foo', {selector: '.multiselect__single'})).toBeInTheDocument()
      })

      await waitFor(() => {
        expect(screen.getByText('Bar')).not.toBeVisible()
      })
    })

    it('should close when an option is selected using the keyboard', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          placeholder: 'type here'
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Bar')).toBeVisible()
      })

      await userEvent.type(screen.getByPlaceholderText('type here'), '{arrowdown}{enter}', { delay: 10 })

      await waitFor(() => {
        expect(screen.getByText('Bar', {selector: '.multiselect__single'})).toBeInTheDocument()
      })

      await waitFor(() => {
        expect(screen.getByText('Foo')).not.toBeVisible()
      })
    })
  })

  it('should close when losing focus via tab', async () => {
    render(MultiSelect, {
      props: {
        options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
      },
    })

    userEvent.click(screen.getByRole('combobox'))

    await waitFor(() => {
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.tab()

    await waitFor(() => {
      expect(screen.getByText('Bar')).not.toBeVisible()
    })
  })

  describe('main display and form value', () => {
    it('should display no selected option initially', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      expect(multiSelect.container).not.toHaveVisibleTextContent()
    })

    it('should display the placeholder initially', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          placeholder: 'Select one',
        },
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Select one')
    })

    it('should display the initial value', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          value: '2'
        },
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    it('should prefer to display the initial value over the placeholder', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          placeholder: 'Select one',
          value: '2'
        },
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    it('should display the selected option', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

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
        })

        expect(multiSelect.container).not.toHaveVisibleTextContent()
        expect(screen.getByTestId('formValue')).toHaveValue('')
      })

      it('should display the initial value', async () => {
        const multiSelect = render(MultiSelect, {
          props: {
            options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
            name: 'my-input',
            value: '2'
          },
        })

        expect(multiSelect.container).toHaveVisibleTextContent('Bar')
        expect(screen.getByTestId('formValue')).toHaveValue('2')
      })

      it('should display the selected option', async () => {
        const multiSelect = render(MultiSelect, {
          props: {
            options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
            name: 'my-input',
          },
        })

        userEvent.click(screen.getByRole('combobox'))

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
  })

  describe('clear button', () => {
    it('should not display the clear button when show-clear is unset and no option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is false and no option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': false,
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is true and no option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is unset and an option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          value: '2',
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is false and an option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': false,
          value: '2',
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should display the clear button when show-clear is true and an option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
          value: '2',
        },
      })

      expect(document.querySelector('.multiselect__clear')).toBeInTheDocument()
    })

    it('should remove the selected entry when clicking the clear button', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
          value: '2',
          name: 'my-input',
        },
      })

      userEvent.click(document.querySelector('.multiselect__clear'))

      await waitFor(() => {
        expect(multiSelect.container).not.toHaveVisibleTextContent()
        expect(screen.getByTestId('formValue')).toHaveValue('')
      })
    })
  })

  it('should display the no-options text', async () => {
    const multiSelect = render(MultiSelect, {
      props: {
        options: [],
        'no-options': 'Test text',
      },
    })

    expect(multiSelect.container).toHaveTextContent('Test text')
  })

  it('should be able to submit a form on input', async () => {
    const formSubmitted = jest.fn()
    render({
        template: `
          <div>
            <form id="myform" @submit.prevent="formSubmitted"></form>
            <multi-select :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                          submit-on-input="myform"></multi-select>
          </div>`,
        components: {MultiSelect},
        methods: { formSubmitted }
      },
    )

    userEvent.click(screen.getByRole('combobox'))

    await waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.click(screen.getByText('Bar'))

    await waitFor(() => {
      expect(formSubmitted).toHaveBeenCalled()
    })

  })

  it('should emit an input event when mounted', async () => {
    const onInput = jest.fn()
    const multiSelect = render({
        template: `
          <multi-select :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                        @input="onInput" value="2"></multi-select>`,
        components: { MultiSelect },
        methods: { onInput }
      },
    )

    await waitFor(() => {
      expect(onInput).toHaveBeenCalledWith("2", undefined)
    })

  })

  it('should emit an input event when an option is selected', async () => {
    const onInput = jest.fn()
    const multiSelect = render({
        template: `
          <multi-select :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                        @input="onInput" value="2"></multi-select>`,
        components: { MultiSelect },
        methods: { onInput }
      },
    )

    await waitFor(() => {
      expect(onInput).toHaveBeenCalled()
    })

    onInput.mockClear()

    userEvent.click(screen.getByRole('combobox'))

    await waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.click(screen.getByText('Foo'))

    expect(onInput).toHaveBeenCalledWith("1", null)
  })

  it('should not emit an update:selected event when mounted', async () => {
    const onUpdateSelected = jest.fn()
    const multiSelect = render({
        template: `
          <multi-select :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                        @update:selected="onUpdateSelected" value="2"></multi-select>`,
        components: { MultiSelect },
        methods: { onUpdateSelected }
      },
    )

    // onUpdateSelected should never be called
    return expect(waitFor(() => {
      expect(onUpdateSelected).toHaveBeenCalled()
    })).rejects.toThrow(/toHaveBeenCalled/)
  })

  it('should emit an update:selected event when an option is selected', async () => {
    const onUpdateSelected = jest.fn()
    const multiSelect = render({
        template: `
          <multi-select :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                        @update:selected="onUpdateSelected" value="2"></multi-select>`,
        components: { MultiSelect },
        methods: { onUpdateSelected }
      },
    )

    userEvent.click(screen.getByRole('combobox'))

    await waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.click(screen.getByText('Foo'))

    expect(onUpdateSelected).toHaveBeenCalledWith({"id": 1, "label": "Foo"}, null)
  })
})

describe('multiple select', () => {
  describe('autofocus', () => {
    it('should open when autofocus is set', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          autofocus: true,
        },
      })

      await waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })
    })

    it('should not open automatically when autofocus is not set', () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      // The select options should never become visible
      return expect(waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })).rejects.toThrow(/Received element is not visible/)
    })
  })

  describe('opening and closing', () => {
    it('should open when clicked', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })
    })

    it('should close when the arrow is clicked', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })

      userEvent.click(screen.getByText('', {selector: '.multiselect__select'}))

      await waitFor(() => {
        expect(screen.getByText('Foo')).not.toBeVisible()
        expect(screen.getByText('Bar')).not.toBeVisible()
      })
    })

    it('should not close when an option is selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Bar')).toBeVisible()
      })

      userEvent.click(screen.getByText('Foo'))

      await waitFor(() => {
        expect(screen.getByText('Foo', {selector: '.multiselect__tag span'})).toBeInTheDocument()
      })

      // The select options should never disappear
      return expect(waitFor(() => {
        expect(screen.getByText('Bar')).not.toBeVisible()
      })).rejects.toThrow(/Received element is visible/)
    })

    it('should not close when an option is selected using the keyboard', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          placeholder: 'type here',
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Bar')).toBeVisible()
      })

      await userEvent.type(screen.getByPlaceholderText('type here'), '{arrowdown}{enter}', { delay: 10 })

      await waitFor(() => {
        expect(screen.getByText('Bar', {selector: '.multiselect__tag span'})).toBeInTheDocument()
      })

      // The select options should never disappear
      return expect(waitFor(() => {
        expect(screen.getByText('Foo')).not.toBeVisible()
      })).rejects.toThrow(/Received element is visible/)
    })
  })

  it('should close when losing focus via tab', async () => {
    render(MultiSelect, {
      props: {
        multiple: true,
        options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
      },
    })

    userEvent.click(screen.getByRole('combobox'))

    await waitFor(() => {
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.tab()

    await waitFor(() => {
      expect(screen.getByText('Foo')).not.toBeVisible()
      expect(screen.getByText('Bar')).not.toBeVisible()
    })
  })

  describe('main display and form value', () => {
    it('should display no selected option initially', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      expect(multiSelect.container).not.toHaveVisibleTextContent()
    })

    it('should display the placeholder initially', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          placeholder: 'Select one',
        },
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Select one')
    })

    it('should display the initial value', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          value: '2'
        },
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    it('should prefer to display the initial value over the placeholder', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          placeholder: 'Select one',
          value: '2'
        },
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    it('should display the selected option', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })

      userEvent.click(screen.getByText('Bar'))

      await waitFor(() => {
        expect(screen.getByText('Bar', { selector: '.multiselect__tag span' })).toBeInTheDocument()
      })

      expect(screen.getByRole('combobox')).toHaveVisibleTextContent('Bar Foo Bar')

      userEvent.click(screen.getByText('', { selector: '.multiselect__select' }))

      await waitFor(() => {
        expect(multiSelect.container).not.toHaveVisibleTextContent('Foo')
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    it('should display the selected options', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}, {id: 3, label: 'Baz'}],
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
        expect(screen.getByText('Baz')).toBeVisible()
      })

      userEvent.click(screen.getByText('Bar'))
      await waitFor(() => {
        expect(screen.getByText('Bar', { selector: '.multiselect__tag span' })).toBeInTheDocument()
      })

      userEvent.click(screen.getByText('Foo'))
      await waitFor(() => {
        expect(screen.getByText('Foo', { selector: '.multiselect__tag span' })).toBeInTheDocument()
      })

      expect(screen.getByRole('combobox')).toHaveVisibleTextContent('Bar Foo Foo Bar Baz')

      userEvent.click(screen.getByText('', { selector: '.multiselect__select' }))

      await waitFor(() => {
        expect(multiSelect.container).not.toHaveVisibleTextContent('Baz')
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Foo')
      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    describe('with name prop', () => {
      it('should display no selected option initially', async () => {
        const multiSelect = render(MultiSelect, {
          props: {
            multiple: true,
            options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
            name: 'my-input',
          },
        })

        expect(multiSelect.container).not.toHaveVisibleTextContent()
        expect(screen.getByTestId('formValue')).toHaveValue('')
      })

      it('should display the initial value', async () => {
        const multiSelect = render(MultiSelect, {
          props: {
            multiple: true,
            options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
            name: 'my-input',
            value: '2'
          },
        })

        expect(multiSelect.container).toHaveVisibleTextContent('Bar')
        expect(screen.getByTestId('formValue')).toHaveValue('2')
      })

      it('should display the selected option', async () => {
        const multiSelect = render(MultiSelect, {
          props: {
            multiple: true,
            options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
            name: 'my-input',
          },
        })

        userEvent.click(screen.getByRole('combobox'))

        await waitFor(() => {
          expect(screen.getByText('Foo')).toBeVisible()
          expect(screen.getByText('Bar')).toBeVisible()
        })

        userEvent.click(screen.getByText('Bar'))

        await waitFor(() => {
          expect(screen.getByText('Bar', {selector: '.multiselect__tag span'})).toBeInTheDocument()
        })

        expect(screen.getByRole('combobox')).toHaveVisibleTextContent('Bar Foo Bar')

        userEvent.click(screen.getByText('', {selector: '.multiselect__select'}))

        expect(screen.getByTestId('formValue')).toHaveValue('2')

        await waitFor(() => {
          expect(multiSelect.container).not.toHaveVisibleTextContent('Foo')
        })

        expect(multiSelect.container).toHaveVisibleTextContent('Bar')
        expect(screen.getByTestId('formValue')).toHaveValue('2')
      })

      it('should display the selected options', async () => {
        const multiSelect = render(MultiSelect, {
          props: {
            multiple: true,
            options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}, {id: 3, label: 'Baz'}],
            name: 'my-input',
          },
        })

        userEvent.click(screen.getByRole('combobox'))

        await waitFor(() => {
          expect(screen.getByText('Foo')).toBeVisible()
          expect(screen.getByText('Bar')).toBeVisible()
          expect(screen.getByText('Baz')).toBeVisible()
        })

        userEvent.click(screen.getByText('Bar'))
        await waitFor(() => {
          expect(screen.getByText('Bar', {selector: '.multiselect__tag span'})).toBeInTheDocument()
        })

        userEvent.click(screen.getByText('Foo'))
        await waitFor(() => {
          expect(screen.getByText('Foo', {selector: '.multiselect__tag span'})).toBeInTheDocument()
        })

        expect(screen.getByRole('combobox')).toHaveVisibleTextContent('Bar Foo Foo Bar Baz')
        expect(screen.getByTestId('formValue')).toHaveValue('2,1')

        userEvent.click(screen.getByText('', {selector: '.multiselect__select'}))

        await waitFor(() => {
          expect(multiSelect.container).not.toHaveVisibleTextContent('Baz')
        })

        expect(multiSelect.container).toHaveVisibleTextContent('Foo')
        expect(multiSelect.container).toHaveVisibleTextContent('Bar')
        expect(screen.getByTestId('formValue')).toHaveValue('2,1')
      })
    })
  })

  describe('clear button', () => {
    it('should not display the clear button when show-clear is unset and no option is selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is false and no option is selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': false,
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is true and no option is selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is unset and an option is selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          value: '2',
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is unset and multiple options are selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          value: '1,2',
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is false and an option is selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': false,
          value: '2',
        },
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should display the clear button when show-clear is true and an option is selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
          value: '2',
        },
      })

      expect(document.querySelector('.multiselect__clear')).toBeInTheDocument()
    })

    it('should display the clear button when show-clear is true and multiple options are selected', async () => {
      render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
          value: '1,2',
        },
      })

      expect(document.querySelector('.multiselect__clear')).toBeInTheDocument()
    })

    it('should remove the selected entry when clicking the clear button', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
          value: '2',
          name: 'my-input',
        },
      })

      userEvent.click(document.querySelector('.multiselect__clear'))

      await waitFor(() => {
        expect(multiSelect.container).not.toHaveVisibleTextContent()
        expect(screen.getByTestId('formValue')).toHaveValue('')
      })
    })

    it('should remove the selected entries when clicking the clear button', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
          value: '2,1',
          name: 'my-input',
        },
      })

      userEvent.click(document.querySelector('.multiselect__clear'))

      await waitFor(() => {
        expect(multiSelect.container).not.toHaveVisibleTextContent()
        expect(screen.getByTestId('formValue')).toHaveValue('')
      })
    })
  })

  it('should display the no-options text', async () => {
    const multiSelect = render(MultiSelect, {
      props: {
        multiple: true,
        options: [],
        'no-options': 'Test text',
      },
    })

    expect(multiSelect.container).toHaveTextContent('Test text')
  })

  it('should be able to submit a form on input', async () => {
    const formSubmitted = jest.fn()
    render({
        template: `
          <div>
            <form id="myform" @submit.prevent="formSubmitted"></form>
            <multi-select multiple :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                          submit-on-input="myform"></multi-select>
          </div>`,
        components: {MultiSelect},
        methods: { formSubmitted }
      },
    )

    userEvent.click(screen.getByRole('combobox'))

    await waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.click(screen.getByText('Bar'))

    await waitFor(() => {
      expect(formSubmitted).toHaveBeenCalled()
    })

  })

  it('should emit an input event when mounted', async () => {
    const onInput = jest.fn()
    const multiSelect = render({
        template: `
          <multi-select multiple :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                        @input="onInput" value="2"></multi-select>`,
        components: { MultiSelect },
        methods: { onInput }
      },
    )

    await waitFor(() => {
      expect(onInput).toHaveBeenCalledWith("2", undefined)
    })

  })

  it('should emit an input event when an option is selected', async () => {
    const onInput = jest.fn()
    const multiSelect = render({
        template: `
          <multi-select multiple :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                        @input="onInput"></multi-select>`,
        components: { MultiSelect },
        methods: { onInput }
      },
    )

    await waitFor(() => {
      expect(onInput).toHaveBeenCalled()
    })

    onInput.mockClear()

    userEvent.click(screen.getByRole('combobox'))

    await waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.click(screen.getByText('Foo'))

    await waitFor(() => {
      expect(onInput).toHaveBeenCalledWith("1", null)
    })
  })

  it('should not emit an update:selected event when mounted', async () => {
    const onUpdateSelected = jest.fn()
    const multiSelect = render({
        template: `
          <multi-select multiple :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                        @update:selected="onUpdateSelected" value="2"></multi-select>`,
        components: { MultiSelect },
        methods: { onUpdateSelected }
      },
    )

    // onUpdateSelected should never be called
    return expect(waitFor(() => {
      expect(onUpdateSelected).toHaveBeenCalled()
    })).rejects.toThrow(/toHaveBeenCalled/)
  })

  it('should emit an update:selected event when an option is selected', async () => {
    const onUpdateSelected = jest.fn()
    const multiSelect = render({
        template: `
          <multi-select multiple :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                        @update:selected="onUpdateSelected"></multi-select>`,
        components: { MultiSelect },
        methods: { onUpdateSelected }
      },
    )

    userEvent.click(screen.getByRole('combobox'))

    await waitFor(() => {
      expect(screen.getByText('Foo')).toBeVisible()
      expect(screen.getByText('Bar')).toBeVisible()
    })

    userEvent.click(screen.getByText('Foo'))

    await waitFor(() => {
      expect(onUpdateSelected).toHaveBeenCalledWith([{"id": 1, "label": "Foo"}], null)
    })
  })

  describe('groups', () => {
    it('should select and display the options from the selected group', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}, {id: 3, label: 'Baz'}],
          groups: { Group1: '2,1' }
        },
      })

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByRole('combobox')).toHaveVisibleTextContent('Foo Bar Baz Group1')
      })

      userEvent.click(screen.getByText('Group1'))
      await waitFor(() => {
        expect(screen.getByText('Bar', { selector: '.multiselect__tag span' })).toBeInTheDocument()
        expect(screen.getByText('Foo', { selector: '.multiselect__tag span' })).toBeInTheDocument()
      })

      expect(screen.getByRole('combobox')).toHaveVisibleTextContent('Bar Foo Foo Bar Baz Group1')

      userEvent.click(screen.getByText('', { selector: '.multiselect__select' }))

      await waitFor(() => {
        expect(multiSelect.container).not.toHaveVisibleTextContent('Baz')
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Foo')
      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    it('should submit a form only after the group values have been auto-selected', async () => {
      const formSubmitted = jest.fn()
      render({
          template: `
          <div>
            <form id="myform" @submit.prevent="formSubmitted">
              <multi-select multiple :options="[{id: 1, label: \'Foo\'}, {id: 2, label: \'Bar\'}]"
                            :groups="{ Group1: '2,1' }" name="multiselect" submit-on-input="myform"></multi-select>
            </form>
          </div>`,
          components: {MultiSelect},
          methods: {
            formSubmitted () {
              formSubmitted(event.target.elements.multiselect.value)
            }
          }
        },
      )

      userEvent.click(screen.getByRole('combobox'))

      await waitFor(() => {
        expect(screen.getByText('Foo')).toBeVisible()
        expect(screen.getByText('Bar')).toBeVisible()
      })

      userEvent.click(screen.getByText('Group1'))

      await waitFor(() => {
        expect(formSubmitted).toHaveBeenCalledWith('2,1')
      })
    })
  })
})
