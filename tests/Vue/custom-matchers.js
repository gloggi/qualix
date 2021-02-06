import {checkHtmlElement, getMessage, matches, normalize} from '@testing-library/jest-dom/dist/utils'

function isStyleVisible(element) {
  const {getComputedStyle} = element.ownerDocument.defaultView

  const {display, visibility, opacity} = getComputedStyle(element)
  return (
    display !== 'none' &&
    visibility !== 'hidden' &&
    visibility !== 'collapse' &&
    opacity !== '0' &&
    opacity !== 0
  )
}

function isAttributeVisible(element, previousElement) {
  return (
    !element.hasAttribute('hidden') &&
    (element.nodeName === 'DETAILS' && previousElement.nodeName !== 'SUMMARY'
      ? element.hasAttribute('open')
      : true)
  )
}

function isElementVisible(element, previousElement) {
  return (
    isStyleVisible(element) &&
    isAttributeVisible(element, previousElement) &&
    (!element.parentElement || isElementVisible(element.parentElement, element))
  )
}

function getVisibleTextContent(htmlElement) {
  let result = ''
  const nodeType = htmlElement.nodeType

  if (nodeType === 3) return htmlElement.nodeValue
  if (nodeType !== 1 && nodeType !== 9 && nodeType !== 11) return ''
  if (!isElementVisible(htmlElement)) return ''

  if (nodeType === 1 || nodeType === 9 || nodeType === 11) {
    htmlElement.childNodes.forEach(child => {
      result += ' ' + getVisibleTextContent(child)
    })
  }

  return result.trim();
}

function toHaveVisibleTextContent(
  htmlElement,
  checkWith = true,
  options = {normalizeWhitespace: true},
) {
  checkHtmlElement(htmlElement, toHaveVisibleTextContent, this)

  const textContent = options.normalizeWhitespace
    ? normalize(getVisibleTextContent(htmlElement))
    : getVisibleTextContent(htmlElement).replace(/\u00a0/g, ' ') // Replace &nbsp; with normal spaces

  if (checkWith === true) {
    return {
      pass: Boolean(textContent),
      message: () => {
        const to = this.isNot ? 'not to' : 'to'
        return getMessage(
          this,
          this.utils.matcherHint(
            `${this.isNot ? '.not' : ''}.toHaveVisibleTextContent`,
            'element',
            '',
          ),
          `Expected element ${to} have any visible text content`,
          undefined,
          'Received',
          textContent,
        )
      },
    }
  }

  const checkingWithEmptyString = textContent !== '' && checkWith === ''
  return {
    pass: !checkingWithEmptyString && matches(textContent, checkWith),
    message: () => {
      const to = this.isNot ? 'not to' : 'to'
      return getMessage(
        this,
        this.utils.matcherHint(
          `${this.isNot ? '.not' : ''}.toHaveVisibleTextContent`,
          'element',
          '',
        ),
        checkingWithEmptyString
          ? `Checking with empty string will always match, use the matcher with no argument instead`
          : `Expected element ${to} have visible text content`,
        checkWith,
        'Received',
        textContent,
      )
    },
  }
}

expect.extend({ toHaveVisibleTextContent })
