import React from 'react';
import { Text, View } from '@react-pdf/renderer';
import styles from './styles.js';

function EvaluationGridRowControl({rowTemplate, row, t}) {
    if (rowTemplate?.control_type === 'slider') {
        return <View style={styles.evaluationGridRowScale}>
            <View style={styles.evaluationGridRowScaleAxisSquish}>
                <View style={styles.evaluationGridRowScaleAxis} />
                <View style={styles.evaluationGridRowScaleTicks}>
                    {[...Array(9)].map((e, i) => <View key={i} style={styles.evaluationGridRowScaleTick} />)}
                </View>
                { row ? <View style={{ width: '8pt', height: '8mm', textAlign: 'center', position: 'absolute', top: '-2mm', left: (parseInt(row.value) + 1) + '0%' }}><Text>❌</Text></View> : <View /> }
            </View>
            <View style={styles.evaluationGridRowScaleLabels}>
                {['--', '-', '+', '++'].map((e, i) => <Text key={i} style={styles.evaluationGridRowScaleLabel}>{e}</Text>)}
            </View>
        </View>
    }
    if (rowTemplate?.control_type === 'radiobuttons') {
        return <View>
            <Text>radiobuttons</Text>
        </View>
    }
    if (rowTemplate?.control_type === 'checkbox') {
        return <View>
            <Text>checkbox</Text>
        </View>
    }
    return <View/>
}

export default EvaluationGridRowControl
