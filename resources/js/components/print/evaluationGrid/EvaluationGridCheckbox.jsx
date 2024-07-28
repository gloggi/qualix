import React from 'react';
import { Text, View } from '@react-pdf/renderer';
import styles from './styles.js';

function EvaluationGridCheckbox({circular, checked}) {
    return <View style={{ ...styles.evaluationGridRowCheckbox, ...(circular ? { borderRadius: '4mm' } : { borderRadius: '3pt'}) }}>
        { checked ? <View style={{ width: '8pt', height: '8mm', textAlign: 'center', position: 'absolute', top: '-0.5pt', left: '4pt' }}><Text>❌</Text></View> : <View /> }
    </View>
}

export default EvaluationGridCheckbox
