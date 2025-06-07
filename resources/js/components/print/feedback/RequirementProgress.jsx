import React from 'react'
import {View, Text} from '@react-pdf/renderer'
import {groupBy} from 'lodash'
import colors from './colors.js'

const styles = {
    outer: {
        border: '1pt solid black',
        borderRadius: '2pt',
        lineHeight: 0.8,
        width: '100%',
        display: 'flex',
        flexDirection: 'row',
        marginBottom: '5mm',
    },
    bar: {
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
    }
}

function RequirementProgress({ requirements, statuses }) {
    const groupedRequirements = groupBy(requirements, 'status_id')
    const countedStatuses = Object.entries(groupedRequirements).map(([statusId, requirements]) => {
        const status = statuses.find(status => status.id === parseInt(statusId)) || {}
        return { ...status, count: requirements.length }
    })
    return (
        <View style={styles.outer}>
            {countedStatuses.map(status => <View key={status.id} style={{
                ...styles.bar,
                flexBasis: status.count * 999999,
                backgroundColor: colors[status.color].color,
                color: colors[status.color].contrastColor
            }}>
                <Text>{ status.count } { status.name }</Text>
            </View>)}
        </View>
    )
}

export default RequirementProgress
