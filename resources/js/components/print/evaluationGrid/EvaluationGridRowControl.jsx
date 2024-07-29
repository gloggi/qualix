import React from 'react';
import { Text, View } from '@react-pdf/renderer';
import styles from './styles.js';
import EvaluationGridCheckbox from './EvaluationGridCheckbox.jsx';

function EvaluationGridRowControl({rowTemplate, row, t}) {
    if (rowTemplate?.control_type === 'slider') {
        return <View style={styles.evaluationGridRowScale} wrap={false}>
            <View style={styles.evaluationGridRowScaleAxisSquish}>
                <View style={styles.evaluationGridRowScaleAxis} />
                <View style={styles.evaluationGridRowScaleTicks}>
                    {[...Array(9)].map((e, i) => <View key={i} style={styles.evaluationGridRowScaleTick} />)}
                </View>
                { row && (row.value === null) ? <View /> :
                    <View style={{ position: 'absolute', margin: '0 7pt 0 6pt', left: '-5pt', right: '5pt', top: '-6pt', height: '7mm' }}>
                        <View style={{ position: 'absolute', left: (Math.round(parseInt(row.value) * 1000 / 9) / 10) + '%', width: '11pt', height: '100%', textAlign: 'center' }}>
                            <Text>❌</Text>
                        </View>
                    </View>
                }
            </View>
            <View style={styles.evaluationGridRowScaleLabels}>
                {['--', '-', '+', '++'].map((e, i) => <Text key={i} style={styles.evaluationGridRowScaleLabel}>{e}</Text>)}
            </View>
        </View>
    }
    if (rowTemplate?.control_type === 'radiobuttons') {
        return <View style={styles.evaluationGridRowRadiobuttons} wrap={false}>
            <View style={styles.evaluationGridRowRadiobuttonsOption}>
                <EvaluationGridCheckbox circular={true} checked={row?.value === '0'} />
                <Text style={{ width: '100%', textAlign: 'center' }}>-</Text>
            </View>
            <View style={styles.evaluationGridRowRadiobuttonsOption}>
                <EvaluationGridCheckbox circular={true} checked={row?.value === '6'} />
                <Text style={{ width: '100%', textAlign: 'center' }}>+</Text>
            </View>
            <View style={styles.evaluationGridRowRadiobuttonsOption}>
                <EvaluationGridCheckbox circular={true} checked={row?.value === '9'} />
                <View style={{ width: '100%', textAlign: 'center' }}><Text>{t('t.models.evaluation_grid_row.radio_buttons.expectations_surpassed')}</Text></View>
            </View>
        </View>
    }
    if (rowTemplate?.control_type === 'checkbox') {
        return <View style={styles.evaluationGridRowCheckboxControl} wrap={false}>
            <EvaluationGridCheckbox checked={row?.value === '7'} />
        </View>
    }
    return <View/>
}

export default EvaluationGridRowControl
