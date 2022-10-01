import languageBundle
  from '@kirschbaum-development/laravel-translations-loader!@kirschbaum-development/laravel-translations-loader'
import React from 'react'
import createI18n from '../i18n.js'
import feedbackDocument from './FeedbackDocument.jsx'
import { pdf } from '@react-pdf/renderer'

const renderPdf = async (data, language = 'de') => {
  const result = {
    filename: null,
    blob: null,
    error: null,
  }

  try {
    const { translate } = createI18n(languageBundle, language)

    if (typeof feedbackDocument.prepare === 'function') {
      await feedbackDocument.prepare()
    }

    result.blob = await pdf(React.createElement(feedbackDocument, { ...data, t: translate })).toBlob()
  } catch (error) {
    result.error = error
  }

  return result
}

export default renderPdf
