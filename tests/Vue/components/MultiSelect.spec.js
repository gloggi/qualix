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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: {
          '$t': () => {
          }
        }
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
        mocks: {
          '$t': () => {
          }
        }
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
        mocks: {
          '$t': () => {
          }
        }
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
        mocks: {
          '$t': () => {
          }
        }
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
      mocks: {
        '$t': () => {
        }
      }
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
        mocks: { '$t': () => {} }
      })

      expect(multiSelect.container).not.toHaveVisibleTextContent()
    })

    it('should display the placeholder initially', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          placeholder: 'Select one',
        },
        mocks: { '$t': () => {} }
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Select one')
    })

    it('should display the initial value', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          value: '2'
        },
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    it('should display the selected option', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
        mocks: { '$t': () => {} }
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
          mocks: { '$t': () => {} }
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
          mocks: { '$t': () => {} }
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
          mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is false and no option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': false,
        },
        mocks: { '$t': () => {} }
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is true and no option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          'show-clear': true,
        },
        mocks: { '$t': () => {} }
      })

      expect(document.querySelector('.multiselect__clear')).not.toBeInTheDocument()
    })

    it('should not display the clear button when show-clear is unset and an option is selected', async () => {
      render(MultiSelect, {
        props: {
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
          value: '2',
        },
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
      })

      userEvent.click(document.querySelector('.multiselect__clear'))

      await waitFor(() => {
        expect(multiSelect.container).not.toHaveVisibleTextContent()
        expect(screen.getByTestId('formValue')).toHaveValue('')
      })
    })
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: {
          '$t': () => {
          }
        }
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
        mocks: {
          '$t': () => {
          }
        }
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
        mocks: {
          '$t': () => {
          }
        }
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
        mocks: {
          '$t': () => {
          }
        }
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
      mocks: {
        '$t': () => {
        }
      }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
      })

      expect(multiSelect.container).toHaveVisibleTextContent('Bar')
    })

    it('should display the selected option', async () => {
      const multiSelect = render(MultiSelect, {
        props: {
          multiple: true,
          options: [{id: 1, label: 'Foo'}, {id: 2, label: 'Bar'}],
        },
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
          mocks: {
            '$t': () => {
            }
          }
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
          mocks: {
            '$t': () => {
            }
          }
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
          mocks: {
            '$t': () => {
            }
          }
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
          mocks: {
            '$t': () => {
            }
          }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
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
        mocks: { '$t': () => {} }
      })

      userEvent.click(document.querySelector('.multiselect__clear'))

      await waitFor(() => {
        expect(multiSelect.container).not.toHaveVisibleTextContent()
        expect(screen.getByTestId('formValue')).toHaveValue('')
      })
    })
  })
})
