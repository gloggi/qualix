import { render, screen, waitFor } from '@testing-library/vue'
import QualiEditor from '../../../resources/js/components/quali/QualiEditor'
import BootstrapVue from 'bootstrap-vue'
import Vue from "vue"
import VueI18n from "vue-i18n"

describe('quali editor', () => {
  describe('main display and form value', () => {
    it('displays an empty document when no value is specified', () => {
      const qualiEditor = render(QualiEditor, {
        props: {
          requirements: [],
          role: 'textbox',
        },
        mocks: { '$t': () => {} }
      }, vue => { vue.use(BootstrapVue) })

      // No visible text should ever appear
      return expect(waitFor(() => {
        expect(screen.getByRole('textbox')).toHaveVisibleTextContent()
      })).rejects.toThrow(/Expected element to have any visible text content/)
    })

    it('displays the specified text value', async () => {
      const qualiEditor = render(QualiEditor, {
        props: {
          requirements: [],
          role: 'textbox',
          value: {
            type: 'doc',
            content: [{
              type: 'paragraph',
              content: [{
                type: 'text',
                text: 'Test text content',
              }]
            }],
          },
        },
        mocks: { '$t': () => {} }
      }, vue => { vue.use(BootstrapVue) })

      await waitFor(() => {
        expect(screen.getByRole('textbox')).toHaveVisibleTextContent('Test text content')
      })
    })

    it.only('displays the specified requirement', async () => {
      const qualiEditor = render(QualiEditor, {
        props: {
          requirements: [{
            content: 'Test requirement',
            id: 1,
          }],
          role: 'textbox',
          value: {
            type: 'doc',
            content: [{
              type: 'requirement',
              attrs: {
                id: 1,
                passed: null,
              },
            }],
          },
        },
      }, vue => {
        vue.use(BootstrapVue)
        vue.use(VueI18n)
        return {
          i18n: new VueI18n({ locale: 'de', fallbackLocale: 'en', messages: {} })
        }
      })

      await waitFor(() => {
        expect(screen.getByRole('textbox')).toHaveVisibleTextContent('Test requirement')
      })
    })
  })

  describe('autofocus', () => {
    it('should be ready to type when autofocus is set', async () => {
      render(QualiEditor, {
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

    it('should not be ready to type automatically when autofocus is not set', () => {
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
})
