import React from 'react'
import {Text, View} from '@react-pdf/renderer'
import styles from './styles.js'
import EvaluationGridRowControl from './EvaluationGridRowControl.jsx';

function fixNewlines(text) {
    // Remove \r characters, because they are rendered as crossed-out box into the PDF.
    // This happens only in multiline observations for now, because the rest of the text
    // is divided into paragraphs and should never contain newlines.
    return text.replace(/(\r\n|\r|\n)/g, '\n')
}

function EvaluationGridRow({rowTemplate, rows, t}) {
    if (!rowTemplate) return <View/>
    const row = rows ? rows.find(row => row.evaluation_grid_row_template_id === rowTemplate.id) : null

    if (rowTemplate.control_type === 'heading') {
        return <View style={{ ...styles.evaluationGridRow, fontWeight: '700', padding: '2mm 1.5mm 1mm' }} minPresenceAhead={175}><Text>{ fixNewlines(rowTemplate.criterion) }</Text></View>
    }

    return <View style={styles.evaluationGridRow} wrap={false}>
        <View style={styles.evaluationGridRowCriterion}><Text>{fixNewlines(rowTemplate.criterion)}</Text></View>
        <View style={styles.evaluationGridRowValue}>
            <EvaluationGridRowControl rowTemplate={rowTemplate} row={row} t={t} />
        </View>
        <View style={styles.evaluationGridRowNotes}><Text>{fixNewlines(row?.notes || '')}</Text></View>
    </View>
}

export default EvaluationGridRow
