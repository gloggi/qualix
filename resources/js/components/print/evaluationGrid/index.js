import React from 'react'
import createI18n from '../i18n.js'
import evaluationGridDocument from './EvaluationGridDocument.jsx'
import { pdf } from '@react-pdf/renderer'

const renderPdf = async (data, language = 'de') => {
  const result = {
    filename: null,
    blob: null,
    error: null,
  }

  try {
    const { translate } = createI18n(language)

    if (typeof evaluationGridDocument.prepare === 'function') {
      await evaluationGridDocument.prepare()
    }

    result.blob = await pdf(React.createElement(evaluationGridDocument, { ...data, t: translate })).toBlob()
  } catch (error) {
    result.error = error
  }

  return result
}

export default renderPdf
